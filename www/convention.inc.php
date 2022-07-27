<?php
$this_type = 'convention';
$this_id = $con;

if (isset($_SESSION['user_id'])) {
	$userloggames = getuserloggames($_SESSION['user_id']);
}

$persons_limit = 4;
$scenlistdata = $boardlistdata = $gamelist = [];
$oo = $_GET['oo'] ?? FALSE; // sort order for organizers
$edit = $_GET['edit'] ?? FALSE;

/*
function antaltxt($new, $rerun, $cancelled, $total, $type = 'game')
{
	if ($total == 0) {
		return "";
	}
	$antaltxt = sprintf("%d %s", $total, ($type == 'game' ? ($total == 1 ? "scenarie" : "scenarier") : 'brætspil'));
	if ($rerun > 0 && $cancelled > 0) {
		$antaltxt .= sprintf(", heraf %d %s, %d %s og %d %s", $new, ($new == 1 ? "nyt" : "nye"), $rerun, ($rerun == 1 ? "rerun" : "reruns"), $cancelled, ($cancelled == 1 ? "aflyst" : "aflyste"));
	} elseif ($rerun > 0 && $new > 0) {
		$antaltxt .= sprintf(", heraf %d %s og %d %s", $new, ($new == 1 ? "nyt" : "nye"), $rerun, ($rerun == 1 ? "rerun" : "reruns"));
	} elseif ($cancelled > 0 && $new > 0) {
		$antaltxt .= sprintf(", heraf %d %s og %d %s", $new, ($new == 1 ? "nyt" : "nye"), $cancelled, ($cancelled == 1 ? "aflyst" : "aflyste"));
	} elseif ($rerun == $total) {
		$antaltxt .= ", udelukkende reruns";
	} elseif ($cancelled == $total) {
		$antaltxt .= ", udelukkende aflysninger";
	}
	return $antaltxt;
}
*/

// achievements
if ($con == 26) award_achievement(79); // X-Con
if ($con == 127 || $con == 743) award_achievement(80); // 1. Copenhagen Gamecon (Viking Con I) or Konvent '77 (GothCon I)

$convention = getrow("SELECT c.id, c.name, c.internal, c.year, c.description, begin, end, place, conset_id, confirmed, cancelled, conset.name AS cname, COALESCE(c.country, conset.country) AS country
	FROM convention c
	LEFT JOIN conset ON c.conset_id = conset.id
	WHERE c.id = $con
");
if (is_null($convention['id'])) {
	$t->assign('content', $t->getTemplateVars('_nomatch'));
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch'));
	$t->display('default.tpl');
	exit;
}
$showtitle = $conventname = $convention['name'];
$internal = (($_SESSION['user_editor'] ?? FALSE) ? $convention['internal'] : ""); // only set internal if editor

// List of files
$filelist = getfilelist($con, $this_type);

// Part of con series? Find previous and next.
if ($convention['conset_id']) {
	$cname = ($convention['conset_id'] == 40 ? $t->getTemplateVars('_cons_other') : $convention['cname']);
	$partofhtml = "<a href=\"data?conset=" . $convention['conset_id'] . "\" class=\"con\">" . htmlspecialchars($cname) . "</a>";
	$qq = getall("
		SELECT id, name, year, begin, end
		FROM convention 
		WHERE conset_id = " . $convention['conset_id'] . "
		ORDER BY year, begin, name
	");
	unset($seriedata, $seriecount, $seriethis);
	$seriecount = 0;
	foreach ($qq as $row) {
		$seriecount++;
		$seriedata['id'][$seriecount] = $row['id'];
		$seriedata['name'][$seriecount] = $row['name'];
		$seriedata['year'][$seriecount] = $row['year'];
		$seriedata['begin'][$seriecount] = $row['begin'];
		$seriedata['end'][$seriecount] = $row['end'];
		if ($row['id'] == $con) $seriethis = $seriecount;
	}
	$arrows = [];
	if ($seriethis) {
		if (isset($seriedata['id'][($seriethis - 1)])) {
			$arrows['prev'] = ['active' => TRUE, 'conid' => $seriedata['id'][($seriethis - 1)], 'name' => $seriedata['name'][($seriethis - 1)] . " (" . yearname($seriedata['year'][($seriethis - 1)]) . ")"];
		} else {
			$arrows['prev'] = ['active' => FALSE];
		}
		if (isset($seriedata['id'][($seriethis + 1)])) {
			$arrows['next'] = ['active' => TRUE, 'conid' => $seriedata['id'][($seriethis + 1)], 'name' => $seriedata['name'][($seriethis + 1)] . " (" . yearname($seriedata['year'][($seriethis + 1)]) . ")"];
		} else {
			$arrows['next'] = ['active' => FALSE];
		}
	}
}

// List of games

$sce_new = $sce_rerun = $sce_cancelled = $board_new = $board_rerun = $board_cancelled = 0;

$q = getall("
	SELECT g.id, g.title, g.boardgame, pr.id AS preid, pr.event, pr.event_label, pr.iconfile, pr.textsymbol, g.gamesystem_extra, gs.id AS gamesystem_id, gs.name AS sys_name, COUNT(f.id) AS files, p.id AS person_id, CONCAT(firstname,' ',surname) AS person_name, a.label, COALESCE(a.label, g.title) AS title_translation, COALESCE(a2.label, gs.name) AS system_translation
	FROM cgrel
	INNER JOIN game g ON g.id = cgrel.game_id
	LEFT JOIN presentation pr ON cgrel.presentation_id = pr.id 
	LEFT JOIN gamesystem gs ON g.gamesystem_id = gs.id
	LEFT JOIN files f ON g.id = f.game_id AND f.downloadable = 1
	LEFT JOIN pgrel ON g.id = pgrel.game_id AND pgrel.title_id IN(1,4,5)
	LEFT JOIN person p ON p.id = pgrel.person_id
	LEFT JOIN alias a ON g.id = a.game_id AND a.language = '" . LANG . "' AND a.visible = 1
	LEFT JOIN alias a2 ON gs.id = a2.gamesystem_id AND a2.language = '" . LANG . "' AND a2.visible = 1
	WHERE cgrel.convention_id = $con
	GROUP BY g.id, pr.id, p.id
	ORDER BY boardgame, title_translation, p.surname, p.firstname
");

foreach ($q as $r) {
	$sid = $r['id'];
	if (!isset($gamelist[$sid])) {
		$gamelist[$sid] = ['game' => ['title' => $r['title'], 'title_translation' => $r['title_translation'], 'person_extra' => $r['person_extra'] ?? NULL, 'files' => (int) $r['files'], 'boardgame' => (int) $r['boardgame'], 'system_id' => $r['gamesystem_id'], 'system_name' => $r['sys_name'], 'system_translation' => $r['system_translation'], 'system_ext' => $r['gamesystem_extra'], 'presentation_id' => $r['presentation_id'] ?? NULL, 'pre_event' => $r['event'], 'pre_event_label' => $r['event_label'], 'pre_iconfile' => $r['iconfile'], 'pre_textsymbol' => $r['textsymbol']], 'person' => []];
	}
	if ($r['person_id']) {
		$gamelist[$sid]['person'][$r['person_id']] = $r['person_name'];
	}
}

foreach ($gamelist as $game_id => $game) {
	$datalistdata = [];
	$useroptions = [];
	if (isset($_SESSION['user_id'])) {
		if ($game['game']['boardgame']) {
			$options = getuserlogoptions('boardgame');
		} else {
			$options = getuserlogoptions('scenario');
		}
		foreach ($options as $type) {
			if ($type != NULL) {
				$useroptions[$type] = getdynamicgamehtml($game_id, $type, $userloggames[$game_id][$type] ?? FALSE);
			}
		}
	}

	$personlist = [];
	$personlistextra = [];
	$person_count = 0;
	foreach ($game['person'] as $person_id => $person_name) {
		$person_count++;
		$personhtml = "<a href=\"data?person=" . $person_id . "\" class=\"person\">" . htmlspecialchars($person_name) . "</a>";
		if ($person_count < $persons_limit || count($game['person']) == $persons_limit) {
			$personlist[] = $personhtml;
		} else {
			$personlistextra[] = $personhtml;
		}
	}
	$personhtml = "";
	if ($personlist) {
		$personhtml = join("<br>", $personlist);
		$personextrahtml =  join("<br>", $personlistextra);
	}

	if ($game['game']['pre_textsymbol']) { // unicode icons
		$runsymbol = "<span class=\"preicon\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $game['game']['pre_event_label']))) . "\">" . $game['game']['pre_textsymbol'] . "</span>";
	} elseif ($game['game']['pre_iconfile']) {
		$runsymbol = "<img src=\"/gfx/" . $game['game']['pre_iconfile'] . "\" alt=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $game['game']['pre_event_label']))) . "\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $game['game']['pre_event_label']))) . "\" width=\"15\" height=\"15\" />";
	} else {
		$runsymbol = "";
	}

	$datalistdata = [
		'id' => $game_id,
		'userdyn' => $useroptions,
		'filescount' => $game['game']['files'],
		'runsymbol' => $runsymbol,
		'title' => $game['game']['title_translation'],
		'personhtml' => $personhtml,
		'personextracount' => count($personlistextra),
		'personextrahtml' => $personextrahtml ?? '',
		'systemhtml' => $sysstring ?? FALSE,
		'system_id' => $game['game']['system_id'],
		'system_name' => $game['game']['system_name'],
		'system_translation' => $game['game']['system_translation'],
		'system_extra' => $game['game']['system_ext'],
		'boardgame' => $game['game']['boardgame'],
	];

	if ($game['game']['boardgame']) {
		$boardlistdata[] = $datalistdata;
	} else {
		$scenlistdata[] = $datalistdata;
	}

	// Count scenarios based on presentation (premiere, re-run, ...)
	/*
	$total = $sce_new + $sce_rerun + $sce_cancelled;
	$board_total = $board_new + $board_rerun + $board_cancelled;

	$scen_antaltxt = antaltxt($sce_new, $sce_rerun, $sce_cancelled, $total, 'sce');
	$board_antaltxt = antaltxt($board_new, $board_rerun, $board_cancelled, $board_total, 'board');

	if ($scenlist) {
		$scenlist = "<tr><td colspan=\"8\">$scen_antaltxt</td></tr>\n" . $scenlist;
	}
	if ($boardlist) {
		$boardlist = "<tr><td colspan=\"8\">$board_antaltxt</td></tr>\n" . $boardlist;
	}
	*/
}

// List of awards
$award_nominees = getall("
	SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.ranking, a.game_id, b.id AS category_id, b.convention_id, b.name AS category_name, c.year, c.name AS con_name, c.conset_id, d.title, COALESCE(e.label,d.title) AS title_translation
	FROM award_nominees a
	INNER JOIN award_categories b ON a.award_category_id = b.id
	LEFT JOIN convention c ON b.convention_id = c.id
	LEFT JOIN game d ON a.game_id = d.id
	LEFT JOIN alias e ON d.id = e.game_id AND e.language = '" . LANG . "' AND e.visible = 1
	WHERE c.id = $con
	ORDER BY c.year DESC, a.winner DESC, a.id
");

$awardset = [];
$awardnominees = [];
$html = "";
foreach ($award_nominees as $nominee) {
	$cid = $nominee['conset_id'];
	$con_id = $nominee['convention_id'];
	$cat_id = $nominee['category_id'];
	if (!$cid) $cid = 0;
	$awardnominees[$cid][$con_id]['name'] = $nominee['con_name'];
	$awardnominees[$cid][$con_id]['year'] = $nominee['year'];
	$awardnominees[$cid][$con_id]['categories'][$cat_id]['name'] = $nominee['category_name'];
	$awardnominees[$cid][$con_id]['categories'][$cat_id]['nominees'][] = ['id' => $nominee['id'], 'name' => $nominee['name'], 'nominationtext' => $nominee['nominationtext'], 'winner' => $nominee['winner'], 'ranking' => $nominee['ranking'], 'game_id' => $nominee['game_id'], 'title' => $nominee['title_translation']];
}

if ($awardnominees) {
	foreach ((array) $awardnominees[$cid] as $conid => $aconvent) {
		$html .= "<div class=\"awardyear\" data-year=\"" . $aconvent['year'] . "\">";
		$html .= "<div class=\"awardblock\">" . PHP_EOL;
		foreach ($aconvent['categories'] as $category) {
			$html .= PHP_EOL . "<div class=\"awardcategory\" data-category=\"" . htmlspecialchars($category['name']) . "\">" . PHP_EOL;
			$html .= "<h4>" . htmlspecialchars($category['name']) . "</h4>" . PHP_EOL;
			foreach ($category['nominees'] as $nominee) {
				$class = ($nominee['winner'] == 1 ? "winner" : "nominee");
				$html .= "<div class=\"" . $class . "\">";
				$html .= "<h5 class=\"" . $class . "\">";
				$html .= "<span class=\"" . $class . "\">";
				if ($nominee['game_id']) {
					$html .= getdatahtml('sce', $nominee['game_id'], $nominee['title']);
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
		$html .= "</div>" . PHP_EOL;
	}
}
$awardlist = $html;

// List of organizers
if ($oo == 'id') { // oo = organizer order
	$organizerlist = getorganizerlist($con, $this_type, 'a.id');
} else {
	$organizerlist = getorganizerlist($con, $this_type);
}

// List of aliases, alternative title?
$alttitle = getcol("SELECT label FROM alias WHERE convention_id = '$con' AND language = '$lang' AND visible = 1");
if (count($alttitle) == 1) {
	$showtitle = $alttitle[0];
	$aliaslist = getaliaslist($con, $this_type, $showtitle);
	if ($aliaslist) {
		$aliaslist = htmlspecialchars($conventname) . ", " . $aliaslist;
	} else {
		$aliaslist = htmlspecialchars($conventname);
	}
} else {
	$aliaslist = getaliaslist($con, $this_type);
}

// Trivia, links and articles
$trivialist = gettrivialist($this_id, $this_type);
$linklist = getlinklist($this_id, $this_type);
$articles = getarticlereferences($this_id, $this_type);

// Thumbnail
$available_pic = hasthumbnailpic($con, $this_type);

// Userdata, entries from all users
$userlog = [];
if (isset($_SESSION['user_id'])) {
	$userlog = getuserlog($_SESSION['user_id'], $this_type, $convention['id']);
	$users_entries = getalluserentries('convention', $convention['id']);
}

// Edit mode?
$editorganizers = ($edit == 'organizers');
$editmode = (isset($_SESSION['user_id']) && $editorganizers);

// Smarty
$t->assign('pagetitle', $showtitle . " (" . ($convention['year'] ? yearname($convention['year']) : "?") . ")");
$t->assign('type', $this_type);

$t->assign('id', $con);
$t->assign('name', $showtitle);
$t->assign('year', ($convention['year'] ? $convention['year'] : "?"));
$t->assign('arrowset', $arrows);
$t->assign('pic', $available_pic);
$t->assign('ogimage', getimageifexists($this_id, $this_type));
$t->assign('place', $convention['place']);
$t->assign('countrycode', $convention['country']);
$t->assign('dateset', nicedateset($convention['begin'], $convention['end']));
$t->assign('partof', $partofhtml);
$t->assign('confirmed', $convention['confirmed']);
$t->assign('cancelled', $convention['cancelled']);
$t->assign('description', $convention['description']);
$t->assign('internal', $internal);
$t->assign('scenlistdata', $scenlistdata);
$t->assign('boardlistdata', $boardlistdata);
$t->assign('organizerlist', $organizerlist);
$t->assign('award', $awardlist);
$t->assign('trivia', $trivialist);
$t->assign('link', $linklist);
$t->assign('articles', $articles);
$t->assign('alias', $aliaslist);
$t->assign('filelist', $filelist);
$t->assign('filedir', getcategorydir($this_type));

$t->assign('editorganizers', $editorganizers);
$t->assign('editmode', $editmode);
$t->assign('user_can_edit_organizers', $_SESSION['can_edit_organizers'] ?? FALSE);

$t->assign('user_visited', in_array('visited', $userlog));
$t->assign('users_entries', $users_entries ?? FALSE);

if ($con == 504 || $convention['conset_id'] == 117) { // Hardcoded: "Rollespil din Pride" + QueerCon cons
	$t->assign('lgbtmenu', TRUE);
}

$t->display('data.tpl');
