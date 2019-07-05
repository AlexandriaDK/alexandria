<?php
$this_type = 'sce';
if ($game) {
	$scenarie = $game;
}

$r = getrow("
	SELECT sce.id, title, sce.description, intern, sys_id, sys_ext, aut_extra, sce.ottowinner, sce.rlyeh_id, gms_min, gms_max, players_min, players_max, participants_extra, boardgame, sys.name AS sysname FROM sce
	LEFT JOIN sys ON sys.id = sys_id
	WHERE sce.id = '$scenarie'
");


// achievements
if ($scenarie == 161)      award_achievement(55); // De Professionelle
if ($scenarie == 3812)     award_achievement(56); // Fordømt Ungdom
if ($scenarie == 3827)     award_achievement(57); // Paninaro
if (in_array($scenarie, [3755, 4615, 4516, 4597, 4461] ) ) award_achievement(98); // Cykling
if ($r['ottowinner'] == 1) award_achievement(62); // Otto winner
if ($r['rlyeh_id'] > 0)    award_achievement(12); // R'lyeh scenario

if ($r['id'] == 0) {
	$t->assign('content',"Beklager - intet scenarie fundet!");
	$t->assign('pagetitle',"Ikke fundet");
	$t->display('default.tpl');

} else {
//	$description = $r['description'];
	$description = getone("SELECT description FROM game_description WHERE game_id = $scenarie LIMIT 1");
	$descriptions = getall("SELECT description, language, note FROM game_description WHERE game_id = $scenarie ORDER BY (LEFT(language, 2) = '" . LANG . "') DESC, LENGTH(language), priority, language");
	#$descriptions = getone("SELECT description, language, note, (LEFT(language, 2) = '" . LANG . "') AS languagematch FROM game_description WHERE game_id = $scenarie ORDER BY languagematch DESC, LENGTH(language), priority, language");

// Beskrivelse af deltagere:
	$participants = [];
	$gms = $players = "";
	if ($r['gms_min'] !== NULL) {
		if ($r['gms_min'] == 0 && $r['gms_max'] == 0) {
			$gms_text = "Ingen GM's";
		} else {
			$gms_text = $r['gms_min'];
			if ($r['gms_max'] != $r['gms_min']) {
				$gms_text .= "-" . $r['gms_max'];
			}
			$gms_text .= ($r['gms_min'] == 1 && $r['gms_max'] == 1 ? " GM" : " GM's");
		}
		$participants[] = $gms_text;
		$gms = ($r['gms_max'] != $r['gms_min'] ? $r['gms_min'] . "-" . $r['gms_max'] : $r['gms_min']);
	}	
	if ($r['players_min'] !== NULL) {
		$players_text = $r['players_min'];
		if ($r['players_max'] != $r['players_min']) {
			$players_text .= "-" . $r['players_max'];
		}
		$players_text .= ($r['players_min'] == 1 && $r['players_max'] == 1 ? " spiller" : " spillere");
		$participants[] = $players_text;
		$players = ($r['players_max'] != $r['players_min'] ? $r['players_min'] . "-" . $r['players_max'] : $r['players_min']);
	}
	if ($r['participants_extra']) {
		$participants[] = $r['participants_extra'];
	}
	$participants = implode(', ',$participants);

// Liste over aliaser
	$aliaslist = getaliaslist($scenarie,$this_type);

// Liste over filer
	$filelist = getfilelist($scenarie,$this_type);

// Liste over forfattere, m.m.
	$q = getall("SELECT aut.id, CONCAT(aut.firstname,' ',aut.surname) AS name, asrel.tit_id, title.title, title.iconfile, title.iconwidth, title.iconheight, title.textsymbol FROM aut, asrel, title WHERE aut.id = asrel.aut_id AND asrel.tit_id = title.id AND asrel.sce_id = '$scenarie' ORDER BY title.priority, title.id, aut.surname, aut.firstname");
	foreach($q AS $rs) {
		if ( $rs['id'] == $_SESSION['user_author_id'] ) {
			$_SESSION['can_edit_participant'][$scenarie] = TRUE;
		}
		$forflist .= "<tr><td style=\"text-align: center\">";
		if ($rs['textsymbol']) { // unicode-ikoner
			$forflist .= "<span class=\"titicon\" title=\"{$rs['title']}\">{$rs['textsymbol']}</span>";
		} elseif ($rs['iconfile']) {
			$forflist .= "<img src=\"/gfx/{$rs['iconfile']}\" alt=\"{$rs['title']}\" title=\"{$rs['title']}\" width=\"{$rs['iconwidth']}\" height=\"{$rs['iconheight']}\" />";
		} else {
			$forflist .= " ";
		}
		$forflist .= "</td>";
		$scenlist .= "<td><a href=\"data?scenarie={$rs['id']}\" class=\"scenarie\">{$rs['title']}</a></td>";
		$forflist .= "<td><a href=\"data?person=$rs[id]\" class=\"person\">{$rs['name']}</a></td>";
		$forflist .= "</tr>\n";
	}

	if ($forflist) {
		$forflist = "<table class=\"people indata\">\n$forflist\n</table>";
	}


// Intern data:
	if ($intern == TRUE) {
		$internpart = nl2br($r['intern']);
	}

// System:
	if ($r['sysname']) {
		$syspart = "<a href=\"data?system={$r['sys_id']}\" class=\"system\">".htmlspecialchars($r['sysname'])."</a>";
		if ($r['sys_ext']) $syspart .= " ".htmlspecialchars($r['sys_ext']);
		$sysstring = $syspart;
	} elseif ($r['sys_ext']) {
		$sysstring = htmlspecialchars($r['sys_ext']);
	}

// Liste over cons, scenariet har været spillet på:
// Nu kan vi sortere efter cons' tidspunkt, så ingen grund til at sortere på pre.id, medmindre ingen dato er kendt på cons fra samme år.
	$conlist = "";
	$q = getall("SELECT convent.id, convent.name, convent.year, convent.begin, convent.end, pre.event, pre.event_label, pre.iconfile, pre.textsymbol FROM convent, csrel, pre WHERE convent.id = csrel.convent_id AND csrel.pre_id = pre.id AND csrel.sce_id = '$scenarie' ORDER BY convent.year, convent.begin, pre.id, convent.name");
	foreach($q AS $rs) {
		$coninfo = nicedateset($rs['begin'],$rs['end']);
		$conlist .= "<tr><td>";
// Sæt evt. rerun/aflyst-ikoner på
		if ($rs['textsymbol']) { // unicode-ikoner
			$conlist .= "<span class=\"preicon\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) ."\">{$rs['textsymbol']}</span>";
		} elseif ($rs['iconfile']) {
			$conlist .= "<img src=\"/gfx/{$rs['iconfile']}\" alt=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) ."\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) ."\" width=\"14\" height=\"14\" /> ";
		} else {
			$conlist .= " ";
		}
		$conlist .= "</td><td>";
		$conlist .= "<a href=\"data?con={$rs['id']}\" class=\"con\" title=\"$coninfo\">{$rs['name']} ({$rs['year']})</a>";
#		if ($rs['event']) $conlist .= " ({$rs['event']})";
		$conlist .= "</td></tr>" . PHP_EOL;
	}

// Liste over afviklinger for evt. live-scenarier
	$runlist = "";
	$q = getall("SELECT begin, end, location, description, cancelled FROM scerun WHERE sce_id = '$scenarie' ORDER BY begin, end");
	foreach($q AS $rs) {
		$runlist .= "<span" . ($rs['cancelled'] ? " class=\"cancelled\"" : "") . ">";
		$runlist .= nicedateset($rs['begin'],$rs['end']);
		if ($rs['location']) {
			if ( nicedateset($rs['begin'],$rs['end']) ) {
				$runlist .= ", ";
			}
			$runlist .= htmlspecialchars($rs['location']);
		}
		if ($rs['description']) {
			$runlist .= ": ".htmlspecialchars($rs['description']);
		}
		$runlist .= "</span>";
		if ($rs['cancelled']) {
			// $runlist .= " [aflyst]";
			$runlist .= " [" . $t->getTemplateVars('_sce_cancelled') . "]";
		}
		$runlist .= "<br>\n";
	}

// Priser
	$awarddata = [];
	$q = getall("SELECT a.id, a.nominationtext, a.winner, a.ranking, b.name, c.id AS convent_id, c.name AS convent_name, c.year, c.conset_id FROM award_nominees a INNER JOIN award_categories b ON a.award_category_id = b.id INNER JOIN convent c ON b.convent_id = c.id WHERE a.sce_id = $scenarie ORDER BY c.year ASC, c.begin ASC, c.id ASC, a.winner DESC, a.id ASC");
	foreach($q AS $rs) {
		$awardtext = ($rs['winner'] ? "Vinder" : "Nomineret" ) . ", " . htmlspecialchars($rs['name']);
		if ($rs['ranking']) {
			$awardtext .= " (" . htmlspecialchars($rs['ranking']) . ")";
		}
		if ($rs['nominationtext']) {
			$nt_id = "nominee_text_" . $rs['id'];
			$awardtext .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" style=\"font-weight: bold;\" title=\"Vis nomineringstekst\">[+]</span>";
			$awardtext .= "<div class=\"nomtext\" style=\"display: none;\" id=\"$nt_id\">" . nl2br(htmlspecialchars(trim($rs['nominationtext'])), FALSE) . "</div>" . PHP_EOL;

		}

		// $awardtext .=  " – " . $rs['conventname'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "") . "<br>" . PHP_EOL;
		$awarddata[$rs['convent_id']]['name'] = $rs['convent_name'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "");
		$awarddata[$rs['convent_id']]['conset_id'] = $rs['conset_id'];
		$awarddata[$rs['convent_id']]['text'][] = $awardtext;
	}
	$awards = [];

	foreach($awarddata AS $convent_id => $data) {
		$con_award_url = "awards?cid=" . $data['conset_id'] . "#con" . $convent_id;
		$awards[] = [ 'con_award_url' => $con_award_url, 'con_name' => $data['name'], 'awards' => implode("<br>" . PHP_EOL, $data['text']) ];
	}

// Genre:
	$genre = [];
	$q = getall("SELECT gen.id, gen.name FROM gsrel, gen WHERE gen.id = gsrel.gen_id AND gsrel.sce_id = '$scenarie' ORDER BY gen.name");
	foreach($q AS $rs) {
		$genre[] = '<a href="scenarier?g='.$rs['id'].'">'.htmlspecialchars($rs['name']).'</a>';
	}
	$genre = join(", ",$genre);

// Links, trivia, tags
	$linklist = getlinklist($scenarie,$this_type);
	$trivialist = gettrivialist($scenarie,$this_type);
	$taglist = gettaglist($scenarie,$this_type);
	if ($_SESSION['can_edit_participant'][$scenarie] == TRUE) {
		foreach($taglist AS $tag_id => $tag) {
			$_SESSION['can_edit_tag'][$tag_id] = TRUE;
		}
	}
	$alltags = getalltags();
	$json_alltags = json_encode($alltags);

// Evt. forside-billede
	$available_pic = 0;
 
	// Create thumbnail
	if (file_exists("gfx/scenarie/l_".$scenarie.".jpg") && !file_exists("gfx/scenarie/s_".$scenarie.".jpg")) {
		image_rescale_save('gfx/scenarie/l_'.$scenarie.'.jpg','gfx/scenarie/s_'.$scenarie.'.jpg',200,200);
	}

	if (file_exists("gfx/scenarie/s_".$scenarie.".jpg")) {
		$available_pic = 1;
	}

// Userdata, entries from all users
	$userlog = [];
	if ($_SESSION['user_id']) {
		$userlog = getuserlog($_SESSION['user_id'],$this_type,$r['id']);
		$users_entries = getalluserentries('sce', $r['id']);
	}

// April fools day
	if ( $_REQUEST['april'] == 1 ) {
		require("april_scenario.inc.php");
		$description = aprildescription($scenarie);
	}

// Smarty
	$t->assign('pagetitle',$r['title']);
	$t->assign('type',$this_type);
	$t->assign('type2','game');

	$t->assign('id',$scenarie);
	$t->assign('title',$r['title']);
	$t->assign('pic',$available_pic);
	$t->assign('ogimage', getimageifexists($scenarie, 'scenarie') );
	$t->assign('sysstring',$sysstring);
	$t->assign('alias',$aliaslist);
	$t->assign('filelist',$filelist);
	$t->assign('filedir','scenario');

	$t->assign('forflist',$forflist);
	$t->assign('aut_extra',$r['aut_extra']);
	$t->assign('description',$description);
	$t->assign('descriptions',$descriptions);
	$t->assign('descriptionscount', count($descriptions) );
	$t->assign('gms',$gms);
	$t->assign('players',$players);
	$t->assign('participants',$participants);
	$t->assign('boardgame',$r['boardgame']);
	$t->assign('user_can_edit_participants',$_SESSION['can_edit_participant'][$scenarie]);
	$t->assign('conlist',$conlist);
	$t->assign('runlist',$runlist);
	$t->assign('awards',$awards);
	$t->assign('genre',$genre);
	$t->assign('trivia',$trivialist);
	$t->assign('link',$linklist);
	$t->assign('tags',$taglist);
	$t->assign('json_alltags',$json_alltags);
	$t->assign('user_can_edit_tag',$_SESSION['can_edit_tag'] );

	$t->assign('user_read',in_array('read',$userlog));
	$t->assign('user_read_html',getdynamicscehtml($scenarie,'read',in_array('read',$userlog)));
	$t->assign('user_gmed',in_array('gmed',$userlog));
	$t->assign('user_gmed_html',getdynamicscehtml($scenarie,'gmed',in_array('gmed',$userlog)));
	$t->assign('user_played',in_array('played',$userlog));
	$t->assign('user_played_html',getdynamicscehtml($scenarie,'played',in_array('played',$userlog)));
	$t->assign('users_entries', $users_entries);

	$t->display('data.tpl');
}

?>
