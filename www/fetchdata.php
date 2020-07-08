<?php
require_once("./connect.php");
require_once("base.inc.php");
require_once("fetchdata.inc.php");

$category = $_REQUEST['category'];
$data_id = $_REQUEST['data_id'];

$result = getFullEntry($category,$data_id);

header("Content-Type: application/json");
print json_encode($result);
?>

