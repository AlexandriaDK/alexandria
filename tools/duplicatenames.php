<?php
// Find possible duplicate names based on middle names
chdir(__DIR__ . '/../www/');
require_once "./connect.php";
require_once "./base.inc.php";

$names = getcolid("SELECT id, CONCAT(firstname, ' ', surname) AS name FROM person");
$namesclean = [];
foreach ($names as $id => $name) {
  $parts = explode(' ', $name);
  if (count($parts) > 2) {
    $newname = $parts[0] . ' ' . $parts[count($parts) - 1];
    $newid = array_search($newname, $names);
    if ($newid) {
      print "$name ($id), $newname ($newid)" . PHP_EOL;
    }
  }
}
