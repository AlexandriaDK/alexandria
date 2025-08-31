<?php
require_once "./connect.php";
require_once "base.inc.php";

$news = getnews();

foreach ($news as $data) {
  print $data['published'] . "\t" . textlinks($data['text'], true) . "\n";
}
