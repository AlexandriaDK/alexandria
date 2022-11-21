<?php
$this_type = 'tag';

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

list($tag_id, $ttag, $description, $internal) = getrow("SELECT id, tag, description, internal FROM tag WHERE tag = '" . dbesc($tag) . "'");
$this_id = $tag_id ?? 0;
$internal = (($_SESSION['user_editor'] ?? FALSE) ? $internal : ""); // only set internal if editor

$tag = getone("SELECT tag FROM tags WHERE tag = '" . dbesc($tag) . "'");
if (!$tag && !$tag_id) {
	$t->assign('content', $t->getTemplateVars('_nomatch'));
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch'));
	$t->display('default.tpl');
	exit;
}
if (!$tag) {
	$tag = $ttag;
}

// scenarios
$q = getall("
	SELECT g.id, title, c.name, c.id AS con_id, c.year, c.begin, c.end, c.cancelled, person_extra, COUNT(f.id) AS files, COALESCE(alias.label, g.title) AS title_translation
	FROM game g
	INNER JOIN tags ON g.id = tags.game_id
	LEFT JOIN cgrel ON cgrel.game_id = g.id AND cgrel.presentation_id = 1
	LEFT JOIN convention c ON cgrel.convention_id = c.id
	LEFT JOIN files f ON g.id = f.game_id AND f.downloadable = 1
	LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE tags.tag = '" . dbesc($tag) . "'
	GROUP BY g.id, c.id
	ORDER BY title_translation
");

$slist = [];
$sl = 0;

if (count($q) > 0) {
	foreach ($q as $rs) {
		if ($_SESSION['user_id']) {
			foreach (array('read', 'gmed', 'played') as $type) {
				$slist[$sl][$type] = getdynamicgamehtml($rs['id'], $type, $userlog[$rs['id']][$type] ?? FALSE);
			}
		}
		$game_id = (int) $rs['id'];
		// query-i-løkke... skal optimeres!
		$slist[$sl]['files'] = $rs['files'];
		$slist[$sl]['link'] = "data?scenarie=" . $rs['id'];
		$slist[$sl]['title'] = $rs['title_translation'];
		$slist[$sl]['origtitle'] = $rs['title'];
		$slist[$sl]['personlist'] = "";
		$slist[$sl]['cancelled'] = $rs['cancelled'];

		$personlist = [];
		$qq = getall("
			SELECT DISTINCT p.id, CONCAT(firstname,' ',surname) AS name
			FROM person p, pgrel
			WHERE pgrel.game_id = $game_id AND pgrel.person_id = p.id AND pgrel.title_id IN(1,5)
			ORDER BY firstname, surname
		");
		foreach ($qq as $thisforfatter) {
			list($forfid, $forfname) = $thisforfatter;
			$personlist[] = "<a href=\"data?person={$forfid}\" class=\"person\">$forfname</a>";
		}
		if (!$personlist && $rs['person_extra']) {
			$personlist[] = $rs['person_extra'];
		}
		if ($personlist) {
			$slist[$sl]['personlist'] = join("<br />", $personlist);
		}

		if ($rs['con_id']) {
			$coninfo = nicedateset($rs['begin'], $rs['end']);
			$slist[$sl]['coninfo'] = $coninfo;
			$slist[$sl]['conlink'] = "data?con=" . $rs['con_id'];
			$slist[$sl]['conname'] = $rs['name'] . " (" . yearname($rs['year']) . ")";
		}

		$sl++;
	}
}

// Awards - copypasted from convention.inc.php and should probably be a generic function
// List of awards
$awardlist = '';
if ($tag_id) {
	$award_nominees = getall("
		SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.ranking, a.game_id, b.id AS category_id, b.tag_id, b.name AS category_name, t.tag AS tag_name, d.title, COALESCE(e.label,d.title) AS title_translation
		FROM award_nominees a
		INNER JOIN award_categories b ON a.award_category_id = b.id
		LEFT JOIN tag t ON b.tag_id = t.id
		LEFT JOIN game d ON a.game_id = d.id
		LEFT JOIN alias e ON d.id = e.game_id AND e.language = '" . LANG . "' AND e.visible = 1
		WHERE t.id = $tag_id
		ORDER BY category_name, a.winner DESC, a.id
	");

	$awardset = [];
	$awardnominees = [];
	$html = "";
	foreach ($award_nominees as $nominee) {
		$tid = $nominee['tag_id'];
		$tag_id = $nominee['tag_id'];
		$cat_id = $nominee['category_id'];
		if (!$tid) $tid = 0;
		$awardnominees[$tid][$tag_id]['name'] = $nominee['tag_name'];
		// $awardnominees[$tid][$tag_id]['year'] = $nominee['year'];
		$awardnominees[$tid][$tag_id]['categories'][$cat_id]['name'] = $nominee['category_name'];
		$awardnominees[$tid][$tag_id]['categories'][$cat_id]['nominees'][] = ['id' => $nominee['id'], 'name' => $nominee['name'], 'nominationtext' => $nominee['nominationtext'], 'winner' => $nominee['winner'], 'ranking' => $nominee['ranking'], 'game_id' => $nominee['game_id'], 'title' => $nominee['title_translation']];
	}

	if ($awardnominees) {
		foreach ((array) $awardnominees[$tid] as $tagid => $atag) {
			// $html .= "<div class=\"awardyear\" data-year=\"" . $atag['year'] . "\">";
			$html .= "<div class=\"awardblock\">" . PHP_EOL;
			foreach ($atag['categories'] as $category) {
				$html .= PHP_EOL . "<div class=\"awardcategory\" data-category=\"" . htmlspecialchars($category['name']) . "\">" . PHP_EOL;
				$html .= "<h4>" . htmlspecialchars($category['name']) . "</h4>" . PHP_EOL;
				foreach ($category['nominees'] as $nominee) {
					$class = ($nominee['winner'] == 1 ? "winner" : "nominee");
					$html .= "<div class=\"" . $class . "\">";
					$html .= "<h5 class=\"" . $class . "\">";
					$html .= "<span class=\"" . $class . "\">";
					if ($nominee['game_id']) {
						$html .= getdatahtml('game', $nominee['game_id'], $nominee['title']);
					} else {
						$html .= htmlspecialchars($nominee['name']);
					}
					$html .= "</span>";
					if ($nominee['nominationtext']) {
						$nt_id = "nominee_text_" . $nominee['id'];
						$html .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"" . htmlspecialchars($t->getTemplateVars('_award_show_nominationtext')) . "\">[+]</span>";
					}
					$html .=  "</h5>";
					if ($nominee['ranking']) {
						$html .= "<div class=\"ranking\">(" . htmlspecialchars($nominee['ranking']) . ")</div>" . PHP_EOL;
					}
					if ($nominee['nominationtext']) {
						$html .= "<div class=\"nomtext\" style=\"display: none;\" id=\"$nt_id\">" . nl2br(htmlspecialchars(trim($nominee['nominationtext'])), FALSE) . "</div>" . PHP_EOL;
					}

					$html .= "</div>" . PHP_EOL;
				}
				$html .= "</div>" . PHP_EOL;
			}
			$html .= "</div>" . PHP_EOL;
			// $html .= "</div>" . PHP_EOL;
		}
	}
	$awardlist = $html;
}

// List of files
$filelist = getfilelist($this_id, $this_type);

// Trivia, links and articles
$trivialist = gettrivialist($this_id, $this_type);
$linklist = getlinklist($this_id, $this_type);
$articles = getarticlereferences($this_id, $this_type);

// Thumbnail
$available_pic = hasthumbnailpic($this_id, $this_type);

// Smarty
$t->assign('pagetitle', $tag);
$t->assign('type', $this_type);

$t->assign('id', $tag_id);
$t->assign('tag', $tag);
$t->assign('internal', $internal);
$t->assign('pic', $available_pic);
$t->assign('ogimage', getimageifexists($this_id, $this_type));
$t->assign('description', $description);
$t->assign('slist', $slist);
$t->assign('award', $awardlist);
$t->assign('trivia', $trivialist);
$t->assign('link', $linklist);
$t->assign('articles', $articles);
$t->assign('filelist', $filelist);
$t->assign('filedir', getcategorydir($this_type));
if (in_array(strtolower($tag), ['lgbtq', 'queer', 'queerness'])) {
	$t->assign('lgbtmenu', TRUE);
}

$t->display('data.tpl');
