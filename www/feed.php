<?php
require("./connect.php");
require("base.inc.php");

$news = getnews();

foreach ($news as $data) {
  print $data['published'] . "\t" . textlinks($data['text'], true) . "\n";
}
