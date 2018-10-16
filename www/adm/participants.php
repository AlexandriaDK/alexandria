<?php
// Denne side scraper intern igennem efter spiller- og spilleder-antal
$admonly = TRUE;
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$scenarie = $_REQUEST['scenarie'];
$auto_players_max = $auto_players_min = $_REQUEST['auto_players_min'];
if ($scenarie && $auto_players_min) {
	if ($auto_players_min == 'en' || $auto_players_min == 'én') $auto_players_max = $auto_players_min = 1;
	if ($auto_players_min == 'to') $auto_players_max = $auto_players_min = 2;
	if ($auto_players_min == 'tre') $auto_players_max = $auto_players_min = 3;
	if ($auto_players_min == 'fire') $auto_players_max = $auto_players_min = 4;
	if ($auto_players_min == 'fem') $auto_players_max = $auto_players_min = 5;
	if ($auto_players_min == 'seks') $auto_players_max = $auto_players_min = 6;
	if (strpos($auto_players_min, '-') ) {
		list($auto_players_min, $auto_players_max) = explode('-', $auto_players_min);
	}
	doquery("UPDATE sce SET players_min = $auto_players_min, players_max = $auto_players_max WHERE id = $scenarie");
	doquery("INSERT INTO log (data_id, category, time, user, user_id, ip, note) VALUES ($scenarie, 'sce', NOW(), 'Peter Brodersen', 4, '77.66.4.55', 'GM-antal oprettet')");
	header("Location: scenarie.php?scenarie=$scenarie");
	exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - deltagere til scenarie</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<?php
include("links.inc");

$data = getall("SELECT id, title, intern FROM sce WHERE intern REGEXP 'spiller|player|participant|deltager|antal' AND players_min IS NULL");
foreach($data AS $sce) {
	$lines = explode("\n",$sce['intern']);
	$id = $sce['id'];
	$p = 0;
	print "<p>";
	print "<b><a href=\"scenarie.php?scenarie=" . $id . "\">" . $sce['title'] . "</a></b>\n";
	print "<br />";
	foreach($lines AS $line) {
		if ( preg_match('/(spiller|player|participant|deltager|antal)/i',$line) && preg_match('/(\d|en|én|to|tre|fire|fem|seks)/',$line) ) {
			if ($p == 0) {
				$p = 1;
			}
			$linelink = preg_replace('/(\d+(-\d+)?|\ben\b|\bén\b|\bto\b|\btre\b|\bfire\b|\bfem\b|\bseks\b)/','<b style="font-weight: 900; font-size: 1.2em;"><a href="participants.php?scenarie='.$id.'&amp;auto_players_min=$1">_$1_</a></b>',$line);
			print $linelink . "<br />\n";
		}
	}

	if ($p == 1) {
		print "</p>\n";
		print "<hr/>";
	}
}

?>

</body>
</html>
