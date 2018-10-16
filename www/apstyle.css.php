<?php
session_start();
header("Content-Type: text/css");
//if (date("Y-m-d") != "2016-04-01" && $_SERVER['REMOTE_ADDR'] != "77.66.4.55" && $_SERVER['REMOTE_ADDR'] != "144.76.154.185" ) {
if (date("Y-m-d") != "2016-04-01" ) {
	$_SESSION['ap2016'] = 0.1;
	exit;
}
setcookie("ap2016","1",mktime(2,0,0,5,1,2016) );

if (!$_SESSION['ap2016'] || $_SERVER['HTTP_PRAGMA'] || $_SERVER['HTTP_CACHE_CONTROL'] ) {
	$_SESSION['ap2016'] = 0.1;
} else {
	$_SESSION['ap2016'] += 0.2;
}
$blur = $_SESSION['ap2016'];
$hue = $_SESSION['ap2016'] * 60;
?>
body {
	-webkit-filter: blur(<?php print $blur; ?>px) hue-rotate(<?php print $hue; ?>deg);
	-ms-filter: blur(<?php print $blur; ?>px) hue-rotate(<?php print $hue; ?>deg);
	filter: blur(<?php print $blur; ?>px) hue-rotate(<?php print $hue; ?>deg);
}
