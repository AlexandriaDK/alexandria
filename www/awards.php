<?php
require_once "./connect.php";
require_once "base.inc.php";

$this_type = 'awards';

$html = "";

$tid = (int) ($_GET['tid'] ?? 0);
$ucid = $cid = (int) ($_GET['cid'] ?? 0);
$type_id = ($tid ? $tid : $cid);
if ($tid) {
  $type = 'tag';
} else {
  $type = 'convention';
}
if ($ucid == 1) award_achievement(96); // Fastaval conset

$awards = getall("
	SELECT a.conset_id, a.tag_id, COALESCE(b.name, t.tag) AS type_name, COALESCE(a.conset_id, a.tag_id) AS type_id, CASE WHEN b.id IS NOT NULL THEN 'convention' WHEN t.id IS NOT NULL THEN 'tag' ELSE '' END AS type
	FROM awards a
	LEFT JOIN conset b ON a.conset_id = b.id
	LEFT JOIN convention c ON b.id = c.conset_id
	LEFT JOIN tag t ON a.tag_id = t.id
	LEFT JOIN award_categories ac ON (t.id = ac.tag_id OR c.id = ac.convention_id) 
	GROUP BY a.conset_id, a.tag_id, COALESCE(b.name, t.tag), COALESCE(a.conset_id, a.tag_id), type
	HAVING COUNT(ac.id) > 0
	ORDER BY type_name, a.conset_id, a.tag_id
");

// $award_categories = getall("SELECT a.id, a.name, a.convention_id, a.description, b.name AS con_name, b.year FROM award_categories a LEFT JOIN convention b ON a.convention_id = b.id ORDER BY b.year DESC, a.id");
if (!$cid && !$tid) {
  //$award_nominees = getall("SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.game_id, b.id AS category_id, b.convention_id, b.name AS category_name, c.year, c.name AS con_name, c.conset_id, d.title FROM award_nominees a INNER JOIN award_categories b ON a.award_category_id = b.id LEFT JOIN convention c ON b.convention_id = c.id LEFT JOIN sce d ON a.game_id = d.id ORDER BY c.year DESC, a.winner DESC, a.id");
  $award_nominees = [];
} elseif ($tid) {
  $award_nominees = getall("
		SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.ranking, a.game_id, b.id AS category_id, b.convention_id, b.name AS category_name, b.tag_id, t.tag, d.title, COALESCE(e.label,d.title) AS title_translation, 'tag' AS type, 0 AS year, b.tag_id AS type_id
		FROM award_nominees a
		INNER JOIN award_categories b ON a.award_category_id = b.id
		LEFT JOIN tag t ON b.tag_id = t.id
		LEFT JOIN game d ON a.game_id = d.id
		LEFT JOIN alias e ON d.id = e.game_id AND e.language = '" . LANG . "' AND e.visible = 1
		WHERE t.id = $tid
		ORDER BY a.winner DESC, a.id
	");
} else {
  $award_nominees = getall("
		SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.ranking, a.game_id, b.id AS category_id, b.convention_id, b.name AS category_name, c.year, c.name AS con_name, c.conset_id, d.title, COALESCE(e.label,d.title) AS title_translation, 'convention' AS type, b.convention_id AS type_id
		FROM award_nominees a
		INNER JOIN award_categories b ON a.award_category_id = b.id
		LEFT JOIN convention c ON b.convention_id = c.id
		LEFT JOIN game d ON a.game_id = d.id
		LEFT JOIN alias e ON d.id = e.game_id AND e.language = '" . LANG . "' AND e.visible = 1
		WHERE c.conset_id = $cid
		ORDER BY c.year DESC, a.winner DESC, a.id
	");
}
$awardset = [];
$awardnominees = [];
foreach ($awards as $award) {
  $awardset[$award['type']][$award['type_id']]['name'] = $award['type_name'] ?? '';
}

// Kan slås sammen til én - og dermed fjerne ovenstående
foreach ($award_nominees as $nominee) {
  $type = $nominee['type'];
  $type_id = $nominee['type_id'];
  $cid = $nominee['conset_id'] ?? 0;
  $con_id = $nominee['convention_id'] ?? NULL;
  $cat_id = $nominee['category_id'];

  $awardnominees[$cid][$con_id]['name'] = $nominee['con_name'] ?? '';
  $awardnominees[$cid][$con_id]['year'] = yearname($nominee['year'] ?? NULL);
  $awardnominees[$cid][$con_id]['categories'][$cat_id]['name'] = $nominee['category_name'];
  $awardnominees[$cid][$con_id]['categories'][$cat_id]['nominees'][] = ['id' => $nominee['id'], 'name' => $nominee['name'], 'nominationtext' => $nominee['nominationtext'], 'winner' => $nominee['winner'], 'ranking' => $nominee['ranking'], 'game_id' => $nominee['game_id'], 'title' => $nominee['title_translation'], 'origtitle' => $nominee['title']];
}

if (!$type_id) {
  $html .= "<div>";
  foreach ($awards as $award) {
    if ($award['type_id']) {
      $html .= '<h3><a href="awards?' . ($award['type'] == 'convention' ? 'cid' : 'tid') . '=' . $award['type_id'] . '" class="con">' . htmlspecialchars($award['type_name']) . '</a></h3>';
    }
  }
  $html .= "</div>" . PHP_EOL;
} else {
  $type_name = '';
  if ($type == 'convention') {
    $type_name = getone("
			SELECT conset.name
			FROM conset
			INNER JOIN convention ON conset.id = convention.conset_id
			WHERE convention.id = $type_id
		");
  } elseif ($type == 'tag') {
    $type_name = getone("SELECT tag FROM tag WHERE id = $type_id");
  }
  $html .= "<h2>" . htmlspecialchars($type_name) . "</h2>";
  $years = [];
  $categories = [];
  if ($type == 'convention') {
    foreach ($awardnominees[$cid] as $convention) {
      $years[$convention['year'] ?? 1] = true;
      foreach ($convention['categories'] as $category) {
        $categories[$category['name']] = true;
      }
    }
    $html .= "<ul class=\"awardlist\">";
    foreach ($years as $year => $true) {
      //$html .= "<li class=\"yearselector\" data-year=\"$year\" onclick=\"selectAwardOption(this.dataset.year, 'year');\">" . $year . "</li>";
      $html .= "<li class=\"yearselector\" data-year=\"$year\" onclick=\"toggleAwardOptions(this.dataset.year, 'year');\">" . $year . "</li>";
    }
    $html .= "</ul>" . PHP_EOL;
  }

  // Mangler award-info, fx "Otto" eller "Den Gyldne Svupper" - skal nok alligevel lægges sammen
  $html .= "<ul class=\"awardlist\">";
  foreach ($categories as $category => $true) {
    //		$html .= "<li class=\"categoryselector\" data-category=\"" . htmlspecialchars($category) . "\" onclick=\"selectAwardOption(this.dataset.category, 'category');\">" . $category . "</li>";
    $html .= "<li class=\"categoryselector\" data-category=\"" . htmlspecialchars($category) . "\" onclick=\"toggleAwardOptions(this.dataset.category, 'category');\">" . $category . "</li>";
    //$html .= "<li class=\"categoryselector\" data-category=\"" . htmlspecialchars($category) . "\" onclick=\"selectAwardCategory(this.getAttribute('data-category'));\">" . $category . "</li>";
  }
  $html .= "</ul>" . PHP_EOL;

  $html .= "<div class=\"clear\"></div>" . PHP_EOL;

  foreach ($awardnominees[$cid] as $conid => $convention) {
    $htmlid = "con" . $conid;
    $html .= "<div class=\"awardyear\" data-year=\"" . $convention['year'] . "\">";
    if ($type == 'convention') {
      $html .= "<h3 id=\"$htmlid\">" . getdatahtml('convention', $conid, $convention['name'] . " (" . ($convention['year']) . ")") . "</h3>";
    }
    $html .= "<div class=\"awardblock\">" . PHP_EOL;
    foreach ($convention['categories'] as $category) {
      $html .= PHP_EOL . "<div class=\"awardcategory\" data-category=\"" . htmlspecialchars($category['name']) . "\">" . PHP_EOL;
      $html .= "<h4>" . htmlspecialchars($category['name']) . "</h4>" . PHP_EOL;
      foreach ($category['nominees'] as $nominee) {
        $has_nominationtext = !!$nominee['nominationtext'];
        $class = ($nominee['winner'] == 1 ? "winner" : "nominee");
        $html .= "<div class=\"" . $class . "\">";
        $html .= '<details><summary ' . ($has_nominationtext ? '' : 'class="nonomtext"') . '>';
        $html .= "<span class=\"" . $class . "\">";
        if ($nominee['game_id']) {
          $html .= getdatahtml('game', $nominee['game_id'], $nominee['title']);
        } else {
          $html .= htmlspecialchars($nominee['name']);
        }
        $html .= "</span>";
        if ($nominee['ranking']) {
          $html .= "<div class=\"ranking\">(" . htmlspecialchars($nominee['ranking']) . ")</div>" . PHP_EOL;
        }
        $html .= "</summary>";
        if ($has_nominationtext) {
          $html .= '<div class="nomtext">' . nl2br(htmlspecialchars(trim($nominee['nominationtext'])), false) . '</div>' . PHP_EOL;
        }
        $html .= '</details>';
        $html .= "</div>" . PHP_EOL;
      }
      $html .= "</div>" . PHP_EOL;
    }
    $html .= "</div>" . PHP_EOL;
    $html .= "</div>" . PHP_EOL;
  }
  $html .= "<div class=\"clear\"></div>" . PHP_EOL;
}

$t->assign('html_content', $html);
$t->assign('type', $this_type);
$t->assign('id', $ucid);
$t->assign('cid', $ucid);
$t->assign('tid', $tid);
$t->assign('type_name', $type_name ?? '');
$t->assign('ogimage', 'gfx/fastaval_otto_original.jpg');
$t->display('awards.tpl');
