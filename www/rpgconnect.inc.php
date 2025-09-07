<?php
mysqli_report(MYSQLI_REPORT_OFF);

if (!@($dblink = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'], 3306)) || @$_SERVER['QUERY_STRING'] == "crash") {
  define('DBERROR', TRUE);
  header("HTTP/1.1 503 Service Unavailable");
  header("X-Error: Database");
  require("base.inc.php");
  $t->display('dberror.tpl');
  exit;
}
mysqli_select_db($dblink, $_ENV['DB_NAME']) or die("Unable to select database\n"); // definitely need better error
mysqli_set_charset($dblink, "utf8mb4");
mysqli_query($dblink, "SET sql_mode = ''"); // allow dates such as 0000-00-00
