<?php
$admonly = TRUE;
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'users';

$action = $_REQUEST['action'];
$aut_id = (int) $_REQUEST['aut_id'];
$id = (int) $_REQUEST['id'];
$name = $_REQUEST['name'];
$do = $_REQUEST['do'];
$order = (string) $_REQUEST['order'];
$editor = (int) isset($_REQUEST['editor']);
$user_id = (int) $_REQUEST['user_id'];
$asked = (int) $_REQUEST['asked'];
$beer = (int) $_REQUEST['beer'];
$elite = (int) $_REQUEST['elite'];
$brother = (int) $_REQUEST['brother'];
$talk = (int) $_REQUEST['talk'];
$achievement_id = (int) $_REQUEST['achievement_id'];

if ( $action ) {
	validatetoken( $token );
}

// Add manual achievements (asked, beer, ...)
if ($user_id && $asked) {
	award_user_achievement($user_id, 72);
	header("Location: users.php");
	exit;
} elseif ($user_id && $beer) {
	award_user_achievement($user_id, 75);
	header("Location: users.php");
	exit;
} elseif ($user_id && $elite) {
	award_user_achievement($user_id, 93);
	header("Location: users.php");
	exit;
} elseif ($user_id && $brother) {
	award_user_achievement($user_id, 94);
	header("Location: users.php");
	exit;
} elseif ($user_id && $talk) {
	award_user_achievement($user_id, 103);
	header("Location: users.php");
	exit;
}

// Ret achievement
if ($action == "update") {
	if (!$aut_id) $aut_id = 'NULL';
	$q = "UPDATE users SET " .
	     "name = '" . dbesc($name) . "', " .
	     "aut_id = $aut_id, " .
	     "editor = $editor " .
	     "WHERE id = '$id'";
	$r = doquery($q);
	if ($r) {
//		chlog($id,$this_type,"Link rettet");
	}
	$_SESSION['admin']['info'] = "User updated! " . dberror();
	rexit($this_type, ['order' => $order ] );
}

if (!$order) $order = 'lastactive';

if ($order == 'lastlogin') {
	$orderby = 'last_login DESC, a.id';
} elseif ($order == 'lastactive') {
	$orderby = 'last_active DESC, last_login DESC, a.id';
} elseif ($order == 'logincount') {
	$orderby = 'login_count DESC, last_login DESC, a.id';
} elseif ($order == 'name') {
	$orderby = 'a.name';
} elseif ($order == 'id') {
	$orderby = 'a.id';
} elseif ($order == 'iddesc') {
	$orderby = 'a.id DESC';
} elseif ($order == 'achievements') {
	$orderby = 'achievements DESC, a.last_login DESC, a.id';
} elseif ($order == 'editor') {
	$orderby = 'editor DESC, a.id';
} else {
	$orderby = 'a.id';
}

if ($achievement_id) {
	$query = "SELECT a.id, a.name, a.aut_id, a.editor, a.last_login, a.last_active, a.login_days_in_row, a.login_count, SUM(b.achievement_id = 72) AS asking, SUM(b.achievement_id = 75) AS beer, SUM(b.achievement_id = 93) AS elite, SUM(b.achievement_id = 94) AS brother, SUM(b.achievement_id = 103) AS talk, COUNT(b.id) AS achievements FROM users a INNER JOIN user_achievements b ON a.id = b.user_id WHERE b.achievement_id = $achievement_id GROUP BY a.id ORDER BY $orderby";
	$label = getone("SELECT label FROM achievements WHERE id = $achievement_id");
} else {
	$query = "SELECT a.id, a.name, a.aut_id, a.editor, a.last_login, a.last_active, a.login_days_in_row, a.login_count, SUM(b.achievement_id = 72) AS asking, SUM(b.achievement_id = 75) AS beer, SUM(b.achievement_id = 93) AS elite, SUM(b.achievement_id = 94) AS brother, SUM(b.achievement_id = 103) AS talk, COUNT(b.id) AS achievements FROM users a LEFT JOIN user_achievements b ON a.id = b.user_id GROUP BY a.id ORDER BY $orderby";

}
$result = getall($query);

$userlogins = getcolid("SELECT user_id, COUNT(*) AS logins FROM loginmap GROUP BY user_id");

htmladmstart("Users");

if ($info) {
	print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}
if ($label) {
	print "<h2 style=\"text-align: center\">" . htmlspecialchars($label) . "</h2>";
}

print "<table align=\"center\" border=0>".
      "<tr><th colspan=5>Users:</th></tr>\n".
      "<tr>\n".
      "<th><a href=\"users.php?order=" . ($order == 'iddesc' ? 'id' : 'iddesc') ."\">ID</a></th>".
      "<th><a href=\"users.php?order=name\">Name</a></th>".
      "<th>Author ID</th>".
      "<th><a href=\"users.php?order=editor\">Editor</a></th>".
      "<th>Accounts</th>".
      "<th><a href=\"users.php?order=logincount\">Logins</a></th>".
      "<th><a href=\"users.php?order=lastlogin\">Last login</a></th>".
      "<th><a href=\"users.php?order=lastactive\">Last active</a></th>".
      "<th><a href=\"users.php?order=achievements\">Achievements</a></th>".
      "<th>Asked</th>".
      "<th>Beer</th>".
      "<th>Elite</th>".
      "<th>Brother</th>".
      "<th>Talk</th>".
      "</tr>\n";

foreach($result AS $row) {
	$accounts = (int) $userlogins[$row['id']];
	print '<form action="users.php?order=' . $order . '" method="post">'.
			'<input type="hidden" name="token" value="' . $_SESSION['token'] . '">' .
			'<input type="hidden" name="action" value="update">'.
			'<input type="hidden" name="id" value="'.$row['id'].'">' . PHP_EOL;
	print "<tr" . ($accounts === 0 ? ' class="zerousermap" title="No login accounts are associated with this user"' : '') . ">\n".
			'<td style="text-align:right;"><a href="fbgraph.php?user_id=' . $row['id'] . '">'.$row['id'].'</a></td>'.
			'<td><input type="text" name="name" value="'.htmlspecialchars($row['name']).'" size=50 maxlength=100></td>'.
			'<td><input type="number" name="aut_id" value="'.htmlspecialchars($row['aut_id']).'" size="6"></td>'.
			'<td align="center"><input type="checkbox" name="editor" value="yes" ' . ($row['editor'] ? 'checked' : '' ) . '></td>'.
			'<td align="right" class="accounts">' . $accounts . '</td>'.
			'<td align="right" title="' . $row['login_days_in_row'] . '">' . $row['login_count'] . '</td>'.
			'<td align="right">'.($row['last_login'] ? pubdateprint($row['last_login']) : '-') . '</td>'.
			'<td align="right">'.($row['last_active'] ? pubdateprint($row['last_active']) : '-') . '</td>'.
			'<td align="right"><a href="achievements.php?user_id=' . $row['id'] . '">'. $row['achievements'] . '</a></td>'.
			'<td align="center">'.($row['asking'] ? 'Yes' : '<b><a href="users.php?asked=1&amp;user_id=' . $row['id'] . '">No</b>').'</td>'.
			'<td align="center">'.($row['beer'] ? 'Yes' : '<b><a href="users.php?beer=1&amp;user_id=' . $row['id'] . '">No</b>').'</td>'.
			'<td align="center">'.($row['elite'] ? 'Yes' : '<b><a href="users.php?elite=1&amp;user_id=' . $row['id'] . '">No</b>').'</td>'.
			'<td align="center">'.($row['brother'] ? 'Yes' : '<b><a href="users.php?brother=1&amp;user_id=' . $row['id'] . '">No</b>').'</td>'.
			'<td align="center">'.($row['talk'] ? 'Yes' : '<b><a href="users.php?talk=1&amp;user_id=' . $row['id'] . '">No</b>').'</td>'.
			'<td><input type="submit" name="do" value="Ret"></td>'.
			"\n</tr>\n";
	print "</form>\n\n";
}


print "</table>\n";
print "</body>\n</html>\n";

?>
