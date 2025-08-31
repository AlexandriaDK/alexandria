<?php
require_once(__DIR__ . '/../includes/db.auth.php');

$db_name = DB_NAME;
$db_user = DB_USER;
$db_pass = DB_PASS;
$db_host = DB_HOST;
$db_connector = DB_CONNECTOR;

mysqli_report(MYSQLI_REPORT_OFF);
if (!@($dblink = mysqli_connect($db_host, $db_user, $db_pass)) || @$_SERVER['QUERY_STRING'] == "crash") {
  define('DBERROR', true);
  header("HTTP/1.1 503 Service Unavailable");
  header("X-Error: Database");
  require("base.inc.php");
  $t->display('dberror.tpl');
  exit;
}
mysqli_select_db($dblink, $db_name) or die("Unable to select database\n"); // definitely need better error
mysqli_set_charset($dblink, "utf8mb4");
mysqli_query($dblink, "SET sql_mode = ''"); // allow dates such as 0000-00-00
