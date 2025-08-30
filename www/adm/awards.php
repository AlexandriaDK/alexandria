<?php
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";

$this_type = 'awards';

$category = (string) ($_REQUEST['category'] ?? '');
$action = (string) ($_REQUEST['action'] ?? '');
$do = (string) ($_REQUEST['do'] ?? '');
$name = trim((string) ($_REQUEST['name'] ?? ''));
$description = (string) ($_REQUEST['description'] ?? '');
$nominationtext = trim((string) ($_REQUEST['nominationtext'] ?? ''));
$id = (int) ($_REQUEST['id'] ?? false);
$game_id = (int) ($_REQUEST['game_id'] ?? false);
if (!$game_id) {
  $game_id = null;
}
$award_nominee_entity = (int) ($_REQUEST['award_nominee_entity'] ?? '');
$award_nominee_entity_extra = $_REQUEST['award_nominee_entity'] ?? '';
$data_id = (int) ($_REQUEST['data_id'] ?? false);
$convention_id = (int) ($_REQUEST['convention_id'] ?? false);
$tag_id = (int) ($_REQUEST['tag_id'] ?? false);
$winner = (int) isset($_REQUEST['winner']);
$ranking = (string) ($_REQUEST['ranking'] ?? '');

if ($category == 'convention') {
  $type = 'convention';
  $type_id = $data_id;
} elseif ($category == 'tag') {
  $type = 'tag';
  $type_id = $data_id;
  if (!$type_id) {
    $type_id = $tag_id;
  }
} else {
  $type = $convention_id ? 'convention' : 'tag';
  $type_id = $convention_id ? $convention_id : $tag_id;
}

$user_id = $_SESSION['user_id'];

// Edit category
if ($action == "changecategory" && $do != "Delete") {
  $q = "UPDATE award_categories SET " .
    "name = '" . dbesc($name) . "', " .
    "description = '" . dbesc($description) . "' " .
    "WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    chlog($type_id, $type, "Award updated: $id, $name");
  }
  $_SESSION['admin']['info'] = "Award updated! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Delete category
if ($action == "changecategory" && $do == "Delete") {
  // Only delete category if no nominees
  $q = "SELECT COUNT(*) FROM award_nominees where award_category_id = $id";
  $r = getone($q);
  if ($r != 0) {
    $_SESSION['admin']['info'] = "The category needs to be empty before it can be removed! " . dberror();
    rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
  }

  $q = "DELETE FROM award_categories WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    chlog($type_id, $type, "Category removed: $id");
  }
  $_SESSION['admin']['info'] = "Category removed! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Add category
if ($action == "addcategory") {
  $type_field = 'convention_id';
  if ($category == 'tag') {
    $type_field = 'tag_id';
  }
  $q = "INSERT INTO award_categories " .
    "(name, description, $type_field) VALUES " .
    "('" . dbesc($name) . "', '" . dbesc($description) . "', " . $data_id . ")";
  $r = doquery($q);
  if ($r) {
    $id = dbid();
    chlog($type_id, $type, "Award created: $name");
  }
  $_SESSION['admin']['info'] = "Award created! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Edit nominee
if ($action == "changenominee" && $do != "Delete") {
  $award_name = getone("SELECT name FROM award_categories WHERE id = $data_id");
  $q = "UPDATE award_nominees SET " .
    "name= '" . dbesc($name) . "', " .
    "game_id = " . strNullEscape($game_id) . ", " .
    "nominationtext = '" . dbesc($nominationtext) . "', " .
    "winner = $winner, " .
    "ranking = '" . dbesc($ranking) . "' " .
    "WHERE id = '$id'";
  if ($award_nominee_entity) { // assuming person
    doquery("INSERT INTO award_nominee_entities (award_nominee_id, person_id) VALUES ($id, $award_nominee_entity)");
  } elseif ($award_nominee_entity_extra) { // Label
    doquery("INSERT INTO award_nominee_entities (award_nominee_id, label) VALUES ($id, '" . dbesc($award_nominee_entity_extra) . "')");
  }
  $r = doquery($q);
  if ($r) {
    chlog($type_id, $type, "Nominee updated: $name ($data_id), $award_name");
  }
  $_SESSION['admin']['info'] = "Nominee updated! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Add nominee
if ($action == "addnominee") {
  $award_name = getone("SELECT name FROM award_categories WHERE id = $data_id");
  $q = "INSERT INTO award_nominees " .
    "(award_category_id, game_id, name, nominationtext, winner, ranking) VALUES " .
    "($data_id, " . strNullEscape($game_id) . ", '" . dbesc($name) . "', '" . dbesc($nominationtext) . "', " . $winner . ", '" . dbesc($ranking) . "')";
  $r = doquery($q);
  if ($r) {
    $id = dbid();
    chlog($type_id, $type, "Nominee added: $name, $award_name");
  }
  $_SESSION['admin']['info'] = "Nominee added! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

// Remove nominee
if ($action == "changenominee" && $do == "Delete") {
  // Only delete if there are no nominees
  $num_childs = getone("SELECT COUNT(*) FROM award_nominee_entities where award_nominee_id = $id");
  if ($num_childs != 0) {
    $_SESSION['admin']['info'] = "The nominee must have all entities removed, before it can be deleted! " . dberror();
    rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
  }

  $q = "DELETE FROM award_nominees WHERE id = '$id'";
  $r = doquery($q);
  if ($r) {
    chlog($type_id, $type, "Nominee removed: $id");
  }
  $_SESSION['admin']['info'] = "Nominee removed! " . dberror();
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}

if ($action == 'deletenomineeentity') {
  if (getone("SELECT id FROM award_nominee_entities WHERE id = $id")) {
    doquery("DELETE FROM award_nominee_entities WHERE id = $id");
    chlog($type_id, $type, "Nominee connection deleted: $id");
    $_SESSION['admin']['info'] = "Connection removed! " . dberror();
  } else {
    $_SESSION['admin']['info'] = "Could not find connection! " . dberror();
  }
  rexit($this_type, ['category' => $category, 'data_id' => $data_id]);
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Administration - Awards</title>
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
      $(".gametags").autocomplete({
        source: 'lookup.php?type=game',
        autoFocus: true,
        delay: 50,
        minLength: 3
      });
    });
  </script>
</head>

<body>
  <?php
  include("links.inc.php");

  printinfo();

  if ($category == 'convention' || $category == 'tag') {
    $type_field = 'convention_id';
    // get data
    if ($category == 'convention') {
      $title = getone("SELECT CONCAT(name, ' (', year, ')') FROM convention WHERE id = $data_id");
      $mainlink = "convention.php?con=$data_id";
      $type_field = 'convention_id';
    } else {
      $title = getone("SELECT tag FROM tag WHERE id = $data_id");
      $mainlink = "tag.php?tag_id=$data_id";
      $type_field = 'tag_id';
    }

    $query = "SELECT a.id, a.name, a.description, SUM(b.winner = 1) AS winners, COUNT(b.id) AS nominees FROM award_categories a LEFT JOIN award_nominees b ON a.id = b.award_category_id WHERE `$type_field` = $data_id GROUP BY a.id";
    $result = getall($query);

    print "<table align=\"center\" border=0>" .
      "<tr><th colspan=5>Edit awards for: <a href=\"$mainlink\" accesskey=\"q\">" . htmlspecialchars($title) . "</a></th></tr>\n" .
      "<tr>\n" .
      "<th>ID</th>" .
      "<th>Award</th>" .
      "<th>Description</th>" .
      "</tr>\n";

    if ($result) {
      foreach ($result as $row) {
        print '<form action="awards.php" method="post">' .
          '<input type="hidden" name="action" value="changecategory">' .
          '<input type="hidden" name="category" value="' . $category . '">' .
          '<input type="hidden" name="data_id" value="' . $data_id . '">' .
          '<input type="hidden" name="id" value="' . $row['id'] . '">';
        print "<tr valign=\"top\">\n" .
          '<td style="text-align:right;">' . $row['id'] . '</td>' .
          '<td><input type="text" name="name" value="' . htmlspecialchars($row['name']) . '" size=40 maxlength=150><br><a href="awards.php?category=awardcategory&amp;data_id=' . $row['id'] . '">' . ($row['winners'] == 1 ? "1 winner" : (int) $row['winners'] . " winners") . " / " . ($row['nominees'] == 1 ? "1 nominee" : $row['nominees'] . " nominees") . '</a></td>' .
          '<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;">' . htmlspecialchars($row['description'] ?? '') . '</textarea></td>' .
          '<td><input type="submit" name="do" value="Update"> ' .
          ($row['nominees'] == 0 ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove award?\');">' : '') . '</td>' .
          "</tr>\n";
        print "</form>\n\n";
      }
    }
    print '<form action="awards.php" method="post">' .
      '<input type="hidden" name="action" value="addcategory">' .
      '<input type="hidden" name="category" value="' . $category . '">' .
      '<input type="hidden" name="data_id" value="' . $data_id . '">';
    print "<tr valign=\"top\">\n" .
      '<td style="text-align:right;">New</td>' .
      '<td><input type="text" name="name" value="" size=40 maxlength=150 autofocus></td>' .
      '<td><textarea name="description" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;"></textarea></td>' .
      '<td colspan=2><input type="submit" name="do" value="Create"></td>' .
      "</tr>\n";
    print "</form>\n\n";
    print "</table>" . PHP_EOL;
  } elseif ($category == "awardcategory" && $data_id) {
    // get category
    list($category_id, $name, $convention_id, $convention_name, $year, $tag_id, $tag_name) = getrow("
			SELECT a.id, a.name, a.convention_id, b.name AS convention_name, b.year, a.tag_id, c.tag AS tag_name
			FROM award_categories a
			LEFT JOIN convention b ON a.convention_id = b.id
			LEFT JOIN tag c ON a.tag_id = c.id
			WHERE a.id = $data_id
		");
    if (!$category_id) {
      die("Unknown award category");
    }
    $nominees = getall("SELECT a.id, a.game_id, a.name, a.nominationtext, a.winner, a.ranking, b.title, COUNT(c.id) AS count_entity FROM award_nominees a LEFT JOIN game b ON a.game_id = b.id LEFT JOIN award_nominee_entities c ON a.id = c.award_nominee_id WHERE award_category_id = $data_id GROUP BY a.id ORDER BY winner DESC, a.id");

    print "<table align=\"center\" border=0>" .
      "<tr><th colspan=5>Edit nominees for " . htmlspecialchars($name);
    if ($convention_id) {
      print " at: <a href=\"awards.php?category=convention&amp;data_id=$convention_id\" accesskey=\"q\">" . htmlspecialchars($convention_name) . " ($year)</a>";
    } elseif ($tag_id) {
      print " for: <a href=\"awards.php?category=tag&amp;data_id=$tag_id\" accesskey=\"q\">" . htmlspecialchars($tag_name) . "</a>";
    }
    print
      "</th></tr>\n" .
      "<tr>\n" .
      "<th>ID</th>" .
      "<th>Nominee</th>" .
      "<th>Game (optional)</th>" .
      "<th>Nominee text</th>" .
      "<th>Winner?</th>" .
      "<th>Position (optional)</th>" .
      "<th></th>" .
      "</tr>\n";
    foreach ($nominees as $nominee) {
      $game_id = "";
      if ($nominee['title']) {
        $game_id = $nominee['game_id'] . " - " . $nominee['title'];
      }
      $html_entity = "";
      $html_entity .= '<div style="margin-left: 3em;">';
      $html_entity .= ($nominee['count_entity'] == 1 ? '1 connection' : $nominee['count_entity'] . " connections");
      $html_entity .= ' <a href="#" onclick="this.nextSibling.style.display=\'block\'; this.nextSibling.focus(); this.style.display=\'none\'; return false;">[+]</a>';
      $html_entity .= '<input name="award_nominee_entity" style="font-size: 0.7em; display: none;" class="peopletags" placeholder="Name of individual nominee">';
      $entities = getall("SELECT id, COALESCE(person_id, game_id) AS data_id, CASE WHEN !ISNULL(person_id) THEN 'person' WHEN !ISNULL(game_id) THEN 'game' END AS category, label FROM award_nominee_entities WHERE award_nominee_id = " . $nominee['id'] . " ORDER BY id");
      $html_entity .= '<br>';
      foreach ($entities as $entity) {
        $html_entity .= '<a href="#" onclick="if (confirm(\'Do you want to delete this connection?\') ) { location.href=\'awards.php?category=awardcategory&amp;data_id=' . $data_id . '&amp;convention_id=' . $convention_id . '&amp;tag_id=' . $tag_id . '&amp;action=deletenomineeentity&amp;id=' . $entity['id'] . '\'; } else { return false; }">[delete]</a> ';
        if ($entity['category']) {
          $name = getentry($entity['category'], $entity['data_id']);
          $link = getdatalink($entity['category'], $entity['data_id'], true);
          $linkhtml = getdatahtml($entity['category'], $entity['data_id'], $name, true);
          $html_entity .= $linkhtml;
        } else {
          $html_entity .= $entity['label'];
        }
        $html_entity .= "<br>" . PHP_EOL;
      }
      $html_entity .= '</div>';

      print '<form action="awards.php" method="post">' .
        '<input type="hidden" name="action" value="changenominee">' .
        '<input type="hidden" name="category" value="' . $category . '">' .
        '<input type="hidden" name="data_id" value="' . $data_id . '">' .
        '<input type="hidden" name="convention_id" value="' . $convention_id . '">' .
        '<input type="hidden" name="tag_id" value="' . $tag_id . '">' .
        '<input type="hidden" name="id" value="' . $nominee['id'] . '">';
      print "<tr valign=\"top\">\n" .
        '<td style="text-align:right;">' . $nominee['id'] . '</td>' .
        '<td><input type="text" name="name" value="' . htmlspecialchars($nominee['name']) . '" size=40 maxlength=150 placeholder="(leave blank for scenario or board game)"><br>' .
        $html_entity .
        '</td>' .
        '<td><input type="text" name="game_id" value="' . htmlspecialchars($game_id) . '" size=30 maxlength=150 class="gametags"></td>' .
        '<td><textarea name="nominationtext" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;" >' . htmlspecialchars($nominee['nominationtext']) . '</textarea></td>' .
        '<td style="text-align: center;"><input type="checkbox" name="winner" value="yes" ' . ($nominee['winner'] ? 'checked' : '') . '></td>' .
        '<td><input type="text" name="ranking" value="' . htmlspecialchars($nominee['ranking']) . '" size=10 maxlength=150><br>' .
        '<td><input type="submit" name="do" value="Update"> ' .
        ($nominee['count_entity'] == 0 ? '<input type="submit" name="do" value="Delete" class="delete" onclick="return confirm(\'Remove nominee?\');">' : '') . '</td>' .
        "</tr>\n";
      print "</form>\n\n";
    }
    print '<form action="awards.php" method="post">' .
      '<input type="hidden" name="action" value="addnominee">' .
      '<input type="hidden" name="category" value="' . $category . '">' .
      '<input type="hidden" name="data_id" value="' . $data_id . '">' .
      '<input type="hidden" name="convention_id" value="' . $convention_id . '">' .
      '<input type="hidden" name="tag_id" value="' . $tag_id . '">';
    print "<tr valign=\"top\">\n" .
      '<td style="text-align:right;">New</td>' .
      '<td><input type="text" name="name" value="" size=40 maxlength=150 placeholder="(leave blank for scenario or board game)" autofocus></td>' .
      '<td><input type="text" name="game_id" value="" size=30 maxlength=150 class="gametags"></td>' .
      '<td><textarea name="nominationtext" cols="40" rows="2" onfocus="this.rows=10;" onblur="this.rows=2;" ></textarea></td>' .
      '<td style="text-align: center;"><input type="checkbox" name="winner" value="yes" ' . (count($nominees) == 0 ? 'checked' : '') . '></td>' .
      '<td><input type="text" name="ranking" value="" size=10 maxlength=150></td>' .
      '<td colspan=2><input type="submit" name="do" value="Create"></td>' .
      "</tr>\n";
    print "</form>\n\n";

    print "</table>" . PHP_EOL;
  }

  print "</body>\n</html>\n";

  ?>