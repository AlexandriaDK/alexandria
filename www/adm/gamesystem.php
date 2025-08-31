<?php
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";
$this_type = 'gamesystem';

$gamesystem = (int) ($_REQUEST['gamesystem'] ?? '');
$action = (string) ($_REQUEST['action'] ?? '');
$name = (string) ($_REQUEST['name'] ?? '');
$description = (string) ($_REQUEST['description'] ?? '');

$this_id = $gamesystem;

if ($action) {
  validatetoken($token);
}


if (!$action && $gamesystem) {
  list($id, $name, $description) = getrow("SELECT id, name, description FROM gamesystem WHERE id = '$gamesystem'");
}

if ($action == "edit" && $gamesystem) {
  $name = trim($name);
  if (!$name) {
    $_SESSION['admin']['info'] = "Name is missing!";
  } else {
    $q = "UPDATE gamesystem SET " .
      "name = '" . dbesc($name) . "', " .
      "description = '" . dbesc($description) . "' " .
      "WHERE id = '$gamesystem'";
    $r = doquery($q);
    if ($r) {
      chlog($gamesystem, $this_type, "System edited");
    }
    $_SESSION['admin']['info'] = "System edited! " . dberror();
    rexit($this_type, ['gamesystem' => $gamesystem]);
  }
}

if ($action == "create") {
  $name = trim($name);
  $rid = getone("SELECT id FROM gamesystem WHERE name = '$name'");
  if ($rid) {
    $_SESSION['admin']['info'] = "A system with this name already exists!";
    $_SESSION['admin']['link'] = "gamesystem.php?gamesystem=" . $rid;
  } elseif (!$name) {
    $_SESSION['admin']['info'] = "Name is missing!";
  } else {
    $q = "INSERT INTO gamesystem (name, description) " .
      "VALUES ( " .
      "'" . dbesc($name) . "', " .
      "'" . dbesc($description) . "' " .
      ")";
    $r = doquery($q);
    if ($r) {
      $gamesystem = dbid();
      chlog($gamesystem, $this_type, "System created");
    }
    $_SESSION['admin']['info'] = "System created! " . dberror();
    rexit($this_type, ['gamesystem' => $gamesystem]);
  }
}

if ($action == "Delete" && $gamesystem) {
  $error = [];
  if (getCount('game', $this_id, false, $this_type)) $error[] = "game";
  if (getCount('article_reference', $this_id, false, $this_type)) $error[] = "article reference";
  if ($error) {
    $_SESSION['admin']['info'] = "Can't delete. The tag still has relations: " . implode(", ", $error);
    rexit($this_type, ['gamesystem' => $gamesystem]);
  } else {
    $name = getone("SELECT name FROM gamesystem WHERE id = $this_id");

    $q = "DELETE FROM gamesystem WHERE id = $gamesystem";
    $r = doquery($q);

    if ($r) {
      chlog($this_id, $this_type, "System deleted: $name");
    }
    $_SESSION['admin']['info'] = "System deleted! " . dberror();
    rexit($this_type);
  }
}
htmladmstart("Game system");

print "<form action=\"gamesystem.php\" method=\"post\">\n";
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
if (!$gamesystem) print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"create\">\n";
else {
  print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"edit\">\n";
  print "<INPUT TYPE=\"hidden\" name=\"gamesystem\" value=\"$gamesystem\">\n";
}

print "<a href=\"gamesystem.php\">New system</a>";

print "<table border=0>\n";

if ($gamesystem) {
  print "<tr><td>ID</td><td>$gamesystem - <a href=\"../data?system=$gamesystem\" accesskey=\"q\">Show RPG system page</a>";
  if ($viewlog == true) {
    print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$gamesystem\">Show log</a>";
  }
  print "\n</td></tr>\n";
}

tr("Name", "name", $name, "", "", "text", true, true);
print "<tr valign=top><td>Description</td><td><textarea name=description cols=60 rows=8>\n" . stripslashes(htmlspecialchars($description)) . "</textarea></td></tr>\n";


print '<tr><td>&nbsp;</td><td><input type="submit" value="' . ($gamesystem ? "Update" : "Create") . ' system">' . ($gamesystem ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete system?\n\nAs a safety precaution all relations will be checked.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

if ($gamesystem) {
  print changelinks($gamesystem, $this_type);
  print changetrivia($gamesystem, $this_type);
  print changealias($gamesystem, $this_type);
  print changefiles($gamesystem, $this_type);
  print showpicture($gamesystem, $this_type);
  print showtickets($gamesystem, $this_type);

  $q = getall("SELECT id, title FROM game WHERE gamesystem_id = '$gamesystem' ORDER BY title, id");
  print "<tr valign=top><td align=right>Contains the following<br>scenarios</td><td>\n";
  foreach ($q as list($id, $title)) {
    print "<a href=\"game.php?game=$id\">$title</a><br>";
  }
  if (!$q) print "[None]";
  print "</td></tr>\n";
}

?>

</table>

</form>

<hr size=1>

<form action="gamesystem.php" method="get">
  <table>
    <tr valign="baseline">
      <td>
        <b>Choose system</b>
      </td>

      <td>
        <select name="gamesystem">

          <?php
          $q = getall("SELECT id, name FROM gamesystem ORDER BY name");
          foreach ($q as $r) {
            print "<option value=$r[id]";
            if ($r['id'] == $gamesystem) print " SELECTED";
            print ">$r[name]\n";
          }
          ?>
        </select>
        <br>
        <input type=submit value="Edit">

      </td>
    </tr>
  </table>
</form>

</body>

</html>