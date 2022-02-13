<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'files';
unset($result);

ini_set("user_agent", "Alexandria.dk Dungeon Looter Agent");

$category = (string) $_REQUEST['category'];
if ($category == 'game') $category = 'sce';
$id = (int) $_REQUEST['id'];
$data_id = (int) $_REQUEST['data_id'];
$action = (string) $_REQUEST['action'];
$section = (string) ($_REQUEST['section'] ?? '');
$do = (string) $_REQUEST['do'];
$ocr = (string) $_REQUEST['ocr'];
$description = trim((string) $_REQUEST['description']);
$downloadable = (string) $_REQUEST['downloadable'];
$language = trim((string) $_REQUEST['language']);
$remoteurl = (string) $_REQUEST['remoteurl'];
$pages = (string) ($_REQUEST['pages'] ?? '');
$allowed_extensions = ["pdf","txt","doc","docx","zip","rar","mp3","pps","jpg","png","gif","webp"];
$allowed_schemes = [ 'http', 'https', 'ftp', 'ftps' ];

setlocale(LC_CTYPE, "da_DK.UTF-8"); // due to ImageMagick escapeshellarg() if imagick is not installed as module

// PHP's basename() removes first letter in file name if it is a highbit (Århus.pdf => rhus.pdf)
// Or, more precise, basename() only works if
//   1. the locale for setlocale does exist (e.g. running "locale-gen da_DK.UTF-8")
//   2. setlocale() is run for LC_ALL (none of the other LC_* works) for that locale
//   3. locale is a language. Setting the language to 'C' will not work.
// E.g.: setlocale(LC_ALL, 'da_DK.UTF-8')
if (! function_exists('mb_basename') ) { 
	function mb_basename ( $path ) {
		return array_reverse(explode("/",$path))[0];
	}
}

function splitpdf($path, $pages, $category, $data_id) { // requires pdftk
	$pages = trim($pages);
	if (! preg_match('/^\d+(-\d+)?( +\d+(-\d+)?)?$/', $pages) ) {
		$_SESSION['admin']['info'] = "Bad page range: $pages";
		return false;
	} else {
		$firstpage = (int) $pages;
	}
	if ( ! is_readable($path)) {
		$_SESSION['admin']['info'] = "Can't read file";
		return false;
	}
	$command = 'pdftk ' . escapeshellarg($path) . ' cat ' . $pages . ' output -';
	$fileparts = pathinfo($path);
	if ($category == 'issue') {
		$newname = getone("
			SELECT s.title
			FROM issue i
			INNER JOIN article a ON i.id = a.issue_id  
			INNER JOIN sce s ON a.sce_id = s.id
			WHERE i.id = $data_id
			AND a.page = $firstpage
		");
	}
	if (! $newname) {
		$newname = $fileparts['filename'] . '_' . $pages;
	}
	$newname .= '.' . $fileparts['extension'];
	$pdfdata = `LANG=en_US.UTF-8 $command`; // remember to add LANG variable, otherwise e.g. 'ø' vil be converted to '??'
	header("Content-Type: application/pdf");
	header('Content-Disposition: attachment; filename="' . rawurlencode($newname) . '"');
	header("Content-Length: " . strlen($pdfdata) );
	print $pdfdata;
	exit;
}

// Split PDF to new file (cut pages, etc.)
if ($action == "splitpdf") {
	$filename = mb_basename( $_REQUEST['filename'] );
	$subdir = getcategorydir($category);
	$path = DOWNLOAD_PATH . $subdir . "/" . $data_id . "/" .$filename;
	splitpdf($path, $pages, $category, $data_id);
}

// Remove file from database
if ($action == "changefile" && $do == "Remove") {
	
	$q = "DELETE FROM filedata WHERE files_id = '$id'";
	$r = doquery($q);
	$q = "DELETE FROM files WHERE id = '$id'";
	$r = doquery($q);
	$_SESSION['admin']['info'] = "File data deleted! " . dberror();
	if ($r) {
		chlog($data_id,$category,"File removed");
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ($action == "changefile" && $ocr) { // Mark file to be OCR'ed
	$q = "UPDATE files SET indexed = 11 WHERE id = '$id' AND indexed IN(0,1)";
	$r = doquery($q);
	$_SESSION['admin']['info'] = "File in OCR queue! " . dberror();
	if ($r) {
		chlog($data_id,$category,"File in OCR queue");
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ($action == "changefile") { // Edit file data
	$downloadable = ($downloadable?1:0);
	$q = "UPDATE files SET description = '" . dbesc($description) . "', downloadable = '$downloadable', language = '" . dbesc($language) . "' WHERE id = '$id'";
	$r = doquery($q);
	$_SESSION['admin']['info'] = "File data updated! " . dberror();
	if ($r) {
		chlog($data_id,$category,"File updated");
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
}

// Upload file
if ( $action == 'uploadfile' &&
     $data_id &&
	 $category &&
	 getcategorydir($category)
   ) {
	$basename = $_FILES['file']['name'];
	$upload_path = DOWNLOAD_PATH . getcategorydir($category) . "/" . $data_id . "/" . $basename;
	$upload_dir = dirname($upload_path);
	if (!is_dir($upload_dir) ) {
		mkdir($upload_dir, 0775);
	}
	if (file_exists($upload_path) ) {
		$_SESSION['admin']['info'] = "Error: A file with this file name already exists";
	} elseif ($_FILES['file']['error']) {
		$_SESSION['admin']['info'] = "Error during upload. Error code: " . $_FILES['file']['error'];
	} elseif (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path) ) {
		chmod($upload_path, 0664);
		$_SESSION['admin']['info'] = "The file has been uploaded.";
		chlog($data_id,$category,"File uploaded: " . $basename);
	} else {
		$_SESSION['admin']['info'] = "Error: Can't save uploaded file. Make sure the web server can write to file path: $upload_path";
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ( $action == 'uploadremotefile' &&
	$data_id &&
	$category &&
	$remoteurl &&
	getcategorydir($category)
   ) {
	$urldata = parse_url($remoteurl);
	$basename = urldecode(mb_basename($urldata['path']));
	if (!$basename) {
		$basename = "scenarie_" . $data_id . ".pdf";
	}
	$upload_path = DOWNLOAD_PATH . getcategorydir($category) . "/" . $data_id . "/" . $basename;
	$upload_dir = dirname($upload_path);
	$pathinfo = pathinfo($upload_path);
	if (!is_dir($upload_dir) ) {
		mkdir($upload_dir, 0775);
	}
	if (!in_array($urldata['scheme'], $allowed_schemes ) ) {
		$_SESSION['admin']['info'] = "Error: Not a valid URL";
	} elseif ( ! in_array( strtolower($pathinfo['extension']), $allowed_extensions) ) {
		$_SESSION['admin']['info'] = "Error: Not a valid file type (" . (!is_null($pathinfo['extension']) ? "." . htmlspecialchars($pathinfo['extension']) : "blank" ) . ")";
	} elseif (file_exists($upload_path) ) {
		$_SESSION['admin']['info'] = "Error: A file with this file name already exists";
	} elseif (!copy($remoteurl, $upload_path) ) {
		$_SESSION['admin']['info'] = "Unknown error when transferring file: " . error_get_last()['message'];
	} else {
		chmod($upload_path, 0664);
		$_SESSION['admin']['info'] = "The file has been uploaded.";
		chlog($data_id,$category,"File remote uploaded: " . $remoteurl);
		$remoteurl = "";
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id, 'remoteurl' => $remoteurl] );
}

// Add file
$path = $_REQUEST['path'];
$description = $_REQUEST['description'];
$downloadable = $_REQUEST['downloadable'];

if ($action == "addfile") {
	$path = trim($path);
	if (substr($path,0,1) != "/") {
		$subdir = getcategorydir($category);
		$path = DOWNLOAD_PATH . $subdir . "/" . $data_id . "/" . $path;
	}
	$filename = mb_basename($path);
	$description = trim($description);
	$downloadable = ($downloadable?1:0);
	$extension = strtolower(substr(strrchr($path, "."), 1));
	if (!file_exists($path)) {
		$_SESSION['admin']['info'] = "Error: The files does not exist: $path";
	} elseif (!in_array($extension, $allowed_extensions)) {
		$_SESSION['admin']['info'] = "Error: Not a vaild file type: $path";
	} else {
		doquery("INSERT INTO files (data_id, category, filename, description, downloadable, language, inserted) VALUES ('$data_id','$category','" . dbesc($filename) . "','" . dbesc($description) ."','$downloadable','" . dbesc($language) . "', NOW() )");
		$_SESSION['admin']['info'] = "The file has been created." . dberror();
		chlog($data_id,$category,"File created: " . $filename);
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ($action == "thumbnail") {
	$path = $basename =  mb_basename( $_REQUEST['filename'] );
	$subdir = getcategorydir($category);
	$target_subdir = getcategorythumbdir($category);

	$path = DOWNLOAD_PATH . $subdir . "/" . $data_id . "/" .$path;
	$target = "gfx/" . $target_subdir . "/l_" . $data_id . ".jpg";
	$target_mini = "gfx/" . $target_subdir . "/s_" . $data_id . ".jpg";

	if (!file_exists($path) ) {
		$_SESSION['admin']['info'] = "Error: File for thumbnail does not exist.";
	} else {
		$extension = strtolower(substr(strrchr($path, "."), 1));
		if ($extension == "pdf") {
			$file = $path . "[0]";
		} else {
			$file = $path;
		}
		$valid_extensions = [ "pdf", "jpg", "jpeg", "gif", "png", "webp" ];
		if ( ! in_array( strtolower($extension), $valid_extensions) ) {
			$_SESSION['admin']['info'] = "Error: Can't recognize file type as image.";
		} else {
			if (class_exists("imagick") ) { // use imagemagick module if present
				$image = new imagick();
				$image->setResolution(150,150); // bump resolution up - probably
				if ( ! $image->readImage($file) ) {
					$_SESSION['admin']['info'] = "Error: Can't recognize file as image.";
				} else {
					$image->setImageFormat('jpg');
					$image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
					if ($section == 'left') {
						$image->cropImage(ceil($image->getImageWidth()/2), $image->getImageHeight(), 0, 0);
					} elseif ($section == 'right') {
						$image->cropImage(ceil($image->getImageWidth()/2), $image->getImageHeight(), floor($image->getImageWidth()/2), 0);
					}
					$image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN); // flatten transparency to background, which is white per default
					if ($image->writeImage($target) ) {
						$_SESSION['admin']['info'] = "Thumbnail created";
						chlog($data_id,$category,"Thumbnail created: " . $basename);
						if (file_exists( $target_mini ) ) {
							unlink( $target_mini );
						}
					} else {
						$_SESSION['admin']['info'] = "Error: Could not save thumbnail.";
					}
				}
			} else {
				if ($section == 'left') {
					$command = "convert 2>&1 -density 150 " . escapeshellarg($file) . " -gravity west -crop 50%x100%+0+0 -flatten " . escapeshellarg($target); 
				} elseif ($section == 'right') {
					$command = "convert 2>&1 -density 150 " . escapeshellarg($file) . " -gravity east -crop 50%x100%+0+0 -flatten " . escapeshellarg($target); 
				} else {
					$command = "convert 2>&1 -density 150 " . escapeshellarg($file) . " -flatten " . escapeshellarg($target); 
				}
				$content = `$command`;
				chlog($data_id,$category,"Thumbnail created: " . $basename);
				$info = "Thumbnail created";
				if ($content) {
					$info .= "\nOutput:\n" . $content;
				}
				$_SESSION['admin']['info'] = $info;
				if (file_exists( $target_mini ) ) {
					unlink( $target_mini );
				}
			}
		}
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ($action == 'deletethumbnail') {
	$folder = getcategorythumbdir($category);
	$deleted = FALSE;
	// Ingen ../ idet vi har chdir()'et et trin ud fra adm-mappen
	$path_large = "gfx/$folder/l_".$data_id.".jpg";
	$path_small = "gfx/$folder/s_".$data_id.".jpg";
	if (file_exists($path_large)) {
		unlink($path_large);
		$deleted = TRUE;
	}
	if (file_exists($path_small)) {
		unlink($path_small);
		$deleted = TRUE;
	}
	if ($deleted) {
		$_SESSION['admin']['info'] = "Thumbnail deleted! ";
		chlog($data_id,$category,"Thumbnail deleted");
	} else {
		$_SESSION['admin']['info'] = "Error: Thumbnail could not be deleted!\nPath large: $path_large\nPath small: $path_small ";
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
}

if ($data_id && $category) {
	$data_id = intval($data_id);
	switch($category) {
	case 'aut':
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
		break;
	case 'sce':
		$cat = 'sce';
		$q = "SELECT title FROM sce WHERE id = '$data_id'";
		$mainlink = "game.php?game=$data_id";
		break;
	case 'convent':
		$cat = 'convent';
		$q = "SELECT CONCAT(name, ' (', year, ')') FROM convent WHERE id = '$data_id'";
		$mainlink = "convent.php?con=$data_id";
		break;
	case 'conset':
		$cat = 'conset';
		$q = "SELECT name FROM conset WHERE id = '$data_id'";
		$mainlink = "conset.php?conset=$data_id";
		break;
	case 'sys':
		$cat = 'sys';
		$q = "SELECT name FROM sys WHERE id = '$data_id'";
		$mainlink = "system.php?system=$data_id";
		break;
	case 'tag':
		$cat = 'tag';
		$q = "SELECT tag FROM tag WHERE id = '$data_id'";
		$mainlink = "tag.php?tag_id=$data_id";
		break;
	case 'issue':
		$cat = 'issue';
		$q = "SELECT title FROM issue WHERE id = '$data_id'";
		$mainlink = "magazine.php?issue_id=$data_id";
		break;
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "
		SELECT COUNT(fd.id) AS pages, f.id, f.filename, f.description, f.downloadable, f.language, f.indexed
		FROM files f
		LEFT JOIN filedata fd ON f.id = fd.files_id
		WHERE f.data_id = '$data_id' AND f.category = '$category'
		GROUP BY f.id, f.filename, f.description, f.downloadable, f.language, f.indexed
		ORDER BY f.id
	";
	$result = getall($query);
}

htmladmstart("Files");

if ($data_id && $category) {
	print "<div align=\"center\" style=\"margin: auto; padding: auto;\">\n";

	print "<table align=\"center\" border=\"0\">".
	      "<tr><th colspan=5>Edit files for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n".
	      "<tr>\n".
	      "<th>ID</th>".
	      "<th>Filename</th>".
	      "<th>Description</th>".
	      "<th>Public</th>".
	      "<th>Language code</th>".
	      "<th>Edit</th>".
	      '<th>Download</th>'.
	      '<th style="padding-left: 1em;">Content index status</th>'.
	      "</tr>\n";

	foreach($result AS $row) {
		$selected = ($row['downloadable'] == 1 ? 'checked="checked"' : '');
		$path = DOWNLOAD_PATH . getcategorydir($category) . '/' . $data_id . '/' . $row['filename'];
		$OCRpossible = ($row['pages'] == 0 && in_array($row['indexed'], [1]) && strtolower(pathinfo($path)['extension']) == 'pdf'); // only indexed PDFs with no text content
		if ($OCRpossible) {
			$ocrhtml = 'Indexed; no text in file <input type="submit" name="ocr" value="Queue for OCR" title="Mark file to be OCR\'ed - takes a couple of minutes">';
		} elseif ($row['indexed'] == 0) {
			$ocrhtml = 'Ready to be indexed';
		} elseif ($row['indexed'] == 1) {
			$ocrhtml = 'Indexed';
		} elseif ($row['indexed'] == 2) {
			$ocrhtml = 'Currently being indexing';
		} elseif ($row['indexed'] == 3) {
			$ocrhtml = 'Skipped';
		} elseif ($row['indexed'] == 4) {
			$ocrhtml = 'Error while indexing';
		} elseif ($row['indexed'] == 5) {
			$ocrhtml = 'File was not found when indexing';
		} elseif ($row['indexed'] == 11) {
			$ocrhtml = 'In queue for OCR\'ing';
		} elseif ($row['indexed'] == 12) {
			$ocrhtml = 'Currently OCR\'ing';
		} elseif ($row['indexed'] == 20) {
			$ocrhtml = 'OCR done, in queue for indexing';
		} else {
			$ocrhtml = '';
		}
		print '<form action="files.php" method="post">'.
		      '<input type="hidden" name="action" value="changefile">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td>' .
		      htmlspecialchars( $row['filename'] ) .
		      ( file_exists( $path ) ? '' : ' <span style="color: #c00" title="File does not exist">⚠</span>' ) .
		      ( strpos($path, '#') === FALSE ? '' : ' <span style="color: #c00" title="File contains # character - please rename">⚠</span>' ) .
		      '</td>' .
		      '<td ><input type="text" name="description" value="'.htmlspecialchars($row['description']).'" size="40"></td>'.
	      	'<td style="text-align: center"><input type="checkbox" name="downloadable" '.$selected.'></td>'.
		      '<td ><input type="text" name="language" value="'.htmlspecialchars($row['language']).'" size="2" maxlength="20" placeholder="da"></td>'.
		      '<td><input type="submit" name="do" value="Edit"> <input type="submit" name="do" value="Remove"></td>'.
		      '<td style="text-align: center"><a href="https://download.alexandria.dk/files/'.getcategorydir($category).'/'.$data_id.'/'.rawurlencode($row['filename']).'" title="Download file">💾</a></td>'.
		      '<td style="padding-left: 1em;">' . $ocrhtml . '</td>' .
		      "</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="files.php" method="post">'.
	      '<input type="hidden" name="action" value="addfile">'.
	      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">';
	print "<tr>\n".
	      '<td style="text-align:right;">New</td>'.
	      '<td><input type="text" name="path" id="newpath" value="" size="40" maxlength="150"></td>'.
	      '<td><input type="text" name="description" id="newdescription" value="" size="40" maxlength="150"></td>'.
	      '<td style="text-align: center"><input type="checkbox" name="downloadable" checked="checked"></td>'.
	      '<td ><input type="text" name="language" value="" size="2" maxlength="20" placeholder="da"></td>'.
	      '<td colspan=2><input type="submit" name="do" value="Create"></td>'.
	      '<td></td>'.
	      "</tr>\n";
	print "</form>\n\n";

	print "<tr valign=\"top\"><td></td><td>Available files:</td><td>Default descriptions:</td></tr><tr valign=\"top\"><td></td><td>";

	$thumbshortcut = TRUE;
	foreach(glob( DOWNLOAD_PATH . getcategorydir($category) . "/" . $data_id . "/*") AS $file) {
		$accesskey = ($thumbshortcut ? 'accesskey="t"' : '');
		$thumbshortcut = FALSE;
		$basename = mb_basename( $file );
		$extension = strtolower(pathinfo($basename,  PATHINFO_EXTENSION));
		$createthumbnailurl = 'files.php?category=' . htmlspecialchars($category) . '&amp;data_id=' . $data_id . '&amp;action=thumbnail&amp;filename=' . rawurlencode( $basename );
		$createcuturl = 'files.php?category=' . htmlspecialchars($category) . '&data_id=' . $data_id . '&action=splitpdf&filename=' . rawurlencode( $basename );
		print '<div class="tooltip">';
		print '<a href="https://download.alexandria.dk/files/' . getcategorydir($category) . '/' . $data_id . '/' . rawurlencode( $basename ) . '" title="Download file">💾</a>&nbsp;';
		if ($extension == 'pdf') {
			print '<span class="tooltiptext">Create new PDF<br><br>Pages: <input type="text" name="pdfcutpages" placeholder="1-3 5" style="width: 50px"><br><input type="submit" value="Create" onclick="window.location.href=\'' . $createcuturl . '&pages=\' + encodeURIComponent(this.previousSibling.previousSibling.value)"></span>';
		}
		print '</div>&nbsp;' . PHP_EOL;
		// print '<a href="https://download.alexandria.dk/files/' . getcategorydir($category) . '/' . $data_id . '/' . rawurlencode( $basename ) . '" title="Download file">💾</a>&nbsp;';
		print '<div class="tooltip">';
		print '<a ' . $accesskey . ' href="' . $createthumbnailurl. '" title="Create thumbnail" onclick="return confirm(\'Create thumbnail?\');" >📷</a>';
		print '<span class="tooltiptext"><a href="' . $createthumbnailurl . '" onclick="return confirm(\'Create thumbnail?\');" title="Create thumbnail">Full</a> • <a href="' . $createthumbnailurl . '&amp;section=left" onclick="return confirm(\'Create thumbnail, left half?\');" title="Create thumbnail, use left half of first page">Left</a> • <a href="' . $createthumbnailurl . '&amp;section=right" onclick="return confirm(\'Create thumbnail, right half?\');" title="Create thumbnail, use right half of first page">Right</a></span>';
		print '</div>&nbsp;' . PHP_EOL;
		print "<a href=\"#\" onclick=\"document.getElementById('newpath').value=this.innerHTML; document.getElementById('newdescription').value=filenameToDescription(this.innerHTML);\">";
		print htmlspecialchars( $basename );
		print "</a>";
		print "<br />\n";
	}
	print "</td><td>";
	$descriptions = ['{$_sce_file_scenario}' => "Scenario", '{$_sce_file_characters}' => "Characters", '{$_sce_file_handouts}' => "Handouts", '{$_sce_file_rules}' => "Rules", '{$_sce_file_programme}' => "Programme", '{$_sce_file_pamphlet}' => "Pamphlet" ];
	foreach( $descriptions AS $templatecode => $label ) {
		print '<div class="descriptionexamples">';
		print "<a href=\"#\" onclick=\"document.getElementById('newdescription').value=this.title;\" title=\"" . htmlspecialchars( $templatecode ) . "\">";
		print htmlspecialchars( $label );
		print '</a> <span onclick="navigator.clipboard.writeText(this.innerHTML); $(this).fadeOut(100).fadeIn(100);">' . htmlspecialchars( $templatecode ) . '</span>';
		print '</div>';
	}

	print "</td></tr>\n";

	print "</table>\n\n";

	// upload file
	print '<form action="files.php" method="post" enctype="multipart/form-data">' .
	      '<input type="hidden" name="action" value="uploadfile">' . 
	      '<input type="hidden" name="' . ini_get("session.upload_progress.name") . '" value="file" /> '.
	      '<input type="hidden" name="category" value="' . htmlspecialchars( $category ) . '">'.
	      '<input type="hidden" name="data_id" value="' . $data_id . '">' . 
	      '<p>Upload: <input type="file" name="file" />' .
	      '<input type="submit" value="Upload" />' .
	      '</p>' .
	      '</form>' . PHP_EOL . PHP_EOL
	      ;

	print '<form action="files.php" method="post">' .
	      '<input type="hidden" name="action" value="uploadremotefile">' . 
	      '<input type="hidden" name="category" value="' . htmlspecialchars( $category ) . '">'.
	      '<input type="hidden" name="data_id" value="' . $data_id . '">' . 
	      '<p>Remote upload from URL: <input type="text" name="remoteurl" size="60" placeholder="https://www.example.com/blog/scenario.pdf" value="' . htmlspecialchars($remoteurl) . '" />' .
	      '<input type="submit" value="Upload" />' .
	      '</p>' .
	      '</form>' . PHP_EOL . PHP_EOL
	;

	if ($category == 'sce') { // suggest scenario extract from magazine PDF
		$magazineprint = getrow("
			SELECT i.id, a.page, m.name, i.title, f.filename
			FROM magazine m
			INNER JOIN issue i ON m.id = i.magazine_id
			INNER JOIN article a ON i.id = a.issue_id 
			INNER JOIN files f ON i.id = f.data_id AND f.category = 'issue'
			WHERE a.sce_id = $data_id
		");
		// https://localhost/en/adm/files.php?category=issue&data_id=1&action=splitpdf&filename=no7web.pdf&pages=2-3,6
		if ($magazineprint) {
			$pdfurl = 'https://download.alexandria.dk/files/' . getcategorydir('issue') . "/" . $magazineprint['id'] . "/" . rawurlencode($magazineprint['filename']) . '#page=' . $magazineprint['page'];
			print '<form><input type="hidden" name="category" value="issue"><input type="hidden" name="data_id" value="' . $magazineprint['id'] . '"><input type="hidden" name="action" value="splitpdf"><input type="hidden" name="filename" value="' . htmlspecialchars($magazineprint['filename']) . '"><div>Extract from ' . htmlspecialchars($magazineprint['name']) . ', <a href="' . $pdfurl . '">' . htmlspecialchars($magazineprint['title']) . '</a>:<br>';
			print 'Pages: <input type="text" name="pages" value="' . $magazineprint['page'] . '" placeholder="1-3 5" style="width: 50px"> <input type="submit" value="Extract"></div></form>';
		}
	}

	if ( ($path = getthumbnailpath($data_id, $category)) !== FALSE ) {
		print '<form action="files.php" method="post" onsubmit="return confirm(\'Delete thumbnail?\');">' .
		      '<input type="hidden" name="action" value="deletethumbnail">' . 
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="data_id" value="' . $data_id . '">' . 
		      '<p><a href="../' . $path . '">Thumbnail</a><br>' .
		      '<input type="submit" value="Delete thumbnail" />' .
		      '</p>' .
		      '</form>'
		;
		
	}
	print "\n\n</div>\n";

} else {
	print "Error: No data id supplied.";
}

print "</body>\n</html>\n";

?>
