<?php
$this_type = 'convent';

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

$persons_limit = 4;
$scenlistdata = $boardlistdata = $gamelist = [];
$oo = $_GET['oo'] ?? FALSE; // sort order for organizers
$edit = $_GET['edit'] ?? FALSE;

function antaltxt ($new, $rerun, $cancelled, $total, $type = 'sce') {
		if ($total == 0) {
			return "";
		}
		$antaltxt = sprintf("%d %s",$total,($type == 'sce' ? ($total == 1 ? "scenarie" : "scenarier") : 'brætspil') );
		if ($rerun > 0 && $cancelled > 0) {
			$antaltxt .= sprintf(", heraf %d %s, %d %s og %d %s",$new,($new == 1 ? "nyt" : "nye"),$rerun,($rerun == 1 ? "rerun" : "reruns"),$cancelled,($cancelled==1?"aflyst":"aflyste"));
		} elseif ($rerun > 0 && $new > 0) {
			$antaltxt .= sprintf(", heraf %d %s og %d %s",$new,($new == 1 ? "nyt" : "nye"),$rerun,($rerun == 1 ? "rerun" : "reruns") );
		} elseif ($cancelled > 0 && $new > 0) {
			$antaltxt .= sprintf(", heraf %d %s og %d %s",$new,($new == 1 ? "nyt" : "nye"),$cancelled,($cancelled==1?"aflyst":"aflyste") );
		} elseif ($rerun == $total) {
			$antaltxt .= ", udelukkende reruns";
		} elseif ($cancelled == $total) {
			$antaltxt .= ", udelukkende aflysninger";
		}
		return $antaltxt;

}

// achievements
if ($con == 26) award_achievement(79); // X-Con
if ($con == 127 || $con == 743) award_achievement(80); // 1. Copenhagen Gamecon (Viking Con I) or Konvent '77 (GothCon I)

$convent = getrow("SELECT convent.id, convent.name, convent.intern, convent.year, convent.description, begin, end, place, conset_id, confirmed, cancelled, conset.name AS cname, COALESCE(convent.country, conset.country) AS country FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE convent.id = $con");
if (is_null($convent['id']) ) {
	$t->assign('content', $t->getTemplateVars('_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
	$t->display('default.tpl');
	exit;
}
$showtitle = $conventname = $convent['name'];
$intern = ( ( $_SESSION['user_editor'] ?? FALSE ) ? $convent['intern'] : ""); // only set intern if editor

// List of files
$filelist = getfilelist($con,$this_type);

// Part of con series? Find previous and next.
if ($convent['conset_id']) {
	$cname = ($convent['conset_id'] == 40 ? $t->getTemplateVars('_cons_other') : $convent['cname']);
	$delafout = "<a href=\"data?conset=" . $convent['conset_id'] . "\" class=\"con\">" . htmlspecialchars( $cname ) . "</a>";
	$qq = getall("
		SELECT id, name, year, begin, end
		FROM convent 
		WHERE conset_id = " . $convent['conset_id'] . "
		ORDER BY year, begin, name
	");
	unset($seriedata,$seriecount,$seriethis);
	$seriecount = 0;
	foreach($qq AS $row) {
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
		if (isset($seriedata['id'][($seriethis-1)])) {
			$arrows['prev'] = [ 'active' => TRUE, 'conid' => $seriedata['id'][($seriethis-1)], 'name' => $seriedata['name'][($seriethis-1)] . " (" . yearname($seriedata['year'][($seriethis-1)]) . ")" ];
		} else {
			$arrows['prev'] = [ 'active' => FALSE ];
		}
		if (isset($seriedata['id'][($seriethis+1)])) {
			$arrows['next'] = [ 'active' => TRUE, 'conid' => $seriedata['id'][($seriethis+1)], 'name' => $seriedata['name'][($seriethis+1)] . " (" . yearname($seriedata['year'][($seriethis+1)]) . ")" ];
		} else {
			$arrows['next'] = [ 'active' => FALSE ];
		}
	}
}

// List of games

$sce_new = $sce_rerun = $sce_cancelled = $board_new = $board_rerun = $board_cancelled = 0;

$q = getall("
	SELECT sce.id, sce.title, sce.boardgame, pre.id AS preid, pre.event, pre.event_label, pre.iconfile, pre.textsymbol, sce.sys_ext, sys.id AS sys_id, sys.name AS sys_name, COUNT(files.id) AS files, aut.id AS person_id, CONCAT(firstname,' ',surname) AS person_name, alias.label, COALESCE(alias.label, sce.title) AS title_translation
	FROM csrel
	INNER JOIN sce ON sce.id = csrel.sce_id
	LEFT JOIN pre ON csrel.pre_id = pre.id 
	LEFT JOIN sys ON sce.sys_id = sys.id
	LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
	LEFT JOIN asrel ON sce.id = asrel.sce_id AND asrel.tit_id IN(1,4,5)
	LEFT JOIN aut ON aut.id = asrel.aut_id
	LEFT JOIN alias ON sce.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE csrel.convent_id = $con
	GROUP BY sce.id, pre.id, aut.id
	ORDER BY boardgame, title_translation, aut.surname, aut.firstname
");

foreach ($q AS $r) {
	$sid = $r['id'];
	if ( ! isset($gamelist[$sid])) {
		$gamelist[$sid] = ['game' => ['title' => $r['title'], 'title_translation' => $r['title_translation'], 'person_extra' => $r['aut_extra'], 'files' => (int) $r['files'], 'boardgame' => (int) $r['boardgame'], 'system_id' => $r['sys_id'], 'system_name' => $r['sys_name'], 'system_ext' => $r['sys_ext'], 'pre_id' => $r['pre_id'], 'pre_event' => $r['pre_event'], 'pre_event_label' => $r['event_label'], 'pre_iconfile' => $r['iconfile'], 'pre_textsymbol' => $r['textsymbol'] ], 'person' => [] ];
	}
	if ($r['person_id']) {
		$gamelist[$sid]['person'][$r['person_id']] = $r['person_name'];
	}
}

foreach($gamelist AS $game_id => $game) {
	$datalistdata = [];
	$useroptions = [];
	if ($_SESSION['user_id']) {
		if ($game['game']['boardgame']) {
			$options = getuserlogoptions('boardgame');
		} else {
			$options = getuserlogoptions('scenario');
		}
		foreach($options AS $type) {
			if ($type != NULL) {
				$useroptions[$type] = getdynamicscehtml($game_id, $type, $userlog[$game_id][$type] ?? FALSE );
			}
		}
	}

	/*
	if ($rs['boardgame'] == 0) {
		if ($rs['preid'] == 1) {
			$sce_new++;
		} elseif ($rs['preid'] == 2 || $rs['preid'] == 3) {
			$sce_rerun++;
		} elseif ($rs['preid'] == 99) {
			$sce_cancelled++;
		}
	} else {
		if ($rs['preid'] == 1) {
			$board_new++;
		} elseif ($rs['preid'] == 2 || $rs['preid'] == 3) {
			$board_rerun++;
		} elseif ($rs['preid'] == 99) {
			$board_cancelled++;
		}
	}
	*/
	$personlist = [];
	$personlistextra = [];
	$person_count = 0;
	foreach($game['person'] AS $person_id => $person_name) {
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

	if ( $game['game']['pre_textsymbol'] ) { // unicode icons
		$runsymbol = "<span class=\"preicon\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $game['game']['pre_event_label'] ) ) ) . "\">" . $game['game']['pre_textsymbol'] . "</span>";
	} elseif ( $game['game']['pre_iconfile'] ) {
		$runsymbol = "<img src=\"/gfx/" . $game['game']['pre_iconfile'] . "\" alt=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $game['game']['pre_event_label'] ) ) ) . "\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $game['game']['pre_event_label'] ) ) ) . "\" width=\"15\" height=\"15\" />";
	} else {
		$runsymbol = "";
	}

	$datalistdata = [
		'id' => $game_id,
		'userdyn' => $useroptions,
		'filescount' => $game['game']['files'],
		'runsymbol' => $runsymbol,
		'title' => $game['game']['title_translation'],
		'authtml' => $personhtml,
		'autextracount' => count($personlistextra),
		'autextrahtml' => $personextrahtml ?? '',
		'systemhtml' => $sysstring ?? FALSE,
		'system_id' => $game['game']['system_id'],
		'system_name' => $game['game']['system_name'],
		'system_extra' => $game['game']['system_ext'],
		'boardgame' => $game['game']['boardgame'],
	];

	if ($game['game']['boardgame']) {
		$boardlistdata[] = $datalistdata;
	} else {
		$scenlistdata[] = $datalistdata;
	}

// Tilføj antal scenarier, nye som reruns:
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
	SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.ranking, a.sce_id, b.id AS category_id, b.convent_id, b.name AS category_name, c.year, c.name AS con_name, c.conset_id, d.title, COALESCE(e.label,d.title) AS title_translation
	FROM award_nominees a
	INNER JOIN award_categories b ON a.award_category_id = b.id
	LEFT JOIN convent c ON b.convent_id = c.id
	LEFT JOIN sce d ON a.sce_id = d.id
	LEFT JOIN alias e ON d.id = e.data_id AND e.category = 'sce' AND e.language = '" . LANG . "' AND e.visible = 1
	WHERE c.id = $con
	ORDER BY c.year DESC, a.winner DESC, a.id
");

$awardset = [];
$awardnominees = [];
$html = "";
foreach ($award_nominees AS $nominee) {
	$cid = $nominee['conset_id'];
	$con_id = $nominee['convent_id'];
	$cat_id = $nominee['category_id'];
	if (!$cid) $cid = 0;
	$awardnominees[$cid][$con_id]['name'] = $nominee['con_name'];
	$awardnominees[$cid][$con_id]['year'] = $nominee['year'];
	$awardnominees[$cid][$con_id]['categories'][$cat_id]['name'] = $nominee['category_name'];
	$awardnominees[$cid][$con_id]['categories'][$cat_id]['nominees'][] = ['id' => $nominee['id'], 'name' => $nominee['name'], 'nominationtext' => $nominee['nominationtext'], 'winner' => $nominee['winner'], 'ranking' => $nominee['ranking'], 'sce_id' => $nominee['sce_id'], 'title' => $nominee['title_translation'] ];
}

if ($awardnominees) {
	foreach ((array) $awardnominees[$cid] AS $conid => $aconvent) {
		$html .= "<div class=\"awardyear\" data-year=\"" . $aconvent['year'] . "\">";
		$html .= "<div class=\"awardblock\">" . PHP_EOL;
		foreach($aconvent['categories'] AS $category) {
			$html .= PHP_EOL . "<div class=\"awardcategory\" data-category=\"" . htmlspecialchars($category['name']) . "\">" . PHP_EOL;
			$html .= "<h4>" . htmlspecialchars($category['name']) . "</h4>" . PHP_EOL;
			foreach($category['nominees'] AS $nominee) {
				$class = ($nominee['winner'] == 1 ? "winner" : "nominee");
				$html .= "<div class=\"" . $class . "\">";
				$html .= "<h5 class=\"" . $class . "\">";
				$html .= "<span class=\"" . $class . "\">";
				if ($nominee['sce_id']) {
					$html .= getdatahtml('sce', $nominee['sce_id'], $nominee['title']);
				} else {
					$html .= htmlspecialchars($nominee['name']);
				}
				$html .= "</span>";
				if ($nominee['nominationtext']) {
					$nt_id = "nominee_text_" . $nominee['id'];
					$html .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"" . htmlspecialchars($t->getTemplateVars('_award_show_nominationtext') ) . "\">[+]</span>";

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
	$organizerlist = getorganizerlist($con,$this_type, 'a.id');
} else {
	$organizerlist = getorganizerlist($con,$this_type);
}

// List of aliases, alternative title?
$alttitle = getcol("SELECT label FROM alias WHERE data_id = '$con' AND category = '$this_type' AND language = '$lang' AND visible = 1");
if ( count( $alttitle ) == 1 ) {
	$showtitle = $alttitle[0];
	$aliaslist = getaliaslist($con, $this_type, $showtitle);
	if ( $aliaslist ) {
		$aliaslist = htmlspecialchars( $conventname ) . ", " . $aliaslist;
	} else {
		$aliaslist = htmlspecialchars( $conventname );
	}
} else {
	$aliaslist = getaliaslist($con, $this_type);
}

// Trivia and links
$trivialist = gettrivialist($con,$this_type);
$linklist = getlinklist($con,$this_type);

// Thumbnail
$available_pic = hasthumbnailpic($con, $this_type);

// Userdata
$userlog = array();
if ($_SESSION['user_id']) {
	$userlog = getuserlog($_SESSION['user_id'],$this_type,$convent['id']);
	$users_entries = getalluserentries('convent', $convent['id']);
}

// Edit mode?
$editorganizers = ($edit == 'organizers');
$editmode = ( isset($_SESSION['user_id']) && $editorganizers );
$people = [];
if ($editmode) {
	$people = getcol("SELECT CONCAT(id, ' - ', firstname, ' ', surname) AS id_name FROM aut ORDER BY firstname, surname");	
}
$json_people = json_encode($people);

// Smarty
$t->assign('pagetitle',$showtitle." (" . ( $convent['year'] ? yearname($convent['year']) : "?" ) . ")");
$t->assign('type',$this_type);

$t->assign('id',$con);
$t->assign('name', $showtitle);
$t->assign('year',($convent['year'] ? $convent['year'] : "?") );
$t->assign('arrowset',$arrows);
$t->assign('pic',$available_pic);
$t->assign('ogimage', getimageifexists($con, 'convent') );
$t->assign('place',$convent['place']);
$t->assign('countrycode',$convent['country']);
$t->assign('dateset',nicedateset($convent['begin'],$convent['end']));
$t->assign('partof',$delafout);
$t->assign('confirmed',$convent['confirmed']);
$t->assign('cancelled',$convent['cancelled']);
$t->assign('description',$convent['description']);
$t->assign('intern',$intern);
$t->assign('scenlistdata',$scenlistdata);
$t->assign('boardlistdata',$boardlistdata);
$t->assign('organizerlist',$organizerlist);
$t->assign('award',$awardlist);
$t->assign('trivia',$trivialist);
$t->assign('link',$linklist);
$t->assign('alias',$aliaslist);
$t->assign('filelist',$filelist);
$t->assign('filedir', getcategorydir($this_type) );

$t->assign('editorganizers', $editorganizers);
$t->assign('editmode', $editmode );
$t->assign('json_people', $json_people );
$t->assign('user_can_edit_organizers',$_SESSION['can_edit_organizers'] ?? FALSE );

$t->assign('user_visited',in_array('visited',$userlog));
$t->assign('users_entries', $users_entries ?? FALSE);

$t->display('data.tpl');
?>
