<?php
require("./connect.php");
require("smartfind.inc");

if ($QUERY_STRING) $navn = urldecode($QUERY_STRING);
else $navn = "Preter Brolersen";
print "Navn: $navn<br>";
print "ID: ".getautidbyname($navn)."<br><br>";

print "Scenarie: $navn<br>";
print "ID: ".getsceidbytitle($navn)."<br><br>";

print "Con: $navn<br>";
print "ID: ".getconidbyname($navn)."<br><br>";

?>
