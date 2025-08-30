<?php
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";

$this_type = 'organizers';
$category = 'convention';

$action = $_REQUEST['action'] ?? false;
$do = $_REQUEST['do'] ?? false;
$role = trim((string) ($_REQUEST['role'] ?? ''));
$person_text = trim((string) ($_REQUEST['person_text'] ?? ''));
$person_id = (int) ($person_text ?? false);
$person_extra = "";
if (!$person_id) {
  $person_extra = $person_text;
  $person_id = null;
}

$id = $_REQUEST['id'] ?? false;
$data_id = (int) ($_REQUEST['data_id'] ?? '');

$user_id = $_SESSION['user_id'];

$people = [];
$r = getall("SELECT id, firstname, surname FROM person ORDER BY firstname, surname");
foreach ($r as $row) {
  $people[] = $row['id'] . " - " . $row['firstname'] . " " . $row['surname'];
}

// Update organizer
if ($action == "changeorganizer" && $do != "Delete") {

  $q = "UPDATE pcrel SET " .
    "person_id = " . strNullEscape($person_id) . ", " .
    "person_extra = '" . dbesc($person_extra) . "', " .
    "role = '" . dbesc($role) . "', " .
    "added_by_user_id = $user_id " .
    "WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    if ((int) $person_id) {
      chlog($data_id, $category, "Organizer updated: $person_id");
    } else {
      chlog($data_id, $category, "Organizer updated: $person_extra");
    }
  }
  $_SESSION['admin']['info'] = "Organizer updated! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Delete organizer
if ($action == "changeorganizer" && $do == "Delete") {
  $q = "DELETE FROM pcrel WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    chlog($data_id, $category, "Organizer removed");
  }
  $_SESSION['admin']['info'] = "Organizer removed! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Add organizer
if ($action == "addorganizer" && ($person_id || $person_extra || $role)) {
  $q = "INSERT INTO pcrel " .
    "(person_id, person_extra, convention_id, role, added_by_user_id) VALUES " .
    "(" . strNullEscape($person_id) . ", '" . dbesc($person_extra) . "',  $data_id, '" . dbesc($role) . "', " . $_SESSION['user_id'] . ")";
  $r = doquery($q);
  if ($r) {
    $id = dbid();
    if ((int) $person_id) {
      chlog($data_id, $category, "Organizer added: $person_id");
    } else {
      chlog($data_id, $category, "Organizer added: $person_extra");
    }
  }
  $_SESSION['admin']['info'] = "Organizer added! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}


if ($data_id) {
  $cat = 'convention';
  $q = "SELECT CONCAT(name, ' (', COALESCE(year,'?'), ')') FROM convention WHERE id = '$data_id'";
  $mainlink = "convention.php?con=$data_id";

  $title = getone($q);

  $query = "SELECT a.id, a.person_id, a.person_extra, CONCAT(b.firstname, ' ', b.surname) AS fullname, a.role FROM pcrel a LEFT JOIN person b ON a.person_id = b.id WHERE convention_id = $data_id ORDER BY id";
  $result = getall($query);
}

?>
<!DOCTYPE html>
<HTML>

<HEAD>
  <TITLE>Administration - organizers</TITLE>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="/uistyle.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <link rel="icon" type="image/png" href="/gfx/favicon_ti_adm.png">
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="adm.js"></script>
  <script type="text/javascript">
    $(function() {
      $(".peopletags").autocomplete({
        source: 'lookup.php?type=person',
        autoFocus: true,
        delay: 50,
        minLength: 3
      });
    });
  </script>
  <style type="text/css">
    .ui-autocomplete {
      max-height: 300px;
      overflow-y: auto;
      /* prevent horizontal scrollbar */
      overflow-x: hidden;
      font-size: 0.7em;
    }
  </style>


</HEAD>

<body>
  <?php
  include("links.inc.php");

  printinfo();

  print "<table align=\"center\" border=0>" .
    "<tr><th colspan=5>Update organizer for: <a href=\"$mainlink\" accesskey=\"q\">$title</a></th></tr>\n" .
    "<tr>\n" .
    "<th>ID</th>" .
    "<th>Role</th>" .
    "<th>Person</th>" .
    "</tr>\n";

  if ($result) {
    foreach ($result as $row) {
      $person_text = "";
      if ($row['person_id']) $person_text .= $row['person_id'] . " - ";
      if ($row['fullname']) $person_text .= $row['fullname'];
      if ($row['person_extra']) $person_text .= $row['person_extra'];
      print '<form action="organizers.php" method="post">' .
        '<input type="hidden" name="action" value="changeorganizer">' .
        '<input type="hidden" name="data_id" value="' . $data_id . '">' .
        '<input type="hidden" name="id" value="' . $row['id'] . '">';
      print "<tr>\n" .
        '<td style="text-align:right;">' . $row['id'] . '</td>' .
        '<td><input type="text" name="role" value="' . htmlspecialchars($row['role']) . '" size=40 maxlength=100></td>' .
        '<td><input type="text" name="person_text" value="' . htmlspecialchars($person_text) . '" size=40 maxlength=100 class="peopletags"></td>' .
        '<td><input type="submit" name="do" value="Update"></td>' .
        '<td><input type="submit" name="do" value="Delete"></td>' .
        "</tr>\n";
      print "</form>\n\n";
    }
  }

  print '<form action="organizers.php" method="post">' .
    '<input type="hidden" name="action" value="addorganizer">' .
    '<input type="hidden" name="data_id" value="' . $data_id . '">';
  print "<tr>\n" .
    '<td style="text-align: right;">New</td>' .
    '<td><input type="text" name="role" value="" size=40 maxlength=100 autofocus></td>' .
    '<td><input type="text" name="person_text" value="" size=40 maxlength=100 class="peopletags"></td>' .
    '<td colspan=2><input type="submit" name="do" value="Add"></td>' .
    "</tr>\n";
  print "</form>\n\n";

  print "</table>\n";
  print "</body>\n</html>\n";

  ?>