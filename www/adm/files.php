<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'files';
unset($result);

$category = (string) $_REQUEST['category'];
if ($category == 'game') $category = 'sce';
$id = (int) $_REQUEST['id'];
$data_id = (int) $_REQUEST['data_id'];
$action = (string) $_REQUEST['action'];
$do = (string) $_REQUEST['do'];
$description = (string) $_REQUEST['description'];
$downloadable = (string) $_REQUEST['downloadable'];
$language = (string) $_REQUEST['language'];
$remoteurl = (string) $_REQUEST['remoteurl'];
$allowed_extensions = ["pdf","txt","doc","docx","zip","rar","mp3","pps","jpg","png","gif","webp"];
$allowed_schemes = [ 'http', 'https', 'ftp', 'ftps' ];

setlocale(LC_CTYPE, "da_DK.UTF-8"); // due to escapeshellarg()

function createThumbnail($filename) {

}

$paths = array(
	"sce" => "scenario",
	"convent" => "convent",
	"conset" => "conset"
);

$thumbpaths = array(
	"sce" => "scenarie",
	"convent" => "convent",
	"conset" => "conset"
);

// Slet fil
if ($action == "changefile" && $do == "Delete") {
	
	$q = "DELETE FROM files WHERE id = '$id'";
	$r = doquery($q);
	$q = "DELETE FROM filedata WHERE files_id = '$id'";
	$r = doquery($q);
	$_SESSION['admin']['info'] = "File data deleted! " . dberror();
	if ($r) {
		chlog($data_id,$category,"Fil slettet");
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ($action == "changefile") { // Ret fil
	$downloadable = ($downloadable?1:0);
	$q = "UPDATE files SET description = '" . dbesc($description) . "', downloadable = '$downloadable', language = '" . dbesc($language) . "' WHERE id = '$id'";
	$r = doquery($q);
	$_SESSION['admin']['info'] = "File data updated! " . dberror();
	if ($r) {
		chlog($data_id,$category,"Fil opdateret");
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
}

// Upload fil
if ( $action == 'uploadfile' &&
     $data_id &&
     $category &&
     $paths[$category]
   ) {
	$basename = $_FILES['file']['name'];
	$upload_path = DOWNLOAD_PATH . $paths[$category] . "/" . $data_id . "/" . $basename;
	$upload_dir = dirname($upload_path);
	if (!is_dir($upload_dir) ) {
		mkdir($upload_dir, 0755);
	}
	if (file_exists($upload_path) ) {
		$_SESSION['admin']['info'] = "Error: A file with this file name already exists";
	} elseif ($_FILES['file']['error']) {
		$_SESSION['admin']['info'] = "Error during upload. Error code: " . $_FILES['file']['error'];
	} elseif (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path) ) {
		$_SESSION['admin']['info'] = "The file has been uploaded.";
		chlog($data_id,$category,"Fil uploadet: " . $basename);
	} else {
		$_SESSION['admin']['info'] = "Unknown error when uploading.";
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ( $action == 'uploadremotefile' &&
    	$data_id &&
	$category &&
	$remoteurl &&
 	$paths[$category]
   ) {
	$urldata = parse_url($remoteurl);
	$basename = basename($urldata['path']);
	if (!$basename) {
		$basename = "scenarie_" . $data_id . ".pdf";
	}
	$upload_path = DOWNLOAD_PATH . $paths[$category] . "/" . $data_id . "/" . $basename;
	$upload_dir = dirname($upload_path);
	$pathinfo = pathinfo($upload_path);
	if (!is_dir($upload_dir) ) {
		mkdir($upload_dir, 0755);
	}
	if (!in_array($urldata['scheme'], $allowed_schemes ) ) {
		$_SESSION['admin']['info'] = "Error: Not a valid URL";
	} elseif (!in_array($pathinfo['extension'], $allowed_extensions) ) {
		$_SESSION['admin']['info'] = "Error: Not a valid file type (" . (!is_null($pathinfo['extension']) ? "." . htmlspecialchars($pathinfo['extension']) : "blank" ) . ")";
	} elseif (file_exists($upload_path) ) {
		$_SESSION['admin']['info'] = "Error: A file with this file name already exists";
	} elseif (!copy($remoteurl, $upload_path) ) {
		$_SESSION['admin']['info'] = "Unknown error when uploading.";
	} else {
		$_SESSION['admin']['info'] = "The file has been uploaded.";
		chlog($data_id,$category,"Fil remote-uploadet: " . $remoteurl);
		$remoteurl = "";
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id, 'remoteurl' => $remoteurl] );
}

// Tilføj fil
$path = $_REQUEST['path'];
$description = $_REQUEST['description'];
$downloadable = $_REQUEST['downloadable'];

if ($action == "addfile") {
	$path = trim($path);
	if (substr($path,0,1) != "/") {
		$subdir = $paths[$category];
		$path = DOWNLOAD_PATH . $subdir . "/" . $data_id . "/" . $path;
	}
	$filename = basename($path);
	$description = trim($description);
	$downloadable = ($downloadable?1:0);
	$extension = strtolower(substr(strrchr($path, "."), 1));
	if (!file_exists($path)) {
		$_SESSION['admin']['info'] = "Error: The files does not exist: $path";
	} elseif (!in_array($extension, $allowed_extensions)) {
		$_SESSION['admin']['info'] = "Error: Not a vaild file type: $path";
	} else {
/*
		if ($extension == "pdf") {
			$command = "pdftotext ".escapeshellarg($path)." -";
			$content = `$command`;
		} elseif ($extension == "doc") {
			$command = "antiword ".escapeshellarg($path);
			$content = `$command`;
		} elseif ($extension == "txt") {
			$content = file_get_contents($path);
		} else {
			$content = "";
		}
//		$content = utf8_encode($content); // all output is assumed iso-8859-1 for the time being
		$pages = explode("\x0c",$content);
		if ($pages) {
			mysql_query("INSERT INTO files (data_id, category, filename, description, downloadable, inserted) VALUES ('$data_id','$category','" . dbesc($filename) . "','" . dbesc($description) ."','$downloadable', NOW() )");
			print dberror();
			$fileid = mysql_insert_id();
			chlog($data_id,$category,"Fil oprettet");
		}
		$numpages = 0;
		foreach($pages AS $page => $text) {
			if ($text) {
				$numpages++;
				$sql = "INSERT INTO filedata (files_id, label, content) VALUES ('$fileid','Side ".($page+1)."','".dbesc($text)."')";
				print dberror();
				mysql_query($sql);
			}
		}
		if ($pages) {
			$_SESSION['admin']['info'] = "Fil oprettet! ($numpages sider) " . dberror();
		}
*/
		doquery("INSERT INTO files (data_id, category, filename, description, downloadable, language, inserted) VALUES ('$data_id','$category','" . dbesc($filename) . "','" . dbesc($description) ."','$downloadable','" . dbesc($language) . "', NOW() )");
		$_SESSION['admin']['info'] = "The file has been created." . dberror();
	}
	rexit($this_type, [ 'category' => $category, 'data_id' => $data_id] );
} elseif ($action == "thumbnail") {
	$path = $basename =  basename( $_REQUEST['filename'] );
	$subdir = $paths[$category];
	$target_subdir = $thumbpaths[$category];

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
		if (!in_array($extension, $valid_extensions) ) {
			$_SESSION['admin']['info'] = "Error: Can't recognize file type as image.";
		} else {
			if (class_exists("imagick") ) { // use imagemagick module if present
				$image = new imagick($file);
				if (!$image) {
					$_SESSION['admin']['info'] = "Error: Can't recognize file as image.";
				} else {
					$image->setImageFormat('jpg');
					$image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
					$image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN); // flatten transparency to background, which is white per default
					if ($image->writeImage($target) ) {
						$_SESSION['admin']['info'] = "Thumbnail created";
						chlog($data_id,$category,"Thumbnail oprettet: " . $basename);
						if (file_exists( $target_mini ) ) {
							unlink( $target_mini );
						}
					} else {
						$_SESSION['admin']['info'] = "Error: Could not save thumbnail.";
					}
				}
			} else {
				$command = "convert 2>&1 " . escapeshellarg($file) . " -flatten " . escapeshellarg($target); 
				$content = `$command`;
				chlog($data_id,$category,"Thumbnail oprettet: " . $basename);
				$info = "Thumbnail created.";
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
	switch($category) {
		case 'convent': $folder = "convent"; break;
		case 'aut': $folder = "person"; break;
		case 'sce': $folder = "scenarie"; break;
		case 'sys': $folder = "system"; break;
		default: $folder = FALSE;
	}
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
		chlog($data_id,$category,"Thumbnail slettet");
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
	default:
		$cat = 'aut';
		$q = "SELECT CONCAT(firstname,' ',surname) AS name FROM aut WHERE id = '$data_id'";
		$mainlink = "person.php?person=$data_id";
	}
	$title = getone($q);
	
	$query = "SELECT id, filename, description, downloadable, language FROM files WHERE data_id = '$data_id' AND category = '$category' ORDER BY id";
	$result = getall($query);
}

?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - files</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">

<script type="text/javascript">
function filenameToDescription(filename) {
	description = filename.charAt(0).toUpperCase() + filename.slice(1);
	description = description.substr(0,description.lastIndexOf('.'));
	return description;
}

</script>

</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc.php");

printinfo();

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
	      "<th>Download</th>".
	      "</tr>\n";

        foreach($result AS $row) {
		$selected = ($row['downloadable'] == 1 ? 'checked="checked"' : '');
		print '<form action="files.php" method="post">'.
		      '<input type="hidden" name="action" value="changefile">'.
		      '<input type="hidden" name="data_id" value="'.$data_id.'">'.
		      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
		      '<input type="hidden" name="id" value="'.$row['id'].'">';
		print "<tr>\n".
		      '<td style="text-align:right;">'.$row['id'].'</td>'.
		      '<td >'.$row['filename'].'</td>'.
		      '<td ><input type="text" name="description" value="'.htmlspecialchars($row['description']).'" size="40"></td>'.
	      	'<td style="text-align: center"><input type="checkbox" name="downloadable" '.$selected.'></td>'.
		      '<td ><input type="text" name="language" value="'.htmlspecialchars($row['language']).'" size="2" maxlength="20" placeholder="da"></td>'.
		      '<td><input type="submit" name="do" value="Edit"> <input type="submit" name="do" value="Delete"></td>'.
		      '<td ><a href="http://download.alexandria.dk/files/'.$paths[$category].'/'.$data_id.'/'.rawurlencode($row['filename']).'" title="Download file">💾</a></td>'.
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

	foreach(glob( DOWNLOAD_PATH . $paths[$category] . "/" . $data_id . "/*") AS $file) {
		print '<a href="http://download.alexandria.dk/files/' . $paths[$category] . '/' . $data_id . '/' . rawurlencode(basename($file)) . '" title="Download file">💾</a>&nbsp;';
		print "<a href=\"files.php?category=" . htmlspecialchars($category) . "&amp;data_id=" . $data_id . "&amp;action=thumbnail&amp;filename=" . rawurlencode(basename($file)) . "\" title=\"Create thumbnail\" onclick=\"return confirm('Create thumbnail?');\" >📷</a>&nbsp;";
		print "<a href=\"#\" onclick=\"document.getElementById('newpath').value=this.innerHTML; document.getElementById('newdescription').value=filenameToDescription(this.innerHTML);\">";
		print basename($file);
		print "</a>";
		print "<br />\n";
	}
	print "</td><td>";
	$descriptions = ['{$_sce_file_scenario}' => "Scenario", '{$_sce_file_characters}' => "Characters", "Handouts" => "Handouts", '{$_sce_file_rules}' => "Regler", '{$_sce_file_programme}' => "Programme" ];
	foreach( $descriptions AS $templatecode => $label ) {
		print "<a href=\"#\" onclick=\"document.getElementById('newdescription').value=this.title;\" title=\"" . htmlspecialchars( $templatecode ) . "\">";
		print htmlspecialchars( $label );
		print "</a><br />\n";
	}

	print "</tr>\n";

	print "</table>\n";

	// upload file
	print '<form action="files.php" method="post" enctype="multipart/form-data">' .
	      '<input type="hidden" name="action" value="uploadfile">' . 
	      '<input type="hidden" name="' . ini_get("session.upload_progress.name") . '" value="file" /> '.
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
	      '<input type="hidden" name="data_id" value="' . $data_id . '">' . 
	      '<p>Upload: <input type="file" name="file" />' .
	      '<input type="submit" value="Upload" />' .
	      '</p>' .
	      '</form>'
	      ;

	print '<form action="files.php" method="post">' .
	      '<input type="hidden" name="action" value="uploadremotefile">' . 
	      '<input type="hidden" name="category" value="'.htmlspecialchars($category).'">'.
	      '<input type="hidden" name="data_id" value="' . $data_id . '">' . 
	      '<p>Remote upload from URL: <input type="text" name="remoteurl" size="60" placeholder="http://www.example.com/blog/scenario.pdf" value="' . htmlspecialchars($remoteurl) . '" />' .
	      '<input type="submit" value="Upload" />' .
	      '</p>' .
	      '</form>'
	;

	if (($path = getthumbnailpath($data_id, $category)) !== FALSE) {
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
