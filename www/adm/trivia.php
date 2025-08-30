<?php
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";
$this_type = 'trivia';

$action = $_REQUEST['action'] ?? '';
$do = $_REQUEST['do'] ?? '';
$fact = $_REQUEST['fact'] ?? '';
$internal = $_REQUEST['internal'] ?? '';
$id = (int) ($_REQUEST['id'] ?? 0);
$data_id = $_REQUEST['data_id'] ?? '';
$category = $_REQUEST['category'] ?? '';

// Ret trivia
if ($action == "changetrivia" && $do != "Delete") {
  $fact = trim($fact);
  $internal = trim($internal);
  $q = "UPDATE trivia SET " .
    "fact = '" . dbesc($fact) . "', " .
    "internal = '" . dbesc($internal) . "' " .
    "WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    chlog($data_id, $category, "Trivia updated");
  }
  $_SESSION['admin']['info'] = "Trivia updated! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Slet trivia
if ($action == "changetrivia" && $do == "Delete") {
  $q = "DELETE FROM trivia WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    chlog($data_id, $category, "Trivia deleted");
  }
  $_SESSION['admin']['info'] = "Trivia deleted! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Tilføj trivia
if ($action == "addtrivia") {
  $fact = trim($fact);
  $internal = trim($internal);
  $data_field = getFieldFromCategory($category);
  $q = "INSERT INTO trivia " .
    "($data_field, fact, internal) VALUES " .
    "('$data_id', '" . dbesc($fact) . "', '" . dbesc($internal) . "')";
  $r = doquery($q);
  if ($r) {
    $id = dbid();
    chlog($data_id, $category, "Trivia created");
  }
  $_SESSION['admin']['info'] = "Trivia created! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

if ($data_id && $category) {
  $data_id = intval($data_id);
  $data_field = getFieldFromCategory($category);
  $linktitle = getlabel($category, $data_id, true);

  $query = "SELECT id, fact, internal FROM trivia WHERE `$data_field` = '$data_id' ORDER BY id";
  $result = getall($query);
}
htmladmstart("Trivia");

if ($data_id && $category) {

  print "<table align=\"center\" border=0>" .
    "<tr><th colspan=5>Edit trivia for: $linktitle</th></tr>\n" .
    "<tr>\n" .
    "<th>ID</th>" .
    "<th>Trivia</th>" .
    "<th>Internal note</th>" .
    "</tr>\n";

  foreach ($result as $row) {
    print '<form action="trivia.php" method="post">' .
      '<input type="hidden" name="action" value="changetrivia">' .
      '<input type="hidden" name="data_id" value="' . $data_id . '">' .
      '<input type="hidden" name="category" value="' . htmlspecialchars($category) . '">' .
      '<input type="hidden" name="id" value="' . $row['id'] . '">';
    print "<tr valign=\"top\">\n" .
      '<td style="text-align:right;">' . $row['id'] . '</td>' .
      '<td><textarea cols=40 rows=3 name="fact">' . htmlspecialchars($row['fact']) . '</textarea></td>' .
      '<td><textarea cols=40 rows=3 name="internal">' . htmlspecialchars($row['internal']) . '</textarea></td>' .
      '<td><input type="submit" name="do" value="Save"></td>' .
      '<td><input type="submit" name="do" value="Delete"></td>' .
      "</tr>\n";
    print "</form>\n\n";
  }

  print '<form action="trivia.php" method="post">' .
    '<input type="hidden" name="action" value="addtrivia">' .
    '<input type="hidden" name="data_id" value="' . $data_id . '">' .
    '<input type="hidden" name="category" value="' . htmlspecialchars($category) . '">';
  print "<tr valign=\"top\">\n" .
    '<td style="text-align:right;">New</td>' .
    '<td><textarea cols=40 rows=3 id="newfact" name="fact"></textarea></td>' .
    '<td><textarea cols=40 rows=3 name="internal"></textarea></td>' .
    '<td colspan=2><input type="submit" name="do" value="Create"></td>' .
    "</tr>\n";
  print "</form>\n\n";

  print "</table>";
} else {
  print "Error: No data id.";
}
print "</body>\n</html>\n";
