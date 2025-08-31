<?php
require("./connect.php");
require_once("base.inc.php");
require_once("smartfind.inc.php");

function getjostid($name)
{
  global $id_a, $id_b, $id_data;
  $id_a = $id_b = $id_data = array();
  category_search($name, "CONCAT(firstname,' ',surname)", "person");
  /*
	print "<!--";
	print "id_a:";
	var_dump($id_a);
	print "id_b:";
	var_dump($id_b);
	print "-->\n\n\n\n";
	*/
  if (count($id_a) == 1) {
    return array_shift($id_a);
  } elseif (count($id_b) == 1 && strlen($name) >= 4) {
    return array_shift($id_b);
  }
  return false;
}

// Get people; do they exist?
$from = (string) ($_REQUEST['from'] ?? '');
$to = (string) ($_REQUEST['to'] ?? '');

// Prepare for errors
$from_error = $to_error = false;

// Numbers are ID's - otherwise get people from name
if (is_numeric($from)) {
  $from_id = intval($from);
} elseif ($from) {
  $from_id = getjostid($from);
  if (!$from_id) $from_error = true;
}

if (is_numeric($to)) {
  $to_id = intval($to);
} elseif ($to) {
  $to_id = getjostid($to);
  if (!$to_id) $to_error = true;
}

if (isset($from_id)) $from = getentry('person', $from_id);
if (isset($to_id))  $to = getentry('person', $to_id);

$mainperson = $from_id ?? 0;
$subperson = $to_id ?? 0;

$content = "";
$intro = 0;
if (!$mainperson || !$subperson) {
  $intro = 1;
}

$qnums = 0;
$graph = [];
$svglist = [];
$svg = '';


if ($mainperson && $subperson) {

  $person = getcolid("SELECT id, CONCAT(firstname,' ',surname) AS name FROM person");

  $title = getcolid("SELECT id, title FROM title");

  if (!$person[$mainperson]) $error = $t->getTemplateVars('_jostgame_personnotfound');
  if (!$person[$subperson]) $error = $t->getTemplateVars('_jostgame_personnotfound');

  if ($mainperson == $subperson) $error = $t->getTemplateVars('_jostgame_sameperson');

  if (!isset($error)) {
    $check[1][] = $subperson;
    $checked[] = $subperson;
    $i = 1;
    $personstotal = 1;

    // Loop!
    while ($check[$i]) {

      $inlist = join(",", $check[$i]);
      $notlist = join(",", $checked);

      $query_nocon = "
			SELECT
				COUNT(*) AS antal,
				t2.person_id AS link,
				g.id AS gameid,
				g.title,
				COALESCE(alias.label, g.title) AS title_translation,
				t2.title_id,
				t1.person_id AS rlink,
				t1.title_id AS rtitle_id
			FROM person a1
			INNER JOIN pgrel t1 ON t1.person_id = a1.id
			INNER JOIN game g ON g.id = t1.game_id
			INNER JOIN pgrel t2 ON t1.game_id = t2.game_id
			INNER JOIN person a2 ON a2.id = t2.person_id
			LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
			WHERE
				t1.person_id IN ($inlist) AND
				t2.person_id NOT IN ($notlist) AND
				t1.title_id IN (1,4,5) AND t2.title_id IN (1,4,5)
			GROUP BY
				link
			ORDER BY
				a1.firstname,
				a1.surname,
				a2.firstname,
				a2.surname,
				t1.title_id,
				t2.title_id,
				title_translation
		";



      $query_con = "
				SELECT
					COUNT(*) AS antal,
					t2.person_id AS link,
					g.id AS gameid,
					g.title,
					t2.title_id,
					t1.person_id AS rlink,
					t1.title_id AS rtitle_id,
					c.name,
					c.year
				FROM person AS p1
				INNER JOIN pgrel t1 ON t1.person_id = p1.id
				INNER JOIN game g ON g.id = t1.game_id
				INNER JOIN pgrel t2 ON t1.game_id = t2.game_id
				INNER JOIN person p2 ON p2.id = t2.person_id
				LEFT JOIN cgrel ON g.id = cgrel.game_id AND cgrel.presentation_id = 1
				LEFT JOIN convention c ON c.id = cgrel.convention_id
				WHERE
					t1.person_id IN ($inlist) AND
					t2.person_id NOT IN ($notlist) AND
					t1.title_id IN (1,4,5) AND t2.title_id IN (1,4,5)
				GROUP BY
					link
				ORDER BY
					p1.firstname,
					p1.surname,
					p2.firstname,
					p2.surname,
					t1.title_id,
					t2.title_id,
					g.title
			";

      // set query

      $query = $query_nocon;

      if ($showquery ?? false) $content .= "<br>$query<br>\n";
      $q = getall($query);
      print dberror();
      $qnums++;
      foreach ($q as $row) {
        $connection[$row['link']] = $row['rlink'];
        #		$content .= "($qnums) ".$row['link'] . " => " . $row['rlink']."<br>";
        $games[$row['link']]['title'] = $row['title_translation'];
        $games[$row['link']]['origtitle'] = $row['title'];
        $games[$row['link']]['gameid'] = $row['gameid'];
        $games[$row['link']]['antal'] = $row['antal'];
        if ($row['link'] == $mainperson) {
          $found = true;
          break 2;
        }
        $personstotal++;
        $check[($i + 1)][] = $row['link'];
        $checked[] = $row['link'];
      }
      $i++;
    }

    if ($found == true) {
      $content .= sprintf($t->getTemplateVars($qnums == 1 ? '_jost_connected' : '_jost_connected_pl'), $person[$mainperson], $person[$subperson], $qnums);
      if ($qnums >= 6) award_achievement(29);
      if ($qnums >= 10) award_achievement(30);
      if ($qnums >= 15) award_achievement(31);
    } else {
      $content .= sprintf($t->getTemplateVars('_jost_notconnected'), $person[$mainperson], $person[$subperson]);
    }
    $content .= "<br /><br />\n";

    // backtracker
    if ($found == true) {
      $map = "<map name=\"jostresult\">\n";
      $i = 0;
      $find = $mainperson;
      while ($find != $subperson && $i < 20) {
        $i++;
        $gametitle = $games[$find]['title'];
        $gameid = $games[$find]['gameid'];
        $antal = $games[$find]['antal'];
        $content .= textlinks(sprintf("%d: " . $t->getTemplateVars('_jost_connectedlist') . "<br>", $i, $find, htmlspecialchars($person[$find]), $gameid, htmlspecialchars($gametitle), $connection[$find], htmlspecialchars($person[$connection[$find]])));
        // for graph
        $graph[] = $find;
        $graph[] = $gameid;
        $svglist[] = ['type' => 'person', 'id' => $find, 'label' => $person[$find]];
        $svglist[] = ['type' => 'game', 'id' => $gameid, 'label' => $gametitle];
        // for ImageMap
        $y1 = (($i - 0.5) * 70) - 15;
        $y2 = (($i - 0.5) * 70) + 15;
        $map .= "<area shape=\"rect\" coords=\"10,$y1,150,$y2\" href=\"data?person=$find\" title=\"" . htmlspecialchars($person[$find]) . "\" alt=\"" . htmlspecialchars($person[$find]) . "\"/>\n";
        $y1 = ($i * 70) - 15;
        $y2 = ($i * 70) + 15;
        $map .= "<area shape=\"rect\" coords=\"100,$y1,240,$y2\" href=\"data?scenarie=$gameid\" title=\"" . htmlspecialchars($scen) . "\" alt=\"" . htmlspecialchars($scen) . "\" />\n";
        // next
        $find = $connection[$find];
      }
      // for graph
      $graph[] = $find;
      $svglist[] = ['type' => 'person', 'id' => $find, 'label' => $person[$find]];
      // for ImageMap
      $y1 = (($i + 0.5) * 70) - 15;
      $y2 = (($i + 0.5) * 70) + 15;
      $map .= "<area shape=\"rect\" coords=\"10,$y1,150,$y2\" href=\"data?person=$find\" title=\"$person[$subperson]\" alt=\"$person[$subperson]\" />\n";
      $map .= "</map>\n";
    }

    if ($found == true) {
      // Requires gd
      $content .= $map;
      $content .= "<br /><img src=\"jostgraph.php/sixdegrees_{$mainperson}_{$subperson}.png?" . join(',', $graph) . "\" usemap=\"#jostresult\" style=\"border: 0;\" alt=\"Graph between users\" />\n";
      // Use SVG instead
      // Graph contains 
      $svgheight = count($svglist) * 60 + 60;
      $svg .= '<svg preserveAspectRatio="none" height="' . $svgheight . '" width="350">' . PHP_EOL;
      $y = 0;
      foreach ($svglist as $svgentry) {
        $cx = $svgentry['type'] == 'person' ? 120 : 220;
        $cy += 60;
        $class = $svgentry['type'] == 'person' ? 'person' : 'scenarie';
        $svg .= '<a href="' . getdatalink($svgentry['type'], $svgentry['id']) . '" class="' . $class . '">' . PHP_EOL;
        $svg .= '<ellipse cx="' . $cx . '" cy="' . $cy . '" rx="100" ry="50" stroke="black" stroke-width="2" fill="white"/>' . PHP_EOL;
        $svg .= '<text x="' . ($cx - 50) . '" y="' . $cy . '" font-size="12"> ' . htmlspecialchars($svgentry['label']) . '</text>' . PHP_EOL;
        $svg .= '</a>' . PHP_EOL;
      }
      $svg .= '</svg>' . PHP_EOL;
    }
  } else {
    $content .= '<p class="finderror">' . $error . '</p>' . PHP_EOL;
  }
}

// people
$people = getcol("SELECT CONCAT(firstname, ' ', surname) AS id_name FROM person ORDER BY firstname, surname");
$json_people = json_encode($people);

$t->assign('type', 'jostgame');
$t->assign('content', $content);
$t->assign('intro', $intro);
$t->assign('from', $from);
$t->assign('to', $to);
$t->assign('from_error', $from_error);
$t->assign('to_error', $to_error);
#$t->assign('svg', $svg);
$t->assign('svg', '');

$t->display('jostgame.tpl');
