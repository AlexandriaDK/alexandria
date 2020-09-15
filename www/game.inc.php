<?php
$this_type = 'sce';
if ($game) {
	$scenarie = $game;
}

$r = getrow("
	SELECT sce.id, title, sce.intern, sys_id, sys_ext, aut_extra, sce.ottowinner, sce.rlyeh_id, gms_min, gms_max, players_min, players_max, participants_extra, boardgame, sys.name AS sysname FROM sce
	LEFT JOIN sys ON sys.id = sys_id
	WHERE sce.id = $scenarie
");
$showtitle = $gametitle = $r['title'];

// Achievements
if ($scenarie == 161)      award_achievement(55); // De Professionelle
if ($scenarie == 3812)     award_achievement(56); // Fordømt Ungdom
if ($scenarie == 3827)     award_achievement(57); // Paninaro
if (in_array($scenarie, [3755, 4615, 4516, 4597, 4461] ) ) award_achievement(98); // Bicycling
if ($r['ottowinner'] == 1) award_achievement(62); // Otto winner
if ($r['rlyeh_id'] > 0)    award_achievement(12); // R'lyeh scenario

if ($r['id'] == 0) {
	header( "HTTP/1.1 404 Not Found ");
	$t->assign('content', $t->getTemplateVars('_sce_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
	$t->display('default.tpl');
	exit;
}
$descriptions = getall("SELECT description, language, note FROM game_description WHERE game_id = $scenarie ORDER BY (LEFT(language, 2) = '" . LANG . "') DESC, LENGTH(language), priority, language");
foreach ($descriptions AS $d_id => $description) {
	list($language) = explode( " ", $description['language'] );
	if (preg_match('/^[a-z]{2}$/', $language) ) {
		$descriptions[$d_id]['langcode'] = $language;
		$descriptions[$d_id]['langname'] = getLanguageName( $language );
	}
}
$intern = ( ( $_SESSION['user_editor'] ?? FALSE ) ? $r['intern'] : ""); // only set intern if editor

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
		$gms_text .= " " . ($r['gms_min'] == 1 && $r['gms_max'] == 1 ? $t->getTemplateVars('_sce_gm') : $t->getTemplateVars('_sce_gms') );
	}
	$participants[] = $gms_text;
	$gms = ($r['gms_max'] != $r['gms_min'] ? $r['gms_min'] . "-" . $r['gms_max'] : $r['gms_min']);
}	
if ($r['players_min'] !== NULL) {
	$players_text = $r['players_min'];
	if ($r['players_max'] != $r['players_min']) {
		$players_text .= "-" . $r['players_max'];
	}
	$players_text .= " " . ($r['players_min'] == 1 && $r['players_max'] == 1 ? $t->getTemplateVars('_sce_player') : $t->getTemplateVars('_sce_players') );
	$participants[] = $players_text;
	$players = ($r['players_max'] != $r['players_min'] ? $r['players_min'] . "-" . $r['players_max'] : $r['players_min']);
}
if ($r['participants_extra']) {
	$participants[] = $r['participants_extra'];
}
$participants = implode(', ',$participants);

// List of aliases, alternative title?
$alttitle = getcol("SELECT label FROM alias WHERE data_id = '$scenarie' AND category = '$this_type' AND language = '$lang' AND visible = 1");
if ( count( $alttitle ) == 1 ) {
	$showtitle = $alttitle[0];
	$aliaslist = getaliaslist($scenarie, $this_type, $showtitle);
	if ( $aliaslist ) {
		$aliaslist = "<b title=\"" . $t->getTemplateVars( "_sce_original_title" ) . "\">" . htmlspecialchars( $gametitle ) . "</b>, " . $aliaslist;
	} else {
		$aliaslist = "<b title=\"" . $t->getTemplateVars( "_sce_original_title" ) . "\">" . htmlspecialchars( $gametitle ) . "</b>";
	}
} else {
	$aliaslist = getaliaslist($scenarie, $this_type);
}

// List of files
$filelist = getfilelist($scenarie,$this_type);

// List of authors, ...
$forflist = $scenlist = "";
$q = getall("
	SELECT aut.id, CONCAT(aut.firstname,' ',aut.surname) AS name, asrel.tit_id, asrel.note, title.title_label, title.title, title.iconfile, title.iconwidth, title.iconheight, title.textsymbol
	FROM aut
	INNER JOIN asrel ON aut.id = asrel.aut_id
	LEFT JOIN title ON asrel.tit_id = title.id
	WHERE asrel.sce_id = '$scenarie'
	ORDER BY title.priority, title.id, aut.surname, aut.firstname, aut.id
");
foreach($q AS $rs) {
	$title = $t->getTemplateVars( "_" . $rs['title_label'] );
	$htmlnote = "";
	if ( $rs['note'] ) {
		$htmlnote = " (" . textlinks( htmlspecialchars( $rs['note'] ) ) . ")";
	}
	if ( isset($_SESSION['user_author_id']) && $rs['id'] == $_SESSION['user_author_id'] ) {
		$_SESSION['can_edit_participant'][$scenarie] = TRUE;
	}
	$forflist .= '<tr><td style="text-align: center">';
	if ($rs['textsymbol']) { // unicode-ikoner
		$forflist .= '<span class="titicon" title="' . htmlspecialchars( ucfirst( $title ) ) . '">' . $rs['textsymbol'] . '</span>';
	} elseif ($rs['iconfile']) {
		$forflist .= '<img src="/gfx/' . rawurlencode( $rs['iconfile'] ) . '" alt="' . htmlspecialchars( ucfirst( $title ) ) . '" title="' . htmlspecialchars( ucfirst( $title ) ) . '" width="' . $rs['iconwidth'] . '" height="' . $rs['iconheight'] . '" >';
	} else {
		$forflist .= ' ';
	}
	$forflist .= "</td>";
	$scenlist .= '<td><a href="data?scenarie=' . $rs['id'] . '" class="scenarie">' . htmlspecialchars( $rs['title'] ) . '</a></td>';
	$forflist .= '<td><a href="data?person=' . $rs['id'] . '" class="person">' .htmlspecialchars( $rs['name'] ) . '</a>' . $htmlnote . '</td>';
	$forflist .= "</tr>" . PHP_EOL;
}

if ($forflist) {
	$forflist = "<table class=\"people indata\">\n$forflist\n</table>";
}


// System
if ($r['sysname']) {
	$syspart = "<a href=\"data?system={$r['sys_id']}\" class=\"system\">".htmlspecialchars($r['sysname'])."</a>";
	if ($r['sys_ext']) $syspart .= " ".htmlspecialchars($r['sys_ext']);
	$sysstring = $syspart;
} elseif ($r['sys_ext']) {
	$sysstring = htmlspecialchars($r['sys_ext']);
} else {
	$sysstring = "";
}

// List of cons, the game has been played at
$conlist = "";
$q = getall("SELECT convent.id, convent.name, convent.year, convent.begin, convent.end, convent.cancelled, pre.event, pre.event_label, pre.iconfile, pre.textsymbol FROM convent, csrel, pre WHERE convent.id = csrel.convent_id AND csrel.pre_id = pre.id AND csrel.sce_id = '$scenarie' ORDER BY convent.year, convent.begin, pre.id, convent.name");
foreach($q AS $rs) {
	$coninfo = nicedateset($rs['begin'],$rs['end']);
	$conlist .= "<tr><td>";
// Add rerun/cancelled/test icons
	if ($rs['textsymbol']) { // unicode-ikoner
		$conlist .= "<span class=\"preicon\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) ."\">{$rs['textsymbol']}</span>";
	} elseif ($rs['iconfile']) {
		$conlist .= "<img src=\"/gfx/{$rs['iconfile']}\" alt=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) ."\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) ."\" width=\"15\" height=\"15\" /> ";
	} else {
		$conlist .= " ";
	}
	$conlist .= "</td><td>";
	$conlist .= smarty_function_con( [ 'dataset' => $rs ] );
	$conlist .= "</td></tr>" . PHP_EOL;
}

// List of runs
$runlist = "";
$q = getall("SELECT begin, end, location, description, cancelled FROM scerun WHERE sce_id = '$scenarie' ORDER BY begin, end, id");
foreach($q AS $rs) {
	$runlist .= "<span" . ($rs['cancelled'] ? " class=\"cancelled\"" : "") . ">";
	$runlist .= nicedateset($rs['begin'],$rs['end']);
	if ( $rs['location'] || $rs['description'] ) {
		if ( nicedateset($rs['begin'],$rs['end']) ) {
			$runlist .= ", ";
		}
		$runlist .= htmlspecialchars($rs['location']);
	}
	if ( $rs['location'] && $rs['description'] ) {
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
$awarddata = [];
$q = getall("SELECT a.id, a.nominationtext, a.winner, a.ranking, b.name, c.id AS convent_id, c.name AS convent_name, c.year, c.conset_id FROM award_nominees a INNER JOIN award_categories b ON a.award_category_id = b.id INNER JOIN convent c ON b.convent_id = c.id WHERE a.sce_id = $scenarie ORDER BY c.year ASC, c.begin ASC, c.id ASC, a.winner DESC, a.id ASC");
foreach($q AS $rs) {
	$awardtext = ($rs['winner'] ? ucfirst($t->getTemplateVars('_award_winner') ) : ucfirst($t->getTemplateVars('_award_nominated') ) ) . ", " . htmlspecialchars($rs['name']);
	if ($rs['ranking']) {
		$awardtext .= " (" . htmlspecialchars($rs['ranking']) . ")";
	}
	if ($rs['nominationtext']) {
		$nt_id = "nominee_text_" . $rs['id'];
		$awardtext .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" style=\"font-weight: bold;\" title=\"" . htmlspecialchars($t->getTemplateVars('_award_show_nominationtext') ) . "\">[+]</span>";
		$awardtext .= "<div class=\"nomtext\" style=\"display: none;\" id=\"$nt_id\">" . nl2br(htmlspecialchars(trim($rs['nominationtext'])), FALSE) . "</div>" . PHP_EOL;

	}

	$awarddata[$rs['convent_id']]['name'] = $rs['convent_name'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "");
	$awarddata[$rs['convent_id']]['conset_id'] = $rs['conset_id'];
	$awarddata[$rs['convent_id']]['text'][] = $awardtext;
}
$awards = [];

foreach($awarddata AS $convent_id => $data) {
	$con_award_url = "awards?cid=" . $data['conset_id'] . "#con" . $convent_id;
	$awards[] = [ 'con_award_url' => $con_award_url, 'con_name' => $data['name'], 'awards' => implode("<br>" . PHP_EOL, $data['text']) ];
}

// Genre
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
if ($_SESSION['can_edit_participant'][$scenarie] ?? FALSE) {
	foreach($taglist AS $tag_id => $tag) {
		$_SESSION['can_edit_tag'][$tag_id] = TRUE;
	}
}
$alltags = getalltags();
$json_alltags = json_encode($alltags);

// Possible picture?
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

// Smarty
$t->assign('pagetitle', $showtitle);
$t->assign('type', $this_type);
$t->assign('type2', 'game');

$t->assign('id', $scenarie);
$t->assign('title', $showtitle);
$t->assign('pic', $available_pic);
$t->assign('ogimage', getimageifexists($scenarie, 'scenarie') );
$t->assign('sysstring', $sysstring);
$t->assign('alias', $aliaslist);
$t->assign('filelist', $filelist);
$t->assign('filedir', 'scenario');

$t->assign('forflist',$forflist);
$t->assign('aut_extra',$r['aut_extra']);
$t->assign('descriptions',$descriptions);
$t->assign('intern',$intern);
$t->assign('gms',$gms);
$t->assign('players',$players);
$t->assign('participants',$participants);
$t->assign('boardgame',$r['boardgame']);
$t->assign('user_can_edit_participants',$_SESSION['can_edit_participant'][$scenarie] ?? FALSE);
$t->assign('conlist',$conlist);
$t->assign('runlist',$runlist);
$t->assign('awards',$awards);
$t->assign('genre',$genre);
$t->assign('trivia',$trivialist);
$t->assign('link',$linklist);
$t->assign('tags',$taglist);
$t->assign('json_alltags',$json_alltags);
$t->assign('user_can_edit_tag',$_SESSION['can_edit_tag'] ?? FALSE );

$t->assign('user_read',in_array('read',$userlog));
$t->assign('user_read_html',getdynamicscehtml($scenarie,'read',in_array('read',$userlog)));
$t->assign('user_gmed',in_array('gmed',$userlog));
$t->assign('user_gmed_html',getdynamicscehtml($scenarie,'gmed',in_array('gmed',$userlog)));
$t->assign('user_played',in_array('played',$userlog));
$t->assign('user_played_html',getdynamicscehtml($scenarie,'played',in_array('played',$userlog)));
$t->assign('users_entries', $users_entries ?? FALSE);

$t->display('data.tpl');
?>
