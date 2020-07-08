<?php
require("./connect.php");
require("base.inc.php");

$site = ($_REQUEST['site']?$_REQUEST['site']:'rpgforum');
$id = $_REQUEST['id'];

header("Location: http://".$_SERVER['HTTP_HOST']."/".create_login_url($id,$site) );
exit;

?>
