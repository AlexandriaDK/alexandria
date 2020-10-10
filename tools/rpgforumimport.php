<?php
/*

CREATE TABLE `rpgforum_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `author` text CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  `timestamp` datetime DEFAULT NULL,
  `views` int DEFAULT NULL,
  `post` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `post_idx` (`author`,`title`,`post`),
  FULLTEXT KEY `post_aut_idx` (`author`)
) ENGINE=InnoDB AUTO_INCREMENT=63867 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_danish_ci

 */

chdir("../www/");
require "rpgconnect.inc.php";
require "base.inc.php";

doquery("ALTER TABLE rpgforum_posts DISABLE KEYS");

$file = '../../loot.alexandria.dk/rpgforum/posts-wip.html';

function timefix($timestamp) {
    $from = ['maj', 'okt'];
    $to = ['may', 'oct'];
    $time = str_replace($from, $to, $timestamp);
    return date("Y-m-d H:i:s", strtotime($time));
}

function hed ($string) {
	$string = html_entity_decode( $string, ENT_COMPAT | ENT_HTML401, 'iso-8859-1' );
	return $string;
}

function uhed ($string) {
	return utf8_encode( hed( $string ) );
}
/*
$x = 'Test &aelig; Test';
$x = hed($x);

print $x . PHP_EOL;
print utf8_encode($x) . PHP_EOL;
exit;
 */

$posts = [];

$fp = fopen($file, "r");

$title = $author = $timestamp = $post = "";
$views = 0;

$lines = 0;
$postcount = 0;
while (($line = fgets($fp)) != FALSE) {
    $lines++;
    if (! $title ) {
        if (preg_match('_^<h1>(.*?)</h1>_', $line, $match)) {
            $title = uhed($match[1]);
#            print "Found title: $title" . PHP_EOL;
        }
    } elseif ( ! $author ) {
        if (preg_match('_^<p><em>af (.*?)</em>, (.*?,.*?), (\d+) visninger</p>_', $line, $match) ) {
            $author = uhed($match[1]);
            $timestamp = uhed($match[2]);
            $views = uhed($match[3]);
#            print "Found author: $author, $timestamp, $views" . PHP_EOL;
        }
    } else { // part of string
        if (preg_match('_^(.*)</em></b></i></ul></ol></li><hr/>$_', $line, $match) ) {
            $post .= hed($match[1]);
	    $post = str_replace( "\x92", "'", $post ); // fix invalid char that prevents inserts
	    $post = utf8_encode( $post );
            $query = "INSERT INTO rpgforum_posts(title, author, timestamp, views, post) values ('" . dbesc($title) . "', '" . dbesc($author) . "', '" . timefix($timestamp) . "', $views, '" . dbesc($post) . "')";
	    #print $query . PHP_EOL;
	    doquery( $query );
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
?>
