<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'rss';

$action = (string) $_REQUEST['action'];
$url = (string)  $_REQUEST['url'];
$pageurl = (string)  $_REQUEST['pageurl'];
$person_id = (int) $_REQUEST['person_id'];
$id = (int) $_REQUEST['id'];
$owner = (string) $_REQUEST['owner'];
$name = (string)  $_REQUEST['name'];
$do = (string)  $_REQUEST['do'];

if ($action) {
  validatetoken($token);
}

// Edit link
if ($action == "changelink" && $do != "Slet") {
  $url = trim($url);
  $pageurl = trim($pageurl);
  $owner = trim($owner);
  $name = trim($name);
  $q = "UPDATE feeds SET " .
    "url = '$url', " .
    "pageurl = '$pageurl', " .
    "owner = '$owner', " .
    "name = '$name', " .
    "person_id = " . sqlifnull($person_id) . " " .
    "WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    //		chlog($id,$this_type,"Link rettet");
  }
  $info = "Feed updated! " . dberror();
}

// Empty feed
if ($action == "changelink" && $do == "Empty") {
  $q = "DELETE FROM feedcontent WHERE feed_id = '$id'";
  $r = doquery($q);
  $info = "Feed emptied! " . dberror();
  if ($r) {
    //		chlog($id,$this_type,"Link slettet");
  }
}

// Delete feed
if ($action == "changelink" && $do == "Delete") {
  $q = "DELETE FROM feedcontent WHERE feed_id = '$id'";
  $r = doquery($q);
  $q = "DELETE FROM feeds WHERE id = '$id'";
  $r = doquery($q);
  $info = "Feed deleted! " . dberror();
  if ($r) {
    //		chlog($id,$this_type,"Link slettet");
  }
}

// Add feed
if ($action == "addlink") {
  $url = trim($url);
  $owner = trim($owner);
  $name = trim($name);
  $pageurl = trim($pageurl);
  $q = "INSERT INTO feeds " .
    "(url, owner, name, pageurl, person_id) VALUES " .
    "('$url', '$owner', '$name', '$pageurl', " . sqlifnull($person_id) . ")";
  $r = doquery($q);
  if ($r) {
    $id = dbid();
    //		chlog($id,$this_type,"Link oprettet");
  }
  $info = "Feed created! " . dberror();
}

$query = "SELECT a.id, a.url, a.owner, a.name, a.pageurl, a.person_id, COUNT(b.id) AS count FROM feeds a LEFT JOIN feedcontent b ON a.id = b.feed_id GROUP BY a.id ORDER BY a.id";
$result = getall($query);

htmladmstart("Feeds");

if ($info) {
  print "<table border=0><tr><td bgcolor=\"#ffbb88\"><font size=\"+1\">$info</font></td></tr></table>\n";
}

print "<table align=\"center\" border=0>" .
  "<tr><th colspan=7>Edit feeds:</th></tr>\n" .
  "<tr>\n" .
  "<th>ID</th>" .
  "<th colspan='2'>URL</th>" .
  "<th>Owner</th>" .
  "<th>Name</th>" .
  "<th>Count</th>" .
  "</tr>\n";

foreach ($result as $row) {
  print '<form action="feeds.php" method="post">' .
    '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">' .
    '<input type="hidden" name="action" value="changelink">' .
    '<input type="hidden" name="id" value="' . $row['id'] . '">';
  print "<tr valign='top'>\n" .
    '<td style="text-align:right;">' . $row['id'] . '</td>' .
    '<td>Page:<br />Feed:</td>' .
    '<td><input type="text" name="pageurl" value="' . htmlspecialchars($row['pageurl']) . '" size=50 maxlength=100><br /><input type="text" name="url" value="' . htmlspecialchars($row['url']) . '" size=50 maxlength=100></td>' .
    '<td><input type="text" name="owner" value="' . htmlspecialchars($row['owner']) . '" size=30 maxlength=100><br /><input type="text" name="person_id" value="' . htmlspecialchars($row['person_id']) . '" size=4 maxlength=10></td>' .
    '<td><input type="text" name="name" value="' . htmlspecialchars($row['name']) . '" size=20 maxlength=100></td>' .
    '<td>' . htmlspecialchars($row['count']) . '</td>' .
    '<td><input type="submit" name="do" value="Update"></td>';
  if ($_SESSION['user_admin']) {
    print
      '<td><input type="submit" name="do" value="Empty"></td>' .
      '<td><input type="submit" name="do" value="Delete"></td>';
  }
  print "</tr>\n";
  print "</form>\n\n";
}

print '<form action="feeds.php" method="post">' .
  '<input type="hidden" name="action" value="addlink">';
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
print "<tr>\n" .
  '<td style="text-align:right;">New</td>' .
  '<td>Page:<br />Feed:</td>' .
  '<td><input type="text" name="pageurl" value="" size=50 maxlength=100 placeholder="URL for blog web page"><br /><input type="text" name="url" value="" size=50 maxlength=100 placeholder="Direct URL to RSS"></td>' .
  '<td><input type="text" name="owner" value="" size=20 maxlength=100 placeholder="Owner name"><br /><input type="text" name="person_id" value="" size=4 maxlength=10 placeholder="User ID (leave blank)"></td>' .
  '<td><input type="text" name="name" value="" size=20 maxlength=100 placeholder="Name of blog"></td>' .
  '<td></td>' .
  '<td colspan=3><input type="submit" name="do" value="Create"></td>' .
  "</tr>\n";
print "</form>\n\n";


print "</table>\n";
print "</body>\n</html>\n";
