<?php
// Find possible duplicate names based on middle names
chdir( __DIR__ . '/../www/' );
require("./connect.php");
require("./base.inc.php");

$names = getcolid("SELECT id, CONCAT(firstname, ' ', surname) AS name FROM person");
$namesclean = [];
foreach ($names AS $id => $name) {
    $parts = explode(' ', $name);
    if (count($parts) > 2) {
        $newname = $parts[0] . ' ' . $parts[count($parts)-1];
        $newid = array_search($newname, $names);
        if ($newid) {
            print "$name ($id), $newname ($newid)" . PHP_EOL;
        }
    }
}

?>