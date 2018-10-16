<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$find = $_REQUEST['find'];

// Lidt kvik-find-kode:

if (preg_match("/^([cspfat#])(\d+)$/i",$find,$regs)) {
	$pref = strtolower($regs[1]);
	$id = $regs[2];

	switch($pref) {
		case "s":
			header("Location: http://{$_SERVER['HTTP_HOST']}/adm/scenarie.php?scenarie=$id");
			break;
		
		case "c":
			header("Location: http://{$_SERVER['HTTP_HOST']}/adm/convent.php?con=$id");
			break;

		case "p":
		case "f":
		case "a":
			header("Location: http://{$_SERVER['HTTP_HOST']}/adm/person.php?person=$id");
			break;

		case "t":
		case "#":
			header("Location: http://{$_SERVER['HTTP_HOST']}/adm/ticket.php?id=$id");
			break;

	}

}

$r1 = getall("
	(SELECT id, title, 0 AS alias FROM sce WHERE title LIKE '%".dbesc($find)."%')
	UNION
	(SELECT data_id, label AS title, 1 AS alias FROM alias WHERE label LIKE '%".dbesc($find)."%' AND category = 'sce')
	ORDER BY title
");
$r2 = getall("
	(SELECT id, CONCAT(firstname,' ',surname) AS name, 0 AS alias FROM aut WHERE CONCAT(firstname,' ',surname) LIKE '%".dbesc($find)."%')
	UNION
	(SELECT data_id, label AS name, 1 AS alias FROM alias WHERE label LIKE '%".dbesc($find)."%' AND category = 'aut')
	ORDER BY name
");

if (count($r1) == 1 && count($r2) == 0) {
	$id = $r1[0][0];
	header("Location: http://{$_SERVER['HTTP_HOST']}/adm/scenarie.php?scenarie=$id");
	exit;
} elseif (count($r1) == 0 && count($r2) == 1) {
	$id = $r2[0][0];
	header("Location: http://{$_SERVER['HTTP_HOST']}/adm/person.php?person=$id");
	exit;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><HEAD><TITLE>Administration - søgning</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
</HEAD>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<?php
include("links.inc");

print "<b>Fundne resultater:</b><br>";

print "Scenarier:<br>";

unset($antal);
foreach($r1 AS list($id, $name, $alias) ) {
	print "&nbsp;&nbsp;<a href=\"scenarie.php?scenarie=$id\" " . ($alias ? 'style="font-style: italic;"' : '' ) . ">$name</a><br>\n";
/*
	if (!$alias) {
		print "&nbsp;&nbsp;<a href=\"scenarie.php?scenarie=$id\">$name</a><br>";
	} else {
		print "&nbsp;&nbsp;<i><a href=\"scenarie.php?scenarie=$id\">$name</a></i><br>";
	}
*/
	$antal++;
}
if ($antal == 0) print "&nbsp;&nbsp;Ingen<br>";
print "<br>\n\n";

print "Personer:<br>\n";

unset($antal);

foreach($r2 AS list($id, $name, $alias) ) {
	print "&nbsp;&nbsp;<a href=\"person.php?person=$id\" " . ($alias ? 'style="font-style: italic;"' : '' ) . ">$name</a><br>\n";
	$antal++;
}
if ($antal == 0) print "&nbsp;&nbsp;Ingen<br>";

print "<br><a href=\"{$_SERVER['HTTP_REFERER']}\">Tilbage</a><br>";

?>
</body>
</html>
