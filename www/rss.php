<?php
require("./connect.php");
require("base.inc");
require("template.inc");

$news = getnews();

header("Content-Type: text/xml; charset=UTF-8");

print "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
/*
print "<?xml-stylesheet title=\"XSL_formatting\" type=\"text/xsl\" href=\"rss.xsl\"?>\n";
*/
#print '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns="http://purl.org/rss/1.0/" xmlns:admin="http://webns.net/mvcb/" xmlns:l="http://purl.org/rss/1.0/modules/link/" xmlns:content="http://purl.org/rss/1.0/modules/content/">';
print '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">';
print "\n";
print '<channel>';
print '<title>Alexandria</title>';
print '<link>https://alexandria.dk/</link>';
print '<description>Rollespilsfortegnelsen Alexandria</description>';
print '<atom:link href="https://alexandria.dk/rss.php" rel="self" type="application/rss+xml" />';
print "\n\n";

foreach($news AS $data) {
	$url = "https://alexandria.dk/#news_".str_replace(array("-",":"," "),"",$data['published'])."_".$data['id'];
	print '<item>';
	print "\n";
	print "<title>".strip_tags(textlinks($data['text']))."</title>\n";
	print "<description>".htmlspecialchars(textlinks($data['text'],1))."</description>\n";
	print "<pubDate>". date("r",strtotime($data['published']))."</pubDate>\n";
	print "<guid isPermaLink=\"false\">".$url."</guid>\n";
	print "<link>".$url."</link>\n";
	
	print "</item>\n\n";
}
print "</channel>\n";

print "</rss>\n";

?>
