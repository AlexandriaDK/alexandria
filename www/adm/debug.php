<?php
$admonly = TRUE;
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

htmladmstart("Debug");

print "<h1>Language</h1>";
print "<pre>";
print "LANG: " . htmlspecialchars( LANG ) . PHP_EOL;
#print "PHP Locale, language: " . locale_get_display_language( 'da', 'da' );
print "</pre>";

print "<h1>Cookie:</h1>";
print "<pre>";
print htmlspecialchars(print_r($_COOKIE, TRUE));
print "</pre>";

print "<h1>Session:</h1>";
print "<pre>";
print htmlspecialchars(print_r($_SESSION, TRUE));
print "</pre>";


print "</body>\n</html>\n";

?>
