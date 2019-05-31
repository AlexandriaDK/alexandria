<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

if ($conid) {
	$query = "UPDATE convent SET ".
	         "place = '$sted', ".
	         "description = '$description' ".
	         "WHERE id = '$conid'";
	print "QUERY:<br>$query<br><br><br>";
	doquery($query) OR die("Kunne ikke udføre query:".dberror());
}


$r = getall("SELECT * FROM convent ORDER BY convent.name");
foreach($r as $row) {
	if (preg_match("/Sted:(.*)/",$row['description'],$refs)) {
		print "<p><b>".$row['name'].":</b><br>";
		$sted = trim($refs[1]);
		$description = str_replace($refs[0],'',$row['description']);
		print "Sted = <i>$sted</i><br>";
		print $description."<br>";
		$url = "impsted.php?conid={$row['id']}&amp;sted=".urlencode($sted)."&amp;description=".urlencode(trim($description));
		print "<a href=\"$url\">Ret data</a><br>";
	}
	
	
}
