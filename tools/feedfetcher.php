<?php
// due to blog.com
ini_set("user_agent", "Alexandria.dk feedfetcher");
chdir('../www/');
require("./connect.php");
require("./base.inc.php");
$blog_id = intval($_SERVER['argv']['1'] ?? 0);

function utf8_decode_enhanced($string) {
	$result = html_entity_decode($string,ENT_COMPAT,"UTF-8");
	return $result; // we now use utf-8 - no need to convert anything
	
	$result = str_replace("”",'"',$result); // replaces quote
	$result = str_replace("“",'"',$result); // replaces quote
	$result = str_replace("’","'",$result); // replaces quote
	$result = str_replace("–",'-',$result); // replaces dash
	$result = str_replace("…",'...',$result); // replaces ellipsis
	$result = utf8_decode($result);
	return $result;
}

function parsefeed($feed_id, $xmlstr) {
	$xml = simplexml_load_string($xmlstr);
	
	// check type
	if ( ( (string) $xml->link[0]["type"]) == 'application/atom+xml' && $feed_id != 11) { // ATOM
		print "! Type: ATOM\n";
		foreach($xml->entry AS $i) {
			$title = utf8_decode_enhanced($i->title);
			$pubdate = date("Y-m-d H:i:s",strtotime($i->issued));
			$guid = $i->id;
			$link = "";
			if ( ( (string) $i->link[1]["type"]) == "text/html") {
				$link = (string) $i->link[1]["href"];
			}
			$content = utf8_decode_enhanced($i->content);
			if (!(getone("SELECT 1 FROM feedcontent WHERE feed_id = $feed_id AND guid = '".dbesc($guid)."'") ) ) {
				doquery("INSERT INTO feedcontent (feed_id, title, guid, link, pubdate, content) VALUES ($feed_id, '".dbesc($title)."', '".dbesc($guid)."', '".dbesc($link)."', '$pubdate', '".dbesc($content)."')");
			} else {
				print "- Skipping ".$title."\n";
			}
		}

	} else { // RSS
		print "! Type: RSS\n";
		$comments = 0;
		$itemlist = $xml->channel->item;
		if (!$itemlist) {
			$itemlist = $xml->item;
		}
		foreach($itemlist AS $i) {
			$title = utf8_decode_enhanced($i->title);
			$pubdate = date("Y-m-d H:i:s",strtotime( $i->pubDate ? $i->pubDate : $i->date ) );
			$guid = $i->guid;
			$link = $i->link;
			if (!$guid) $guid = $link; //hack for skolerollespil.dk
			$content = utf8_decode_enhanced($i->description);
			// for slash namespace
			$ns = $i->getNamespaces(true);
			if ($ns['slash']) {
				$slash = $i->children($ns['slash']);
				$comments = (int) $slash->comments;
			} elseif ($ns['thr']) {
				$thr = $i->children($ns['thr']);
				$comments = (int) $thr->total;
			}

			if (!(getone("SELECT 1 FROM feedcontent WHERE feed_id = $feed_id AND guid = '".dbesc($guid)."'") ) ) {
				print "+ Inserting ".$title."\n";
				doquery("INSERT INTO feedcontent (feed_id, title, guid, link, pubdate, content) VALUES ($feed_id, '".dbesc($title)."', '".dbesc($guid)."', '".dbesc($link)."', '$pubdate', '".dbesc($content)."')");
			} else {
				print "- Updating ".$title."\n";
				doquery("UPDATE feedcontent SET title = '".dbesc($title)."', comments = $comments WHERE feed_id = $feed_id AND guid = '".dbesc($guid)."'");
			}
		}
	}
	print "\n";
	return true;
}

// main
print "Beginning fetch at ".date("Y-m-d H:i:s")."\n\n";
if ($blog_id) {
	$feeds = getall("SELECT id, url, owner FROM feeds WHERE id = '$blog_id'");
} else {
	$feeds = getall("SELECT id, url, owner FROM feeds WHERE pauseupdate = 0");
}

foreach($feeds AS $feed) {
	if ($xmlstr = file_get_contents($feed['url'])) {
		print "Parsing blog ".$feed['id']." (".$feed['owner'].")\n";
		parsefeed($feed['id'], $xmlstr);
		doquery("UPDATE feeds SET lastchecked = NOW() WHERE id = $feed[id]");
	}
}

print "Finishing fetch at ".date("Y-m-d H:i:s")."\n\n";
print "===\n\n";
?>
