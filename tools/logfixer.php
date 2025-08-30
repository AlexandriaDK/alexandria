<?php
# Get approximate log statistics based om known start point and log data
# This script does not validate input
# Output is CSV data

require_once "../www/connect.php";
require_once "../www/base.inc.php";

if (count($argv) < 7) {
  print "Missing arguments." . PHP_EOL;
  print "php " . $argv[0] . " startdate enddate sce aut con sys" . PHP_EOL;
  exit(1);
}
#$startdate = '2007-12-26';
#$enddate = '2008-11-01';

$startdate = $argv[1] ?? '2007-01-01';
$enddate = $argv[2] ?? '2007-01-01';

$count = [
  'sce' => $argv[3] ?? 0,
  'aut' => $argv[4] ?? 0,
  'con' => $argv[5] ?? 0,
  'sys' => $argv[6] ?? 0
];

$out = [];

$log = getall("SELECT id, time, note FROM log WHERE time >= '$startdate' AND time <= '$enddate' ORDER BY id");
foreach ($log as $ll) {
  $date = substr($ll['time'], 0, 10);
  if (!$out[$date]) {
    $out[$date] = $count;
  }
  if ($ll['note'] == 'Scenarie oprettet') $count['sce']++;
  if (preg_match('/^Scenarie slettet/', $ll['note'])) $count['sce']--;
  if ($ll['note'] == 'Person oprettet') $count['aut']++;
  if (preg_match('/^Person slettet/', $ll['note'])) $count['aut']--;
  if ($ll['note'] == 'Con oprettet') $count['con']++;
  if (preg_match('/^Con slettet/', $ll['note'])) $count['con']--;
  if ($ll['note'] == 'System oprettet') $count['sys']++;
}
foreach ($out as $date => $l) {
  print implode(",", [$date, $l['sce'], $l['aut'], $l['con'], $l['sys']]) . PHP_EOL;
}
