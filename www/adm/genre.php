<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'genre';

$id = (int) $_REQUEST['id'];
$action = (string) $_REQUEST['action'];
$genid = (array) $_REQUEST['genid'];

$title = getone("SELECT title FROM game WHERE id = '$id'");

// Ret genre
if ($action == "changegenre") {
  doquery("DELETE FROM ggrel WHERE game_id = '$id'");
  foreach ($genid as $gid => $value) {
    doquery("INSERT INTO ggrel (genre_id, game_id) VALUES ('$gid','$id')");
  }
  $_SESSION['admin']['info'] = "Genres for game updated! " . dberror();
  chlog($id, 'game', "Genres updated");
  rexit($this_type, ['id' => $id]);
}

htmladmstart("Genre");

$result = getall("SELECT g.id, g.name, g.genre, ggrel.game_id FROM genre g LEFT JOIN ggrel ON g.id = ggrel.genre_id AND game_id = '$id' ORDER BY g.genre DESC, g.name");

if ($id) {
  $genre = TRUE;
  print "<form action=\"genre.php\" method=\"post\">\n";
  print "<table align=\"center\">" .
    "<tr><th colspan=2>Set genres for: <a href=\"game.php?game=$id\" accesskey=\"q\">$title</a></th></tr>\n";

  foreach ($result as $row) {
    if ($genre == TRUE && $row['genre'] == 0) {
      $genre = FALSE;
      print '<tr><td colspan="2">&nbsp;</td></tr>';
    }
    print "<tr>";
    print "<td><label for=\"gen_{$row['id']}\">" . $row['name'] . "</label></td>";
    print "<td><input id=\"gen_{$row['id']}\" type=\"checkbox\" name=\"genid[" . $row['id'] . "]\" " . ($row['game_id'] ? 'checked="checked"' : '') . " /></td>";
    print "</tr>\n";
  }

  print "<tr><td></td><td><input type=\"submit\" value=\"Save genres\" /><input type=\"hidden\" name=\"action\" value=\"changegenre\" /><input type=\"hidden\" name=\"id\" value=\"$id\" /></td></tr>\n";

  print "</table>\n";
  print "</form>\n\n";
} else {
  print "Error: No data id provided.";
}

print "</body>\n</html>\n";
