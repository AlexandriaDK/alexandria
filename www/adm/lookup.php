<?php
#require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$type = (string) $_REQUEST['type'];
$label = (string) $_REQUEST['label'];
$id = (int) $_REQUEST['currentid'];

if ($type == 'sce' && $label != "") {
	$num = getone("SELECT COUNT(*) FROM sce WHERE title = '" . dbesc($label) . "'");
	print $num;
}

if ($type == 'countrycode' && $label != "") {
	$countryname = getCountryName($label);
	print $countryname;	
}
?>
