<?php
$string = substr($_SERVER['REQUEST_URI'],1);

$magic = TRUE;

if (is_dir($_SERVER['DOCUMENT_ROOT']."/$string")) {
	$url = "/$string/";
} elseif (preg_match('/^[a-z]{2}$/', $string) ) { //language redirect
	$url = '/' . $string . '/';
} elseif (preg_match('/^[a-z]{2}\/$/', $string) ) { //language not found
	$magic = FALSE;
} elseif (substr($string,-4) != '.htm' && substr($string,-5) != '.html') {
	if (preg_match('_^[a-z]{2}/_', $string) ) {
		$langpath = "/" . substr($string,0,2) . '/';
		$find = substr($string,3);
	} else {
		$langpath = '/';
		$find = $string;
	}
	$url = $langpath . "find?find=" . $find;
	#$url = "https://" . $_SERVER['HTTP_HOST'] . "/" . $langpath . "find?find=" . $find;
} else {
	$magic = FALSE;
}

if ($magic) {
	header("Location: $url");
	exit;
} else {
	header("HTTP/1.1 404 Not Found");
	print "<h1 style=\"font-family: sans-serif;\">404 Not Found</h1> <p>404 - Filen findes ikke</p>";
}

?>
