<?php
chdir("../www/");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";

doquery("ALTER TABLE rpgforum_posts DISABLE KEYS");

$file = '../../loot.alexandria.dk/rpgforum/posts-wip.html';

function timefix($timestamp)
{
  $from = ['maj', 'okt'];
  $to = ['may', 'oct'];
  $time = str_replace($from, $to, $timestamp);
  return date("Y-m-d H:i:s", strtotime($time));
}

function hed($string)
{
  $string = html_entity_decode($string, ENT_COMPAT | ENT_HTML401, 'iso-8859-1');
  return $string;
}

function uhed($string)
{
  return mb_convert_encoding(hed($string), "UTF-8", "ISO-8859-1");
}

$posts = [];

$fp = fopen($file, "r");

$title = $author = $timestamp = $post = "";
$views = 0;

$lines = 0;
$postcount = 0;
while (($line = fgets($fp)) != false) {
  $lines++;
  if (! $title) {
    if (preg_match('_^<h1>(.*?)</h1>_', $line, $match)) {
      $title = uhed($match[1]);
      #            print "Found title: $title" . PHP_EOL;
    }
  } elseif (! $author) {
    if (preg_match('_^<p><em>af (.*?)</em>, (.*?,.*?), (\d+) visninger</p>_', $line, $match)) {
      $author = uhed($match[1]);
      $timestamp = uhed($match[2]);
      $views = uhed($match[3]);
      #            print "Found author: $author, $timestamp, $views" . PHP_EOL;
    }
  } else { // part of string
    if (preg_match('_^(.*)</em></b></i></ul></ol></li><hr/>$_', $line, $match)) {
      $post .= hed($match[1]);
      $post = str_replace("\x92", "'", $post); // fix invalid char that prevents inserts
      $post = mb_convert_encoding($post, "ISO-8859-1");
      $query = "INSERT INTO rpgforum_posts(title, author, timestamp, views, post) values ('" . dbesc($title) . "', '" . dbesc($author) . "', '" . timefix($timestamp) . "', $views, '" . dbesc($post) . "')";
      #print $query . PHP_EOL;
      doquery($query);
      $postcount++;
      $title = $author = $timestamp = $post = "";
      $views = 0;
      if ($postcount % 100 == 0) {
        print "Posts: $postcount" . PHP_EOL;
      }
    } else {
      $post .= hed($line);
    }
  }
}
print "Total posts: $postcount" . PHP_EOL;

doquery("ALTER TABLE rpgforum_posts ENABLE KEYS");
