<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$id = (int) ($_REQUEST['id'] ?? 0);
$display = (string) ($_REQUEST['display'] ?? '');
$action = (string) ($_REQUEST['action'] ?? '');
$internal = (string) ($_REQUEST['internal'] ?? '');
$status = (string) ($_REQUEST['status'] ?? '');
$display = ($display == 'all' ? 'all' : '');

$options = ['open', 'in progress', 'closed'];

if ($action) {
	validatetoken($token);
}

if ($action == "update" && $id) {
	$query = "UPDATE updates SET internal = '" . dbesc($internal) . "', status = '$status' WHERE id = $id";
	doquery($query);
	if ($_REQUEST['display'] == 'all') {
		header("Location: ticket.php?id=$id&display=all");
	} else {
		header("Location: ticket.php?id=$id");
	}
	exit;
}

$title = "Administration - tickets";
if ($id > 0) $title .= " - #$id";

htmladmstart("Tickets");

$query = "SELECT id, data_id, category, title, submittime, user_name, user_email, status FROM updates ORDER BY id DESC";
$result = getall($query) or die(dberror());

print "<table><tr valign=\"top\"><td>";

if ($display == 'all') {
	$showlink = '<a href="ticket.php?id=' . $id . '">[only show non-closed]</a>';
} else {
	$showlink = '<a href="ticket.php?id=' . $id . '&amp;display=all">[show all]</a>';
}

// Create overview
print '<table class="ticketlist" cellspacing="0" cellpadding="2"><tr style="background: #ffeebb"><th>ID</th><th>Sender</th><th>Topic</th><th>Status&nbsp;<br>' . $showlink . '</th></tr>';
foreach ($result as $row) {
	if ($row['status'] == "closed" && $display != "all") continue;
	if ($row['data_id'] && $row['category']) {
		$label = getlabel($row['category'], $row['data_id']);
		if (!$label) $label = $row['title'];
	} else {
		$label = $row['title'];
	}

	if ($id == $row['id']) {
		print "<tr valign=\"top\" style=\"background: #fa9;\">";
	} else {
		print "<tr valign=\"top\">";
	}
	print "<td style=\"text-align: right\"><a href=\"ticket.php?id={$row['id']}&amp;display=$display\">{$row['id']}</a></td>";
	print "<td>{$row['user_name']}</td>";
	print "<td>$label</td>";

	print "<td nowrap>{$row['status']}</td>";

	print "</tr>";
}
print '</table>';

print "</td><td>";


// Show single entry
if ($id) {
	$row = getrow("SELECT id, data_id, category, title, description, submittime, user_name, user_email, internal, status FROM updates WHERE id = '$id'") or die(dberror());
	if ($row['data_id'] && $row['category']) {
		$label = getlabel($row['category'], $row['data_id'], TRUE, $row['title']);
	} else {
		$label = $row['title'];
	}

	print "<form action=\"ticket.php\" method=\"post\">\n";
	print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
	print "<input type=\"hidden\" name=\"action\" value=\"update\">\n";
	print "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	print "<input type=\"hidden\" name=\"display\" value=\"$display\">\n";
	print "<table class=\"ticketlist\" style=\"border: 1px solid black;\" border=\"1\" cellspacing=\"0\">";
	print "<tr>";
	print "<td>ID: {$row['id']}</td>";
	print "<td>Correction for:<br>\n$label</td>";
	print "<td>Submitted by:<br>\n" . htmlspecialchars($row['user_name']) . " &lt;<a href=\"mailto:" . htmlspecialchars($row['user_email']) . "\">" . htmlspecialchars($row['user_email']) . "</a>&gt;</td>";
	print "<td>Received:<br>\n{$row['submittime']}</td>";
	print "</tr>";

	print '<tr style="background: #ffeebb"><th colspan="4">Comment:</th></tr>';

	print "<tr>";
	print "<td colspan=\"4\">\n";
	print nl2br(htmlspecialchars($row['description']));
	print "</td></tr>\n";

	print '<tr style="background: #ffeebb"><th colspan="4">Internal log:</th></tr>';

	print '<tr><td colspan="4"><textarea name="internal" rows="6" cols="60">' . htmlspecialchars($row['internal'] ?? '') . '</textarea></td></tr>';

	print "<tr><td colspan=\"4\">Status: <select name=\"status\">\n";
	foreach ($options as $option) {
		printf('<option value="%s" %s>%s</option>', $option, ($row['status'] == $option ? 'selected' : ''), ucfirst($option));
	}
	print "</select>\n";
	print "<input type=\"submit\" value=\"Update\"></td></tr>\n";

	print "</table>";
}

print "</td></tr></table>\n";

?>

</body>

</html>