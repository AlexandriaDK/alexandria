<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$id = (int) $_REQUEST['id'];
$vis = $_REQUEST['vis'];
$action = $_REQUEST['action'];
$intern = $_REQUEST['intern'];
$status = $_REQUEST['status'];

if ($action == "ret" && $id) {
	$query = "UPDATE updates SET intern = '" . dbesc($intern) . "', status = '$status' WHERE id = $id";
	doquery($query);
	if ($_REQUEST['vis'] == 'alle') {
		header("Location: ticket.php?id=$id&vis=alle");
	} else {
		header("Location: ticket.php?id=$id");
	}
	exit;
}


$title = "Administration - tickets";
if ($id > 0) $title .= " - #$id";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head><title><?php print $title; ?></title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">
<?php
include("links.inc");

$query = "SELECT id, data_id, category, title, submittime, user_name, user_email, status FROM updates ORDER BY id DESC";
$result = getall($query) or die(dberror());

$vis = ($_REQUEST['vis'] == 'alle' ? 'alle' : '');

print "<table><tr valign=\"top\"><td>";

if ($vis == 'alle') {
	$vislink = '<a href="ticket.php?id='.$id.'">[ikke&nbsp;lukkede]</a>';
} else {
	$vislink = '<a href="ticket.php?id='.$id.'&amp;vis=alle">[alle]</a>';
}

// Lav oversigt
#print '<table class="ticketlist" cellspacing=2><tr style="background: #ffeebb"><td>ID</td><td>Afsender</td><td>Materiale</td><td>Indsendt</td><td>Status</td></tr>';
#print '<table class="ticketlist" cellspacing=2><tr style="background: #ffeebb"><td>ID</td><td>Afsender</td><td>Data</td><td>Indsendt</td><td>Status&nbsp;'.$vislink.'</td></tr>';
print '<table class="ticketlist" cellspacing="0" cellpadding="2"><tr style="background: #ffeebb"><td>ID</td><td>Afsender</td><td>Indsendt</td><td>Status&nbsp;'.$vislink.'</td></tr>';
foreach($result AS $row) {
	if ($row['status'] == "lukket" && $vis != "alle") continue;
	if ($row['data_id'] && $row['category']) {
		$label = getlabel($row['category'],$row['data_id']);
		if (!$label) $label = $row['title'];
	} else {
		$label = $row['title'];
	}

#	list($date,$time) = explode(" ",$row['submittime']);
#	if ($date == date("Y-m-d")) {
#		$timeinfo = 

	if ($id == $row['id']) {
		print "<tr valign=\"top\" style=\"background: #fa9;\">";
	} else {
		print "<tr valign=\"top\">";
	}
	print "<td nowrap style=\"text-align: right\"><a href=\"ticket.php?id={$row['id']}&amp;vis=$vis\">{$row['id']}</a></td>";
	print "<td nowrap>{$row['user_name']}</td>";
	print "<td>$label</td>";
	
#	print "<td nowrap></td>";
	print "<td nowrap>{$row['status']}</td>";
	
	print "</tr>";
}
print '</table>';

print "</td><td>";


// Vis enkelt entry
if ($id) {
	$row = getrow("SELECT id, data_id, category, title, description, submittime, user_name, user_email, intern, status FROM updates WHERE id = '$id'") or die(dberror());
	if ($row['data_id'] && $row['category']) {
		$label = getlabel($row['category'],$row['data_id'],TRUE,$row['title']);
	} else {
		$label = $row['title'];
	}

	print "<form action=\"ticket.php\" method=\"post\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"ret\">\n";
	print "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	print "<input type=\"hidden\" name=\"vis\" value=\"$vis\">\n";
	print "<table class=\"ticketlist\" style=\"border: 1px solid black;\" border=\"1\" cellspacing=\"0\">";
	print "<tr>";
	print "<td>ID: {$row['id']}</td>";
	print "<td>Rettelse til:<br>\n$label</td>";
	print "<td>Indsendt af:<br>\n".htmlspecialchars($row['user_name'])." &lt;<a href=\"mailto:".htmlspecialchars($row['user_email'])."\">".htmlspecialchars($row['user_email'])."</a>&gt;</td>";
	print "<td>Modtaget:<br>\n{$row['submittime']}</td>";
	print "</tr>";

	print '<tr style="background: #ffeebb"><th colspan="4">Kommentar:</th></tr>';
	
	print "<tr>";
	print "<td colspan=\"4\">\n";
	print nl2br(htmlspecialchars($row['description']));
	print "</td></tr>\n";

	print '<tr style="background: #ffeebb"><th colspan="4">Vores log:</th></tr>';

	print '<tr><td colspan="4"><textarea name="intern" rows="6" cols="60">'.htmlspecialchars($row['intern']).'</textarea></td></tr>';

	print "<tr><td colspan=\"4\">Status: <select name=\"status\">\n";
	printf('<option value="åben" %s>Åben</option>',($row['status']=='åben'?'selected':'') );
	printf('<option value="i gang" %s>I gang</option>',($row['status']=='i gang'?'selected':'') );
	printf('<option value="lukket" %s>Lukket</option>',($row['status']=='lukket'?'selected':'') );
	print "</select>\n";
	print "<input type=\"submit\" value=\"Gem\"></td></tr>\n";

	print "</table>";	
}

print "</td></tr></table>\n";

?>
	


</body>
</html>
