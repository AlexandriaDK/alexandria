<?php
require("./connect.php");
require("base.inc.php");

$news = getnews();

foreach($news AS $data) {
	print $data['published']."\t".textlinks($data['text'],TRUE)."\n";
}
?>
