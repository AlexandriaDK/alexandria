<?php
require("./connect.php");
require("base.inc.php");

$q = mysql_query("SELECT id, url FROM links ORDER BY id");

while ($row = mysql_fetch_array($q)) {
  print "<li><a href=\"{$row['url']}\">{$row['id']}: {$row['url']}</a>\n";
}
