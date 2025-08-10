<?php
$admonly = true;
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

htmladmstart("Debug");

print "<h1>Language</h1>";
print "<pre>";
print "LANG: " . htmlspecialchars(LANG) . PHP_EOL;
print "</pre>";

print "<h1>Cookie:</h1>";
print "<pre>";
print htmlspecialchars(print_r($_COOKIE, true));
print "</pre>";

print "<h1>Session:</h1>";
print "<pre>";
print htmlspecialchars(print_r($_SESSION, true));
print "</pre>";


print "</body>\n</html>\n";
