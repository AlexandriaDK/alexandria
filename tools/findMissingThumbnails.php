<?php
// Find scenarios that have "Thumbnail added" in the log but doesn't have a thumbnail file even though the corresponding file exists.
// This helps finding missing thumbnails from the server crash in 2020.
header("Content-Type: text/plain");
require __DIR__ . "/../www/rpgconnect.inc.php";
require __DIR__ . "/../www/base.inc.php";
chdir(__DIR__ . "/../www/");


$logs = getall("SELECT DISTINCT category, data_id from log WHERE note LIKE 'Thumbnail created%' ORDER BY category, data_id");

define("DOWNLOAD_PATH", "../loot.alexandria.dk/files/");

if (! function_exists('mb_basename') ) { 
	function mb_basename ( $path ) {
		return array_reverse(explode("/",$path))[0];
	}
}

function chlog($data_id, $category, $note="") { // should be a separate function
	$user = 'Peter Brodersen';
	$authuserid = 4;
	$data_id = ($data_id == NULL ? 'NULL' : (int) $data_id);
	$note = dbesc($note);
	$query = "INSERT INTO log (data_id,category,time,user,user_id,note) " .
	         "VALUES ($data_id,'$category',NOW(),'$user','$authuserid','$note')";
	$result = doquery($query);
	return $result;
}

function createThumbnail($category, $data_id, $filename) { // from www/adm/files.php - should be a separate function

    $path = $basename =  mb_basename( $filename );
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
				$image = new imagick($file);
				if (!$image) {
					$_SESSION['admin']['info'] = "Error: Can't recognize file as image.";
				} else {
					$image->setImageFormat('jpg');
					$image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
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
				$command = "convert 2>&1 " . escapeshellarg($file) . " -flatten " . escapeshellarg($target); 
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
}

foreach($logs AS $log) {
    $id = $log['data_id'];
    $category = $log['category'];
    if ($id == 6134 && $category == 'sce') {
        continue;
    }
    if ( ! ($label = getentry($category, $id) ) || $label == ' (?)') { // does not exist anymore; continue
        continue;
    }

    if ( hasthumbnailpic($id, $category) ) {
        continue;
    }
    // SQL lookups might be redundant; could be optimized, but that's not important
    $latestthumb = getrow("SELECT id, note FROM log WHERE data_id = $id AND category = '$category' AND note LIKE 'Thumbnail created%' ORDER BY id DESC LIMIT 1");  
    $latestthumbremoved_id = getone("SELECT id FROM log WHERE data_id = $id AND category = '$category' AND note = 'Thumbnail deleted' ORDER BY id DESC LIMIT 1"); 
    if ($latestthumb['id'] < $latestthumbremoved_id) {
        continue;
    }
    print "Checking $label, " . $log['category'] . " id " . $log['data_id'] . ": ";
    preg_match('/^Thumbnail created: (.*)$/', $latestthumb['note'], $match);
    if (! $match[1]) {
        continue;
    }
    print "File: " . $match[1] . PHP_EOL;
    createThumbnail($category, $id, $match[1]);
    flush();
}
?>
