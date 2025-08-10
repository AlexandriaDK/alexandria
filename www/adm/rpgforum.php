<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$postid = (int) ($_GET['postid'] ?? false);
$search = trim((string) ($_GET['search'] ?? ''));
$author = trim((string) ($_GET['author'] ?? ''));
$order = (string) ($_GET['order'] ?? '');
$orderlist = ['score', 'title', 'timestamp'];
$limit = (int) ($_GET['limit'] ?? 0);
if ($limit < 1) {
  $limit = 1000;
}

function fixpost($post)
{
  $post = str_replace('<img src="../newpics/quote.gif">', '<span style="font-size:1.5em;">"</span>', $post);
  $post = str_replace('<img src="../pics/quote.gif">', '<span style="font-size:1.5em;">"</span>', $post);
  $post = str_replace('<img src="../xmas/quote.gif">', '<span style="font-size:1.5em;">"</span>', $post);
  $content = $post;
  $doc = new DOMDocument();
  $doc->substituteEntities = false;
  $content = mb_convert_encoding($content, 'html-entities', 'utf-8');
  $doc->loadHTML($content);
  $result = $doc->saveHTML();
  $result = str_replace(['<html><body>', '</body></html>'], '', $result);
  $result = html_entity_decode($result);
  return $result;
}

htmladmstart("RPGFORUM");

print "<h1>RPGFORUM Archive search</h1>";

print '<form action="rpgforum.php"><div>Search text: <input type="text" name="search" value="' . htmlspecialchars($search) . '"></div></form>' . PHP_EOL;
print '<form action="rpgforum.php"><div>Or author: <input type="text" name="author" value="' . htmlspecialchars($author) . '"></div></form>' . PHP_EOL;

if ($search !== '' || $author !== '' || $postid != 0) {
  $max_id = getone("SELECT MAX(id) FROM rpgforum_posts");
  print "<hr>";
  if (! in_array($order, $orderlist)) {
    $order = 'timestamp';
  }
  if ($search !== '') {
    $query = ("SELECT id, title, author, timestamp, views, post, MATCH(title,post) AGAINST('" . dbesc($search) . "' IN NATURAL LANGUAGE MODE) AS score FROM rpgforum_posts WHERE MATCH(title,post) AGAINST('" . dbesc($search) . "' IN NATURAL LANGUAGE MODE) ORDER BY $order DESC LIMIT $limit");
  } elseif ($author !== '') {
    $order = 'score desc, timestamp desc';
    $query = ("SELECT id, title, author, timestamp, views, post, MATCH(author) AGAINST('" . dbesc($author) . "' IN NATURAL LANGUAGE MODE) AS score FROM rpgforum_posts WHERE MATCH(author) AGAINST('" . dbesc($author) . "' IN NATURAL LANGUAGE MODE) ORDER BY $order LIMIT $limit");
  } else {
    $query = ("SELECT id, title, author, timestamp, views, post FROM rpgforum_posts WHERE id = $postid");
  }
  $result = getall($query);
  foreach ($result as $post) {
    print '<div class="rpgforumpost">' . PHP_EOL;
    print '<h2 style="margin-bottom: 0"><a href="rpgforum.php?postid=' . $post['id'] . '">' . htmlspecialchars($post['title']) . '</a></h2>' . PHP_EOL;
    print '<div class="nav">';
    if ($post['id'] > 0) {
      print '<a href="rpgforum.php?postid=' . ($post['id'] - 1) . '" title="Previous post by time">&ShortLeftArrow;</a> ';
    }
    if ($post['id'] < $max_id) {
      print '<a href="rpgforum.php?postid=' . ($post['id'] + 1) . '" title="Next post by time">&ShortRightArrow;</a> ';
    }
    print '</div>';
    print '<p style="font-weight: bold;">By <a href="rpgforum.php?author=' . rawurlencode($post['author']) . '">' . htmlspecialchars($post['author']) . "</a>, " . fulldatetime($post['timestamp']) . ", " . $post['views'] . " views.</p>";
    print fixpost($post['post']);
    print '</div>' . PHP_EOL;
    print "<hr>" . PHP_EOL . PHP_EOL;
  }
  if (count($result) == $limit) {
    print "<p><i>Limit of $limit results has been reached</i></p>";
  }
}


htmladmend();
