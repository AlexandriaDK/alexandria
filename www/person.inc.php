<?php
$this_type = 'person';
$this_id = $person;

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

$r = getrow("SELECT id, internal, CONCAT(firstname,' ',surname) AS name, birth, death FROM person WHERE id = $person");
if ($r['id'] == 0) {
	$t->assign('content', $t->getTemplateVars('_nomatch'));
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch'));
	$t->display('default.tpl');
	exit;
}

$internal = (($_SESSION['user_editor'] ?? FALSE) ? $r['internal'] : ""); // only set internal if editor
// Achievements
if (isset($_SESSION['user_author_id']) && $r['id'] == $_SESSION['user_author_id']) award_achievement(21); // view own page

// List over aliases
$aliaslist = getaliaslist($person, $this_type);

// Game list
$q = getall("
	SELECT
		*,
		LEAST(COALESCE(firstcondate,'9999-99-99'), COALESCE(firstrundatecombined,'9999-99-99')) AS combinedfirstrun,
		CASE
		WHEN ISNULL(firstcondate) AND ISNULL(firstrundatecombined) THEN NULL
		WHEN !ISNULL(firstcondate) AND ISNULL(firstrundatecombined) THEN 'con'
		WHEN ISNULL(firstcondate) AND !ISNULL(firstrundatecombined) THEN 'run'
		WHEN firstcondate <= firstrundatecombined THEN 'con'
		ELSE 'run'
		END AS runtype
	FROM (
		SELECT
			MIN(COALESCE(c.begin,c.year)) AS firstcondate,
			MIN(gamerun.begin) AS firstrundate,
			MIN(gr2.begin) AS firstownrun,
			IF(MIN(IFNULL(gr2.id, 0)) = 0, MIN(gamerun.begin), MIN(gr2.begin)) AS firstrundatecombined,
			MIN(IFNULL(gr2.id, 0)) AS earliestrunid, -- gives 0 if at least one registration to game without specific run
			g.id,
			g.title AS title,
			g.boardgame AS boardgame,
			MIN(title.id) AS title_id,
			title.title AS auttitle,
			title.title_label AS auttitlelabel,
			title.iconfile,
			title.iconwidth,
			title.iconheight,
			title.textsymbol,
			COUNT(f.id) AS files,
			COALESCE(alias.label, g.title) AS title_translation
		FROM pgrel
		INNER JOIN game g ON pgrel.game_id = g.id
		LEFT JOIN title ON  pgrel.title_id = title.id
		LEFT JOIN cgrel ON cgrel.game_id = g.id
		LEFT JOIN convention c ON cgrel.convention_id = c.id
		LEFT JOIN gamerun ON g.id = gamerun.game_id 
		LEFT JOIN gamerun gr2 ON pgrel.gamerun_id = gr2.id 
		LEFT JOIN files f ON g.id = f.game_id AND f.downloadable = 1
		LEFT JOIN alias ON g.id = alias.game_id AND alias.language = '" . LANG . "' AND alias.visible = 1
		WHERE pgrel.person_id = $person
		GROUP BY g.id, title.id
	) a
	ORDER BY
		combinedfirstrun != '9999-99-99', -- Sort games without any found date first
		combinedfirstrun,
		title_id,
		title_translation,
		title
");

$slist = [];
$sl = 0;

if (count($q) > 0) {
	foreach ($q as $rs) {
		if ($_SESSION['user_id']) {
			if ($rs['boardgame']) {
				$options = getuserlogoptions('boardgame');
			} else {
				$options = getuserlogoptions('scenario');
			}
			foreach ($options as $type) {
				if ($type) {
					$slist[$sl][$type] = getdynamicgamehtml($rs['id'], $type, $userlog[$rs['id']][$type] ?? FALSE);
				} else {
					$slist[$sl][] = " ";
				}
			}
		}

		$slist[$sl]['files'] = $rs['files'];
		$slist[$sl]['textsymbol'] = $rs['textsymbol'];
		$slist[$sl]['iconfile'] = $rs['iconfile'];
		$slist[$sl]['icontitle'] = ucfirst($t->getTemplateVars('_' . $rs['auttitlelabel']));
		$slist[$sl]['iconwidth'] = $rs['iconwidth'];
		$slist[$sl]['iconheight'] = $rs['iconheight'];
		$slist[$sl]['link'] = "data?scenarie=" . $rs['id'];
		$slist[$sl]['title'] = $rs['title_translation'];
		$slist[$sl]['origtitle'] = $rs['title'];
		$slist[$sl]['firstdate'] = $rs['combinedfirstrun'] != '9999-99-99' ? $rs['combinedfirstrun'] : NULL;

		$game_id = $rs['id'];

		$runlist = [];
		if ($rs['runtype'] == 'con') { // get all "first cons"
			$qq = getall("
				SELECT c.id, c.name, c.year, c.begin, c.end, c.cancelled
				FROM convention c
				INNER JOIN cgrel ON c.id = cgrel.convention_id
				WHERE cgrel.presentation_id = 1 AND cgrel.game_id = $game_id
				ORDER BY c.name
			");
			foreach ($qq as $rrs) {
				$coninfo = nicedateset($rrs['begin'], $rrs['end']);
				$runlist[] = "<a href=\"data?con={$rrs['id']}\" class=\"con" . ($rrs['cancelled'] == 1 ? " cancelled" : "") . "\" title=\"$coninfo\">" . htmlspecialchars($rrs['name']) . " (" . yearname($rrs['year']) . ")</a>";
			}
		} elseif ($rs['runtype'] == 'run') {
			$yearname = '';
			$earliestrunid = $rs['earliestrunid'];
			$title_id = $rs['title_id'];
			$runs = getall("
				(
					SELECT YEAR(begin) AS year, begin, end, location, country
					FROM gamerun
					WHERE game_id = $game_id
					AND begin != '0000-00-00'
					AND $earliestrunid = 0 -- only true if person was original creator
					ORDER BY begin
					LIMIT 1
				)
				UNION
				(
					SELECT YEAR(begin) AS year, begin, end, location, country
					FROM gamerun
					INNER JOIN pgrel ON gamerun.id = pgrel.gamerun_id AND pgrel.person_id = $person AND pgrel.title_id = $title_id
					WHERE gamerun.game_id = $game_id
					AND begin != '0000-00-00'
					ORDER BY begin
				)
			");
			foreach ($runs AS $qrun) {
				$rundescription = '';
				$runinfo = nicedateset($qrun['begin'] ?? NULL, $qrun['end'] ?? NULL);
				if (isset($qrun['location'])) {
					$rundescription = $qrun['location'];
				}
				if (isset($qrun['country'])) {
					if ($rundescription !== '') {
						$rundescription .= ', ';
					}
					$rundescription .= getCountryName($qrun['country']);
				}
				if ($rundescription !== '') {
					$rundescription .= ' ';
				}
				if (isset($qrun['year'])) {
					$yearname = yearname($qrun['year']);
					$rundescription .= '(' . $yearname . ')';
				}
				$runlist[] = '<span title="' . htmlspecialchars($runinfo) . '">' . htmlspecialchars($rundescription) . '</span>';
			}
		}
		if ($runlist) {
			$slist[$sl]['runlist'] = join("<br />", $runlist);
		} else {
			$slist[$sl]['runlist'] = "";
		}
		$sl++;
	}
}

// List of awards
$awarddata = [];

// awards if your are an author (1), organizer (4), or designer (5)
$q = getall("
	(
	SELECT a.id, a.nominationtext, a.winner, a.ranking, a.name AS nomineename, b.name, c.id AS convention_id, c.name AS convent_name, c.year, c.begin, c.conset_id, e.title, COALESCE(f.label,e.title) AS title_translation
	FROM award_nominees a
	INNER JOIN award_categories b ON a.award_category_id = b.id
	INNER JOIN convention c ON b.convention_id = c.id
	INNER JOIN pgrel d ON a.game_id = d.game_id AND d.title_id IN (1,4,5) AND d.person_id = $person
	INNER JOIN game e ON a.game_id = e.id
	LEFT JOIN alias f ON e.id = f.game_id AND f.language = '" . LANG . "' AND f.visible = 1
	)
	UNION ALL
	(
	SELECT a.id, a.nominationtext, a.winner, a.ranking, a.name AS nomineename, b.name, c.id AS convention_id, c.name AS convent_name, c.year, c.begin, c.conset_id, '' AS title, '' as title_translation
	FROM award_nominees a
	INNER JOIN award_categories b ON a.award_category_id = b.id
	INNER JOIN convention c ON b.convention_id = c.id
	INNER JOIN award_nominee_entities d ON a.id = d.award_nominee_id AND d.person_id = $person
	)
	ORDER BY year ASC, begin ASC, convention_id ASC, winner DESC, id ASC
");

foreach ($q as $rs) {
	$awardtext = "";
	if ($rs['title_translation']) {
		$awardtext .= '<span title="' . htmlspecialchars($rs['title']) . '">' . htmlspecialchars($rs['title_translation']) . "</span>: ";
	}
	$awardtext .= ($rs['winner'] ? ucfirst($t->getTemplateVars('_award_winner')) : ucfirst($t->getTemplateVars('_award_nominated'))) . ", " . htmlspecialchars($rs['name']);
	if ($rs['ranking']) {
		$awardtext .= " (" . htmlspecialchars($rs['ranking']) . ")";
	}

	if ($rs['title'] == '' && $rs['nomineename'] && $rs['nomineename'] != $r['name']) { // personal award, group name
		$awardtext .= " (" . htmlspecialchars($rs['nomineename']) . ")";
	}

	if ($rs['nominationtext']) {
		$nt_id = "nominee_text_" . $rs['id'];
		$awardtext .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" style=\"font-weight: bold;\" title=\"" . htmlspecialchars($t->getTemplateVars('_award_show_nominationtext')) . "\">[+]</span>";
		$awardtext .= "<div class=\"nomtext\" style=\"display: none;\" id=\"$nt_id\">" . nl2br(htmlspecialchars(trim($rs['nominationtext'])), FALSE) . "</div>" . PHP_EOL;
	}

	// $awardtext .=  " – " . $rs['conventname'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "") . "<br>" . PHP_EOL;
	$awarddata[$rs['convention_id']]['name'] = $rs['convent_name'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "");
	$awarddata[$rs['convention_id']]['conset_id'] = $rs['conset_id'];
	$awarddata[$rs['convention_id']]['text'][] = $awardtext;
}
$awardlist = "";
foreach ($awarddata as $convention_id => $data) {
	$con_award_url = "/awards?cid=" . $data['conset_id'] . "#con" . $convention_id;
	$awardlist .= "<h4 class=\"awardconventhead\"><a href=\"" . $con_award_url . "\" class=\"con\" title=\"Alle priser for " . htmlspecialchars($data['name']) . "\">" . htmlspecialchars($data['name']) . "</a></h4>" . PHP_EOL;
	$awardlist .= "<div>";
	$awardlist .= implode("<br>" . PHP_EOL, $data['text']);
	$awardlist .= "</div>" . PHP_EOL;
}

// List of organizer posts
$organizerlist = getorganizerlist($person, $this_type);

// Links and trivia
$linklist = getlinklist($this_id, $this_type);
$trivialist = gettrivialist($this_id, $this_type);

// Articles
$articlesfrom = getarticles($this_id, $this_type);
$articles = getarticlereferences($this_id, $this_type);

// Birthday
$birth = "";
$age_year = "";
if ($r['birth'] && $r['birth'] != "0000-00-00" && substr($r['birth'], 0, 4) != "0000") { // no support for birthday without year
	if ($r['death'] && $r['death'] != "0000-00-00") {
		$birth = fulldate($r['birth']);
	} else {
		$birth = fulldate($r['birth']);
		$age_year = birthage($r['birth']);
	}
}

$death = "";
if ($r['death'] && $r['death'] != "0000-00-00") {
	if ($r['birth'] && $r['birth'] != "0000-00-00") {
		$death = fulldate($r['death']);
		$age_year = birthage($r['birth'], $r['death']);
	} else {
		$death = fulldate($r['death']);
	}
}

// Thumbnail
$available_pic = hasthumbnailpic($person, $this_type);

// Smarty
$t->assign('pagetitle', $r['name']);
$t->assign('type', $this_type);

$t->assign('id', $person);
$t->assign('name', $r['name']);
$t->assign('internal', $internal);
$t->assign('pic', $available_pic);
$t->assign('ogimage', getimageifexists($this_id, $this_type));
$t->assign('alias', $aliaslist);
$t->assign('birth', $birth);
$t->assign('death', $death);
$t->assign('age', $age_year);
$t->assign('slist', $slist);
$t->assign('award', $awardlist);
$t->assign('organizerlist', $organizerlist);
$t->assign('articlesfrom', $articlesfrom);
$t->assign('articles', $articles);
$t->assign('trivia', $trivialist);
$t->assign('link', $linklist);

$t->display('data.tpl');
