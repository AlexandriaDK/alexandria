<?php
require("./connect.php");
require("base.inc.php");


header("Content-Type: text/plain");

$convents = getall("SELECT a.id, a.name, a.year, a.begin, a.end, a.place, b.name AS consetname FROM convent a INNER JOIN conset b ON a.conset_id = b.id WHERE a.year = 2007 ORDER BY a.begin");

print "BEGIN:VCALENDAR\r\n";
print "VERSION:2.0\r\n";
print "CALSCALE:GREGORIAN\r\n";
print "METHOD:PUBLISH\r\n";

foreach($convents AS $convent) {
	print "BEGIN:VEVENT\r\n";
	print "DURATION:PT30M\r\n";
	print "LOCATION:".$convent['place']."\r\n";
	print "DTSTART:".date("c",strtotime($convent['begin']))."\r\n";
	print "DTEND:".date("c",strtotime($convent['begin']))."\r\n";
	print "UID:".$convent['id']."\r\n";
	print "SUMMARY:".$convent['name']."\r\n";
	print "END:VEVENT\r\n";
}

?>
