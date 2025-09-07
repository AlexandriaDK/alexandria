<?php
// få til at virke på rollespil.dk
// få til at validere

require_once "./connect.php";
require_once "base.inc.php";

$articles = getall("SELECT a.owner, a.name, a.person_id, b.title, b.link, b.pubdate, b.content FROM feeds a, feedcontent b WHERE a.id = b.feed_id ORDER BY b.pubdate DESC LIMIT 0,40");

header("Content-Type: application/rss+xml");

print "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
/*
print "<?xml-stylesheet title=\"XSL_formatting\" type=\"text/xsl\" href=\"rss.xsl\"?>\n";
*/
print '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns="http://purl.org/rss/1.0/" xmlns:admin="http://webns.net/mvcb/" xmlns:l="http://purl.org/rss/1.0/modules/link/" xmlns:content="http://purl.org/rss/1.0/modules/content/">';
print "\n";
print '<channel rdf:about="https://alexandria.dk/">';
print '<title>Alexandrias blog-feeds</title>';
print '<link>https://alexandria.dk/feeds</link>';
print '<description>Rollespilsfortegnelsen Alexandrias meta-feed over danske rollespilsblogs</description>';
print '<dc:language>da-DK</dc:language>';
print "\n<items>\n";
print "\t<rdf:Seq>\n";

foreach ($articles as $article) {
  $url = $article['link'];
  print "\t\t";
  print '<rdf:li rdf:resource="' . htmlspecialchars($url) . '" />';
  print "\n";
}
print "\t</rdf:Seq>\n</items>\n";
print "</channel>\n\n";

foreach ($articles as $article) {
  $url = $article['link'];
  print '<item rdf:about="' . $url . '" >';
  print "\n";
  print "<title>" . htmlspecialchars($article['owner']) . ": " . htmlspecialchars($article['title']) . "</title>\n";
  print "<link>" . $url . "</link>\n";
  print "<description>" . htmlspecialchars($article['content']) . "</description>\n";
  print "<author>" . htmlspecialchars($article['owner']) . "</author>\n";
  print "<pubDate>" . date("r", strtotime($article['pubdate'])) . "</pubDate>\n";

  print "</item>\n\n";
}

print "</rdf:RDF>\n";
