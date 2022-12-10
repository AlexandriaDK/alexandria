<?php
$this_type = 'game';
if ($game) {
	$scenarie = $game;
}
$this_id = $game = $scenarie;

$r = getrow("
	SELECT g.id, g.title, g.internal, g.gamesystem_id, g.gamesystem_extra, g.person_extra, g.ottowinner, g.rlyeh_id, gms_min, gms_max, players_min, players_max, participants_extra, boardgame, gs.name AS sysname, COALESCE(alias.label, gs.name) AS system_translation
	FROM game g
	LEFT JOIN gamesystem gs ON g.gamesystem_id = gs.id
	LEFT JOIN alias ON g.gamesystem_id = alias.gamesystem_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE g.id = $game
	GROUP BY g.id
");
$showtitle = $gametitle = $r['title'];

// Achievements
if ($game == 161)      award_achievement(55); // De Professionelle
if ($game == 3812)     award_achievement(56); // Fordømt Ungdom
if ($game == 3827)     award_achievement(57); // Paninaro
if (in_array($game, [3755, 4615, 4516, 4597, 4461])) award_achievement(98); // Bicycling  :TODO: - Use tag instead
if ($r['ottowinner'] == 1) award_achievement(62); // Otto winner  :TODO: - check using achievement table
if ($r['rlyeh_id'] > 0)    award_achievement(12); // R'lyeh scenario  :TODO: - use tag instead

if ($r['id'] == 0) {
	header("HTTP/1.1 404 Not Found ");
	$t->assign('content', $t->getTemplateVars('_sce_nomatch'));
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch'));
	$t->display('default.tpl');
	exit;
}
$descriptions = getall("SELECT description, language, note FROM game_description WHERE game_id = $game ORDER BY (LEFT(language, 2) = '" . LANG . "') DESC, LENGTH(language), priority, language");
foreach ($descriptions as $d_id => $description) {
	list($language) = explode(" ", $description['language']);
	if (preg_match('/^[a-z]{2}$/', $language)) {
		$descriptions[$d_id]['langcode'] = $language;
		$descriptions[$d_id]['langname'] = getLanguageName($language);
	}
}
$internal = (($_SESSION['user_editor'] ?? FALSE) ? $r['internal'] : ""); // only set internal if editor

// Description of participants
$participants = [];
$gms = $players = "";
if ($r['gms_min'] !== NULL) {
	if ($r['gms_min'] == 0 && $r['gms_max'] == 0) {
		$gms_text = $t->getTemplateVars('_sce_none_gms');
	} else {
		$gms_text = $r['gms_min'];
		if ($r['gms_max'] != $r['gms_min']) {
			$gms_text .= "-" . $r['gms_max'];
		}
		$gms_text .= " " . ($r['gms_min'] == 1 && $r['gms_max'] == 1 ? $t->getTemplateVars('_sce_gm') : $t->getTemplateVars('_sce_gms'));
	}
	$participants[] = $gms_text;
	$gms = ($r['gms_max'] != $r['gms_min'] ? $r['gms_min'] . "-" . $r['gms_max'] : $r['gms_min']);
}
if ($r['players_min'] !== NULL) {
	$players_text = $r['players_min'];
	if ($r['players_max'] != $r['players_min']) {
		$players_text .= "-" . $r['players_max'];
	}
	$players_text .= " " . ($r['players_min'] == 1 && $r['players_max'] == 1 ? $t->getTemplateVars('_sce_player') : $t->getTemplateVars('_sce_players'));
	$participants[] = $players_text;
	$players = ($r['players_max'] != $r['players_min'] ? $r['players_min'] . "-" . $r['players_max'] : $r['players_min']);
}
if ($r['participants_extra']) {
	$participants[] = $r['participants_extra'];
}
$participants = implode(', ', $participants);

// List of aliases, alternative title?
$alttitle = getcol("SELECT label FROM alias WHERE game_id = '$game' AND language = '$lang' AND visible = 1");
if (count($alttitle) == 1) {
	$showtitle = $alttitle[0];
	$aliaslist = getaliaslist($game, $this_type, $showtitle);
	if ($aliaslist) {
		$aliaslist = "<b title=\"" . $t->getTemplateVars("_sce_original_title") . "\">" . htmlspecialchars($gametitle) . "</b>, " . $aliaslist;
	} else {
		$aliaslist = "<b title=\"" . $t->getTemplateVars("_sce_original_title") . "\">" . htmlspecialchars($gametitle) . "</b>";
	}
} else {
	$aliaslist = getaliaslist($game, $this_type);
}

// List of files
$filelist = getfilelist($game, $this_type);

// List of persons
$personrungroups = ["" => NULL];
$q = getall("
	SELECT p.id, CONCAT(p.firstname,' ',p.surname) AS name, pgrel.title_id, pgrel.note, pgrel.convention_id, pgrel.gamerun_id, title.title_label, title.title, title.iconfile, title.iconwidth, title.iconheight, title.textsymbol, convention.name AS convention_name, COALESCE(convention.begin,convention.year,gamerun.begin) AS begin, COALESCE(convention.end, gamerun.end) AS end, COALESCE(convention.place, gamerun.location) AS location, COALESCE(convention.country, conset.country, gamerun.country) AS country, COALESCE(convention.cancelled,gamerun.cancelled) AS cancelled, CASE WHEN convention_id IS NOT NULL THEN CONCAT('c_', convention_id) WHEN gamerun_id IS NOT NULL THEN CONCAT('r_', gamerun_id) ELSE NULL END AS combined_id
	FROM person p
	INNER JOIN pgrel ON p.id = pgrel.person_id
	LEFT JOIN title ON pgrel.title_id = title.id
	LEFT JOIN gamerun ON pgrel.gamerun_id = gamerun.id
	LEFT JOIN convention ON pgrel.convention_id = convention.id
	LEFT JOIN conset ON convention.conset_id = conset.id
	WHERE pgrel.game_id = $game
	ORDER BY COALESCE(gamerun_id, convention_id) IS NULL DESC, begin, title.priority, title.id, pgrel.note = '' DESC, pgrel.note, p.surname, p.firstname, p.id
");
foreach ($q as $rs) {
	$gamerun_id = $rs['gamerun_id'];
	$convention_id = $rs['convention_id'];
	$combined_id = $rs['combined_id'];
	if (!isset($personrungroups[$combined_id])) {
		$parts = [];
		// if ($datestring = nicedateset($rs['begin'], $rs['end'])) {
		if ($datestring = substr($rs['begin'] ?? '', 0, 4)) { // just get the year for now
			if ($rs['convention_name']) {
				$datestring = $rs['convention_name'] . " (" . $datestring . ")";
			}
			$parts[] = $datestring;
		} elseif ($rs['convention_name']) {
			$parts[] = $rs['convention_name'];
		}
		if ($rs['location']) {
			$parts[] = $rs['location'];
		}
		if ($rs['country']) {
			$parts[] = getCountryName($rs['country']);
		}
		$personrungroups[$combined_id] = [
			'label' => implode(', ', $parts),
			'cancelled' => $rs['cancelled'],
			'persons' => []
		];
	}
	$title = $t->getTemplateVars("_" . $rs['title_label']);
	$htmlnote = "";
	$personhtml = "";
	if ($rs['note']) {
		$htmlnote = " (" . textlinks(htmlspecialchars($rs['note'])) . ")";
	}
	if (isset($_SESSION['user_author_id']) && $rs['id'] == $_SESSION['user_author_id']) {
		$_SESSION['can_edit_participant'][$game] = TRUE;
	}
	$personhtml .= '<tr><td style="text-align: center">';
	if ($rs['textsymbol']) { // unicode icons
		$personhtml .= '<span class="titicon" title="' . htmlspecialchars(ucfirst($title)) . '">' . $rs['textsymbol'] . '</span>';
	} elseif ($rs['iconfile']) {
		$personhtml .= '<img src="/gfx/' . rawurlencode($rs['iconfile']) . '" alt="' . htmlspecialchars(ucfirst($title)) . '" title="' . htmlspecialchars(ucfirst($title)) . '" width="' . $rs['iconwidth'] . '" height="' . $rs['iconheight'] . '" >';
	} else {
		$personhtml .= ' ';
	}
	$personhtml .= "</td>";
	$personhtml .= '<td><a href="data?person=' . $rs['id'] . '" class="person">' . htmlspecialchars($rs['name']) . '</a>' . $htmlnote . '</td>';
	$personhtml .= "</tr>" . PHP_EOL;
	$personrungroups[$combined_id]['persons'][] = $personhtml;
}

$personlist = '';
if ($personrungroups) { // ugly mix of HTML and non-HTML created above
	foreach ($personrungroups as $group) {
		if ($group['label']) {
			$cancelledcss = $group['cancelled'] ? 'cancelled' : '';
			$personlist .= '<h4 class="peoplegamerun ' . $cancelledcss . '">' . htmlspecialchars($group['label']) . '</h4>' . PHP_EOL;
		}
		if ($group['persons']) {
			$personlist .= '<table class="people indata">' . implode(' ', $group['persons']) . '</table>' . PHP_EOL;
		}
	}
}

// System
if ($r['system_translation']) {
	$syspart = '<a href="data?system=' . $r['gamesystem_id'] . '" class="system">' . htmlspecialchars($r['system_translation']) . '</a>';
	if ($r['gamesystem_extra']) $syspart .= " " . htmlspecialchars($r['gamesystem_extra']);
	$sysstring = $syspart;
} elseif ($r['gamesystem_extra']) {
	$sysstring = htmlspecialchars($r['gamesystem_extra']);
} else {
	$sysstring = "";
}

// List of cons, the game has been played at
$conlist = "";
$q = getall("
	SELECT c.id, c.name, c.year, c.begin, c.end, c.cancelled, p.event, p.event_label, p.iconfile, p.textsymbol
	FROM convention c
	INNER JOIN cgrel ON c.id = cgrel.convention_id
	INNER JOIN presentation p ON cgrel.presentation_id = p.id
	WHERE cgrel.game_id = '$game'
	ORDER BY c.year, c.begin, p.id, c.name
");
foreach ($q as $rs) {
	$coninfo = nicedateset($rs['begin'], $rs['end']);
	$conlist .= "<tr><td>";
	// Add rerun/cancelled/test icons
	if ($rs['textsymbol']) { // unicode-ikoner
		$conlist .= "<span class=\"preicon\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label']))) . "\">{$rs['textsymbol']}</span>";
	} elseif ($rs['iconfile']) {
		$conlist .= "<img src=\"/gfx/{$rs['iconfile']}\" alt=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label']))) . "\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label']))) . "\" width=\"15\" height=\"15\" /> ";
	} else {
		$conlist .= " ";
	}
	$conlist .= "</td><td>";
	$conlist .= smarty_function_con(['dataset' => $rs]);
	$conlist .= "</td></tr>" . PHP_EOL;
}

// List of runs
$runlist = "";
$q = getall("SELECT begin, end, location, country, description, cancelled FROM gamerun WHERE game_id = '$game' ORDER BY begin, end, id");
foreach ($q as $rs) {
	$runlist .= "<span" . ($rs['cancelled'] ? " class=\"cancelled\"" : "") . ">";
	$runlist .= ucfirst(nicedateset($rs['begin'], $rs['end']));
	if ($rs['location'] || $rs['description'] || $rs['country']) {
		if (nicedateset($rs['begin'], $rs['end'])) {
			$runlist .= ", ";
		}
		$runlist .= htmlspecialchars($rs['location']);
		if ($rs['country']) {
			if ($rs['location']) {
				$runlist .= ", ";
			}
			$runlist .= getCountryName($rs['country']);
		}
	}
	if (($rs['location'] || $rs['country']) && $rs['description']) {
		$runlist .= ": ";
	}
	if ($rs['description']) {
		$runlist .= htmlspecialchars($rs['description']);
	}
	$runlist .= "</span>";
	if ($rs['cancelled']) {
		// $runlist .= " [aflyst]";
		$runlist .= " [" . $t->getTemplateVars('_sce_cancelled') . "]";
	}
	$runlist .= "<br>\n";
}

// Awards
$awarddata = ['convention' => [], 'tag' => []];
$q = getall("
	SELECT a.id, a.nominationtext, a.winner, a.ranking, b.name, c.id AS convention_id, c.name AS convent_name, c.year, c.conset_id, t.tag AS tag_name, COALESCE(b.convention_id, b.tag_id) AS type_id
	FROM award_nominees a
	INNER JOIN award_categories b ON a.award_category_id = b.id
	LEFT JOIN convention c ON b.convention_id = c.id
	LEFT JOIN tag t ON b.tag_id = t.id
	WHERE a.game_id = $game
	ORDER BY c.year ASC, c.begin ASC, c.id ASC, a.winner DESC, a.id ASC
");
foreach ($q as $rs) {
	$has_nominationtext = !!$rs['nominationtext'];
	$type = ($rs['convention_id'] ? 'convention' : 'tag');
	$awardtext = '<details><summary ' . ($has_nominationtext ? '' : 'class="nonomtext"') . '>';
	$awardtext .= ($rs['winner'] ? ucfirst($t->getTemplateVars('_award_winner')) : ucfirst($t->getTemplateVars('_award_nominated'))) . ", " . htmlspecialchars($rs['name']);
	if ($rs['ranking']) {
		$awardtext .= " (" . htmlspecialchars($rs['ranking']) . ")";
	}
	$awardtext .= '</summary>';
	if ($has_nominationtext) {
		$awardtext .= '<div class="nomtext">' . nl2br(htmlspecialchars(trim($rs['nominationtext'])), FALSE) . '</div>' . PHP_EOL;
	}
	$awardtext .= '</details>';

	$awarddata[$type][$rs['type_id']]['name'] = ($type == 'convention' ? ($rs['convent_name'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "")) : $rs['tag_name']);
	$awarddata[$type][$rs['type_id']]['conset_id'] = $rs['conset_id'] ?? '';
	$awarddata[$type][$rs['type_id']]['text'][] = $awardtext;
}
$awards = [];

foreach ($awarddata['convention'] as $convention_id => $data) {
	$con_award_url = "awards?cid=" . $data['conset_id'] . "#con" . $convention_id;
	$awards[] = ['type_award_url' => $con_award_url, 'type_name' => $data['name'], 'awards' => implode("" . PHP_EOL, $data['text'])];
}
foreach ($awarddata['tag'] as $tag_id => $data) {
	$type_award_url = "awards?tid=" . $tag_id;
	$awards[] = ['type_award_url' => $type_award_url, 'type_name' => $data['name'], 'awards' => implode("" . PHP_EOL, $data['text'])];
}

// Genre
$genre = [];
$q = getall("
	SELECT g.id, g.name
	FROM genre g
	INNER JOIN ggrel ON g.id = ggrel.genre_id
	WHERE ggrel.game_id = '$game'
	ORDER BY g.name
");
foreach ($q as $rs) {
	$genre[] = '<a href="scenarier?g=' . $rs['id'] . '">' . htmlspecialchars($rs['name']) . '</a>';
}
$genre = join(", ", $genre);

// Links, trivia, tags
$linklist = getlinklist($this_id, $this_type);
$trivialist = gettrivialist($this_id, $this_type);
$taglist = gettaglist($this_id, $this_type);
if ($_SESSION['can_edit_participant'][$game] ?? FALSE) {
	foreach ($taglist as $tag_id => $tag) {
		$_SESSION['can_edit_tag'][$tag_id] = TRUE;
	}
}

// Articles
$articlesfrom = getarticles($this_id, $this_type);
$articles = getarticlereferences($this_id, $this_type);

// Thumbnail
$available_pic = hasthumbnailpic($game, $this_type);

// Userdata, entries from all users
$userlog = [];
if (isset($_SESSION['user_id'])) {
	$userlog = getuserlog($_SESSION['user_id'], $this_type, $r['id']);
	$users_entries = getalluserentries('game', $r['id']);
}

// Smarty
$t->assign('pagetitle', $showtitle);
$t->assign('type', $this_type);
$t->assign('type2', 'game');

$t->assign('id', $game);
$t->assign('title', $showtitle);
$t->assign('pic', $available_pic);
$t->assign('ogimage', getimageifexists($this_id, $this_type));
$t->assign('sysstring', $sysstring);
$t->assign('alias', $aliaslist);
$t->assign('filelist', $filelist);
$t->assign('filedir', getcategorydir($this_type));

$t->assign('personlist', $personlist);
$t->assign('person_extra', $r['person_extra']);
$t->assign('descriptions', $descriptions);
$t->assign('internal', $internal);
$t->assign('gms', $gms);
$t->assign('players', $players);
$t->assign('participants', $participants);
$t->assign('boardgame', $r['boardgame']);
$t->assign('user_can_edit_participants', $_SESSION['can_edit_participant'][$game] ?? FALSE);
$t->assign('conlist', $conlist);
$t->assign('runlist', $runlist);
$t->assign('awards', $awards);
$t->assign('genre', $genre);
$t->assign('articlesfrom', $articlesfrom);
$t->assign('articles', $articles);
$t->assign('trivia', $trivialist);
$t->assign('link', $linklist);
$t->assign('tags', $taglist);
$t->assign('json_tags', TRUE);
$t->assign('user_can_edit_tag', $_SESSION['can_edit_tag'] ?? FALSE);

$t->assign('user_read', in_array('read', $userlog));
$t->assign('user_read_html', getdynamicgamehtml($game, 'read', in_array('read', $userlog)));
$t->assign('user_gmed', in_array('gmed', $userlog));
$t->assign('user_gmed_html', getdynamicgamehtml($game, 'gmed', in_array('gmed', $userlog)));
$t->assign('user_played', in_array('played', $userlog));
$t->assign('user_played_html', getdynamicgamehtml($game, 'played', in_array('played', $userlog)));
$t->assign('users_entries', $users_entries ?? FALSE);

if (in_array('LGBTQ', $taglist) || in_array('Queer', $taglist) || in_array('Queerness', $taglist)) {
	$t->assign('lgbtmenu', TRUE);
}

$t->display('data.tpl');
