<?php
$string = substr($_SERVER['REQUEST_URI'],1);

$magic = TRUE;

if (is_dir($_SERVER['DOCUMENT_ROOT']."/$string")) {
	$url = "http://{$_SERVER['HTTP_HOST']}/$string/";
} elseif (preg_match('/^[a-z]{2}$/', $string) ) { //language redirect
	$url = '/' . $string . '/';
} elseif (substr($string,-4) != '.htm' && substr($string,-5) != '.html') {
	$url = "http://{$_SERVER['HTTP_HOST']}/find?find=".$string;
} else {
	$magic = FALSE;
}

if ($magic) {
	header("Location: $url");
	exit;
} else {
	print "<h1 style=\"font-family: sans-serif;\">404 Not Found</h1> <p>404 - Filen findes ikke</p>";
}

?>
