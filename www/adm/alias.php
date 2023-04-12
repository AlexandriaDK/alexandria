<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'alias';

$action = (string) ($_REQUEST['action'] ?? '');
$do = (string) ($_REQUEST['do'] ?? '');
$label = trim((string) ($_REQUEST['label'] ?? ''));
$language = trim((string) ($_REQUEST['language'] ?? ''));
$visible = (string) ($_REQUEST['visible'] ?? '');
$visible = ($visible == "on" ? 1 : 0);
$id = (int) ($_REQUEST['id'] ?? '');
$data_id = (int) ($_REQUEST['data_id'] ?? '');
$category = (string) ($_REQUEST['category'] ?? '');

// Edit alias
if ($action == "changealias" && $do != "Delete") {
	$q = "UPDATE alias SET " .
		"label = '" . dbesc($label) . "', " .
		"language = '" . dbesc($language) . "', " .
		"visible = '$visible' " .
		"WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id, $category, "Alias updated");
	}
	$_SESSION['admin']['info'] = "Alias updated! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Delete alias
if ($action == "changealias" && $do == "Delete") {
	$q = "DELETE FROM alias WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
		chlog($data_id, $category, "Alias deleted");
	}
	$_SESSION['admin']['info'] = "Alias deleted! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Add alias
if ($action == "addalias") {
	$data_field = getFieldFromCategory($category);
	$q = "INSERT INTO alias " .
		"(`$data_field`, label, language, visible) VALUES " .
		"('$data_id', '" . dbesc($label) . "', '" . dbesc($language) . "', '$visible')";
	$r = doquery($q);
	if ($r) {
		$id = dbid();
		chlog($data_id, $category, "Alias created");
	}
	$_SESSION['admin']['info'] = "Alias created! " . dberror();
	rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

if ($data_id && $category) {
	$data_id = intval($data_id);
	$data_field = getFieldFromCategory($category);
	$linktitle = getlabel($category, $data_id, TRUE);

	$query = "SELECT id, label, language, visible FROM alias WHERE `$data_field` = '$data_id' ORDER BY id";
	$result = getall($query);
}

htmladmstart("Alias");

if ($data_id && $category) {

	print "<table align=\"center\" border=0>" .
		"<tr><th colspan=5>Edit aliases for: $linktitle</th></tr>\n" .
		"<tr>\n" .
		"<th>ID</th>" .
		"<th>Alias</th>" .
		"<th>Language code</th>" .
		"<th>Visible</th>" .
		"</tr>\n";

	foreach ($result as $row) {
		$selected = ($row['visible'] == 1 ? 'checked="checked"' : '');
		print '<form action="alias.php" method="post">' .
			'<input type="hidden" name="action" value="changealias">' .
			'<input type="hidden" name="data_id" value="' . $data_id . '">' .
			'<input type="hidden" name="category" value="' . htmlspecialchars($category) . '">' .
			'<input type="hidden" name="id" value="' . $row['id'] . '">';
		print "<tr>\n" .
			'<td style="text-align:right;">' . $row['id'] . '</td>' .
			'<td><input type="text" name="label" value="' . htmlspecialchars($row['label']) . '" size="40" maxlength="150"></td>' .
			'<td><input type="text" name="language" value="' . htmlspecialchars($row['language']) . '" size="2" maxlength="20" placeholder="da"></td>' .
			'<td><input type="checkbox" name="visible" ' . $selected . '></td>' .
			'<td><input type="submit" name="do" value="Update"></td>' .
			'<td><input type="submit" name="do" value="Delete"></td>' .
			"</tr>\n";
		print "</form>\n\n";
	}

	print '<form action="alias.php" method="post">' .
		'<input type="hidden" name="action" value="addalias">' .
		'<input type="hidden" name="data_id" value="' . $data_id . '">' .
		'<input type="hidden" name="category" value="' . htmlspecialchars($category) . '">';
	print "<tr>\n" .
		'<td style="text-align:right;">New</td>' .
		'<td><input type="text" name="label" value="" size="40" maxlength="150"></td>' .
		'<td><input type="text" name="language" value="" size="2" maxlength="20" placeholder="da"></td>' .
		'<td><input type="checkbox" name="visible"></td>' .
		'<td colspan=2><input type="submit" name="do" value="Create"></td>' .
		"</tr>\n";
	print "</form>\n\n";

	print "</table>\n";
} else {
	print "Error: No data id provided.";
}
print "</body>\n</html>\n";
