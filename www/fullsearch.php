<?php
require("./connect.php");
require("base.inc.php");

$sql = "SELECT a.id, a.category, a.data_id, b.label, SUBSTRING(b.content, LOCATE('splatter',content)-30, LENGTH('splatter')+60) from files a, filedata b where a.id = b.files_id AND MATCH(content) AGAINST ('splatter')";

$result = getall($sql);

var_dump($result);
exit;
