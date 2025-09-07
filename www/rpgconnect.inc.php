<?php
require_once(__DIR__ . '/../includes/db.auth.php');

mysqli_report(MYSQLI_REPORT_OFF);

if (!@($dblink = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306)) || @$_SERVER['QUERY_STRING'] == "crash") {
  define('DBERROR', TRUE);
  header("HTTP/1.1 503 Service Unavailable");
  header("X-Error: Database");
  require("base.inc.php");
  $t->display('dberror.tpl');
  exit;
}
mysqli_select_db($dblink, DB_NAME) or die("Unable to select database\n"); // definitely need better error
mysqli_set_charset($dblink, "utf8mb4");
mysqli_query($dblink, "SET sql_mode = ''"); // allow dates such as 0000-00-00
