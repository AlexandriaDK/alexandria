<?php
require_once(__DIR__ . '/../includes/db.auth.php');
	$db_name = DB_NAME;
	$db_user = DB_USER;
	$db_pass = DB_PASS;
	$db_host = DB_HOST;
	$db_connector = DB_CONNECTOR;

	if (!@ ($dblink = mysqli_connect($db_host, $db_user, $db_pass) ) || @$_SERVER['QUERY_STRING'] == "crash") {
		define('DBERROR',TRUE);
		require("base.inc");
		require("template.inc");
		$t->display('dberror.tpl');
		exit;

	}
	mysqli_select_db($dblink, $db_name) or die("Unable to select db\n");
	mysqli_set_charset($dblink, "utf8mb4");
	mysqli_query($dblink, "SET sql_mode = ''"); // allow dates such as 0000-00-00
?>
