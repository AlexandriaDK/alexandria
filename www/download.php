<?php
require("./connect.php");
require("base.inc");
require("template.inc");

list($category,$data_id,$filename) = preg_split('_/_',$_SERVER['PATH_INFO'],-1,PREG_SPLIT_NO_EMPTY);
$data_id = intval($data_id);
$fileondisk = ALEXFILES.'/'.$category.'/'.$data_id.'/'.$filename;

if (file_exists($fileondisk) ) {
	if ($category == 'scenario') $category = 'sce';
	$ip = $_SERVER['REMOTE_ADDR'];
	$browser = $_SERVER['HTTP_USER_AGENT'];
	$referer = $_SERVER['HTTP_REFERER'];
	list($file_id) = getrow("SELECT id FROM files WHERE category = '$category' AND data_id = '$data_id'");
	doquery("INSERT INTO filedownloads (files_id, data_id, category, accesstime, ip, browser, referer) VALUES ('$file_id','$data_id','$category',NOW(),INET_ATON('$ip'),'".dbesc($browser)."','".dbesc($referer)."')");
	#header("Location: http://download.alexandria.dk/files".$_SERVER['PATH_INFO']);

	// achievements
	if ($category == 'sce') {
		award_achievement(60); // download a scenario
	}

	if ($category == 'sce' && $_SESSION['user_author_id'] ) {
		$is_author = getone("SELECT 1 FROM asrel WHERE sce_id = '$data_id' AND tit_id IN (1,4) AND aut_id = '" . $_SESSION['user_author_id'] . "'");
		if ($is_author) {
			award_achievement(85); // download own scenario
		}
	}

	// redirect
	header("Location: http://download.alexandria.dk/files".$_SERVER['PATH_INFO']);

} else {
	header("HTTP/1.0 404 Not Found");
	die("The file was not found - please contact <a href=\"mailto:peter@alexandria.dk\">peter@alexandria.dk</a>.");
}


?>
