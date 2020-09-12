<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$search = trim((string) $_GET['search']);
$author = trim((string) $_GET['author']);
$order = (string) $_GET['order'];
$orderlist = ['score','title','timestamp'];


function fixpost($post) {
    $doc = new DOMDocument();
    $doc->substituteEntities = false;
    $content = mb_convert_encoding($post, 'html-entities', 'utf-8');
    $doc->loadHTML($content);
    
    $result = $doc->saveHTML();
    $result = str_replace('<img src="../newpics/quote.gif">','<span style="font-size:1.5em;">"</span>', $result);
    return $result;

}

htmladmstart("RPGFORUM");

print "<h1>RPGFORUM Archive search</h1>";

print '<form action="rpgforum.php"><div>Search text: <input type="text" name="search" value="' . htmlspecialchars($search) . '"> Or author: <input type="text" name="author" value="' . htmlspecialchars($author) . '"> <input type="submit"></div></form>';

if ($search !== '' || $author !== '') {
    print "<hr>";
    if( ! in_array($order,$orderlist) ) {
        $order = 'timestamp';
    }
    if ($search !== '') {
        $query = ("SELECT id, title, author, timestamp, views, post, MATCH(title,post) AGAINST('" . dbesc($search) . "' IN NATURAL LANGUAGE MODE) AS score FROM rpgforum_posts WHERE MATCH(title,post) AGAINST('" . dbesc($search) . "' IN NATURAL LANGUAGE MODE) ORDER BY $order DESC LIMIT 100");
    } else {
        $order = 'score desc, timestamp desc';
        $query = ("SELECT id, title, author, timestamp, views, post, MATCH(author) AGAINST('" . dbesc($author) . "' IN NATURAL LANGUAGE MODE) AS score FROM rpgforum_posts WHERE MATCH(author) AGAINST('" . dbesc($author) . "' IN NATURAL LANGUAGE MODE) ORDER BY $order LIMIT 100");
    }
    $result = getall($query);
    foreach ($result AS $post) {
        print '<div class="rpgforumpost">';
        print '<h2 style="margin-bottom: 0">' . htmlspecialchars($post['title']) . '</h2>';
        print '<p style="font-weight: bold;">By ' . htmlspecialchars($post['author']) . ", " . fulldatetime($post['timestamp']) . ", " . $post['views'] . " views.</p>";
        print fixpost($post['post']);
        print "<hr>";
    }
    if (count($result) == 100) {
        print "<p><i>Limit of 100 results has been reached</i></p>";
    }
}


htmladmend();
?>

