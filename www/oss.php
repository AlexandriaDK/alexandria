<?php
require("./connect.php");
require("base.inc");
require("template.inc");

/*
function osst ($tid, $title) {
	$html = "\t\t<p>\n\t\t\t<a id=\"$tid\" class=\"oss\">$title</a> <a href=\"#ttop\" style=\"font-size: smaller;\">[top]</a>\n\t\t</p>\n\n";
	return $html;
}

function ossl ($tid, $title) {
	$html = "\t\t\t<li><a href=\"#$tid\">$title</a></li>\n";
	return $html;
}

function ossh ($title) {
	$html = "\t\t<hr style=\"height: 1px; color: 1px solid black;\" />\n\t\t<h3>\n\t\t\t".$title."\n\t\t</h3>\n";
	return $html;
}


*/
$t->display('faq.tpl');

?>
