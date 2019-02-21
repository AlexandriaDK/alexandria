<?php
$admonly = TRUE;
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'news';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - debug</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body>
<?php
include("links.inc");

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

print "<h1>Session:</h1>";
print "<pre>";
print htmlspecialchars(print_r($_SESSION, TRUE));
print "</pre>";

print "</body>\n</html>\n";

?>
