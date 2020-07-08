<?php
chdir("../../");
require("./connect.php");
require("base.inc.php");

$postdata = getone("SELECT incoming_raw FROM actions_log WHERE id = 2");

$r = json_decode($postdata);

print_r($r);

$intentName = $r->queryResult->intent->displayName;
$languageCode = $r->queryResult->languageCode;

print $intentName . PHP_EOL;
print $languageCode . PHP_EOL;

