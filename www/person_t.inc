<?php
$this_type = 'aut';

$r = getrow("SELECT id, CONCAT(firstname,' ',surname) AS name, birth, WEEKDAY(birth) AS birthday, death, WEEKDAY(death) AS deathday FROM aut WHERE id = '$person'");
if ($r['id'] == 0) {
	$t->assign('content',"Beklager - ingen forfatter fundet");
	$t->assign('pagetitle',"Ikke fundet");
	$t->display('default.tpl');

} else {
// Achievements
/*
	print "<!-- " . gettype($r['id']) . " -->\n";
	print "<!-- " . gettype($_SESSION['user_author_id']) . " -->\n";
	print "<!-- " .  (int) ($r['id'] == $_SESSION['user_author_id']) . " -->\n";
*/
	if (isset($_SESSION['user_author_id']) && $r['id'] == $_SESSION['user_author_id']) award_achievement(21); // view own page

// Liste over aliaser
	$aliaslist = getaliaslist($person,$this_type);

// Scenarier af forfatteren
	if ($_SESSION['user_id']) {

		$q = getall("
			SELECT
				MIN(convent.year) AS firstyear,
				MIN(convent.begin) AS firstbegin,
				sce.id,
				sce.title AS title,
				sce.boardgame AS boardgame,
				title.title AS auttitle,
				title.title_label AS auttitlelabel,
				title.iconfile,
				title.iconwidth,
				title.iconheight,
				title.textsymbol,
				SUM(type = 'read') AS `read`,
				SUM(type = 'gmed') AS gmed,
				SUM(type = 'played') AS played,
				COUNT(files.id) AS files
			FROM
				asrel,
				title,
				sce
			LEFT JOIN csrel ON
				csrel.sce_id = sce.id
			LEFT JOIN convent ON
				csrel.convent_id = convent.id
			LEFT JOIN files ON
				sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
			LEFT JOIN userlog ON
				sce.id = userlog.data_id AND
				userlog.category = 'sce' AND
				userlog.user_id = '{$_SESSION['user_id']}'
			WHERE
				sce.id = asrel.sce_id AND
				asrel.tit_id = title.id AND
				asrel.aut_id = '$person' 
			GROUP BY
				sce.id, title.id
			ORDER BY
				firstyear,
				firstbegin,
				title.id,
				sce.title
		");
	} else {
		$q = getall("
			SELECT
				MIN(convent.year) AS firstyear,
				MIN(convent.begin) AS firstbegin,
				sce.id,
				sce.title AS title,
				sce.boardgame AS boardgame,
				title.title AS auttitle,
				title.title_label AS auttitlelabel,
				title.iconfile,
				title.iconwidth,
				title.iconheight,
				title.textsymbol,
				COUNT(files.id) AS files
			FROM
				asrel,
				title,
				sce
			LEFT JOIN csrel ON
				csrel.sce_id = sce.id
			LEFT JOIN convent ON
				csrel.convent_id = convent.id
			LEFT JOIN files ON
				sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
			WHERE
				sce.id = asrel.sce_id AND
				asrel.tit_id = title.id AND
				asrel.aut_id = '$person' 
			GROUP BY
				sce.id, title.id
			ORDER BY
				firstyear,
				firstbegin,
				title.id,
				sce.title
		");
		
	}

	$slist = array();
	$sl = 0;

	if (count($q) > 0) {
		$scenlist = "<table class=\"people indata\">\n";
		foreach($q AS $rs) {
			$scenlist .= "<tr>";
			if ($_SESSION['user_id']) {
				if ($rs['boardgame']) {
					$options = getuserlogoptions('boardgame');
				} else {
					$options = getuserlogoptions('scenario');
				}
				foreach($options AS $type) {
					if ($type) {
						$slist[$sl][$type] = getdynamicscehtml($rs['id'],$type,$rs[$type]);
					} else {
						$slist[$sl][] = " ";
					}
				}
			}
			
			$slist[$sl]['files'] = $rs['files'];
			
			$slist[$sl]['textsymbol'] = $rs['textsymbol'];
			$slist[$sl]['iconfile'] = $rs['iconfile'];
//			$slist[$sl]['icontitle'] = $rs['auttitle'];
			$slist[$sl]['icontitle'] = ucfirst($t->getTemplateVars('_' . $rs['auttitlelabel'] ) );
			$slist[$sl]['iconwidth'] = $rs['iconwidth'];
			$slist[$sl]['iconheight'] = $rs['iconheight'];
			
			$scenlist .= "<td><a href=\"data?scenarie={$rs['id']}\" class=\"scenarie\">{$rs['title']}</a></td>";
			$slist[$sl]['link'] = "data?scenarie=".$rs['id'];
			$slist[$sl]['title'] = $rs['title'];
	
			$conlist = [];
			$qq = getall("SELECT convent.id, convent.name, convent.year, convent.begin, convent.end, convent.cancelled FROM convent, csrel WHERE convent.id = csrel.convent_id AND csrel.pre_id = 1 AND csrel.sce_id = '{$rs['id']}' ORDER BY convent.name");
			foreach($qq AS $rrs) {
				$coninfo = nicedateset($rrs['begin'],$rrs['end']);
				$conlist[] = "<a href=\"data?con={$rrs['id']}\" class=\"con" . ($rrs['cancelled'] == 1 ? " cancelled" : "") . "\" title=\"$coninfo\">".htmlspecialchars($rrs['name'])." (" . yearname($rrs['year']) . ")</a>";
			}
			if ($conlist) {
				$scenlist .= "<td style=\"padding-left: 10px;\">".join("<br />",$conlist)."</td>";
				$slist[$sl]['conlist'] = join("<br />",$conlist);
			} else {
				$scenlist .= "<td>&nbsp;</td>";
				$slist[$sl]['conlist'] = "&nbsp;";
			}
	
			$scenlist .= "</tr>\n";
			$sl++;
		}
		$scenlist .= "</table>";
	}

// Liste over awards
	$awarddata = [];
//	$q = getall("SELECT a.id, a.nominationtext, a.winner, a.ranking, b.name, c.id AS convent_id, c.name AS convent_name, c.year, c.conset_id, e.title FROM award_nominees a INNER JOIN award_categories b ON a.award_category_id = b.id INNER JOIN convent c ON b.convent_id = c.id INNER JOIN asrel d ON a.sce_id = d.sce_id AND d.tit_id IN (1,5) AND d.aut_id = $person INNER JOIN sce e ON a.sce_id = e.id ORDER BY c.year ASC, c.begin ASC, c.id ASC, a.winner DESC, a.id ASC");

	// awards, hvis man er forfatter (1), arrangør (4) eller designer (5)
	$q = getall("
		(
		SELECT a.id, a.nominationtext, a.winner, a.ranking, a.name AS nomineename, b.name, c.id AS convent_id, c.name AS convent_name, c.year, c.begin, c.conset_id, e.title 
		FROM award_nominees a
		INNER JOIN award_categories b ON a.award_category_id = b.id
		INNER JOIN convent c ON b.convent_id = c.id
		INNER JOIN asrel d ON a.sce_id = d.sce_id AND d.tit_id IN (1,4,5) AND d.aut_id = $person
		INNER JOIN sce e ON a.sce_id = e.id
		)
		UNION ALL
		(
		SELECT a.id, a.nominationtext, a.winner, a.ranking, a.name AS nomineename, b.name, c.id AS convent_id, c.name AS convent_name, c.year, c.begin, c.conset_id, '' AS title
		FROM award_nominees a
		INNER JOIN award_categories b ON a.award_category_id = b.id
		INNER JOIN convent c ON b.convent_id = c.id
		INNER JOIN award_nominee_entities d ON a.id = d.award_nominee_id AND category = 'aut' AND data_id = $person
		)
		ORDER BY year ASC, begin ASC, convent_id ASC, winner DESC, id ASC");
	foreach($q AS $rs) {
		$awardtext = "";
		if ($rs['title']) {
			$awardtext .= htmlspecialchars($rs['title']) . ": ";
		}
		$awardtext .= ($rs['winner'] ? "Vinder" : "Nomineret" ) . ", " . htmlspecialchars($rs['name']);
		if ($rs['ranking']) {
			$awardtext .= " (" . htmlspecialchars($rs['ranking']) . ")";
		}

		if ($rs['title'] == '' && $rs['nomineename'] && $rs['nomineename'] != $r['name']) { // personal award, group name
			$awardtext .= " (" . htmlspecialchars($rs['nomineename']) . ")";
		}
		
		if ($rs['nominationtext']) {
			$nt_id = "nominee_text_" . $rs['id'];
			$awardtext .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" style=\"font-weight: bold;\" title=\"" . htmlspecialchars($t->getTemplateVars('_award_show_nominationtext') ) . "\">[+]</span>";
			$awardtext .= "<div class=\"nomtext\" style=\"display: none;\" id=\"$nt_id\">" . nl2br(htmlspecialchars(trim($rs['nominationtext'])), FALSE) . "</div>" . PHP_EOL;

		}

		// $awardtext .=  " – " . $rs['conventname'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "") . "<br>" . PHP_EOL;
		$awarddata[$rs['convent_id']]['name'] = $rs['convent_name'] . ($rs['year'] ? " (" . $rs['year'] . ")" : "");
		$awarddata[$rs['convent_id']]['conset_id'] = $rs['conset_id'];
		$awarddata[$rs['convent_id']]['text'][] = $awardtext;
	}
	$awardlist = "";
	foreach($awarddata AS $convent_id => $data) {
		$con_award_url = "/awards?cid=" . $data['conset_id'] . "#con" . $convent_id;
		$awardlist .= "<h4 class=\"awardconventhead\"><a href=\"" . $con_award_url . "\" class=\"con\" title=\"Alle priser for " . htmlspecialchars($data['name']) . "\">" . htmlspecialchars($data['name']) . "</a></h4>" . PHP_EOL;
		$awardlist .= "<div>";
		$awardlist .= implode("<br>" . PHP_EOL, $data['text']);
/*
		foreach($data['text'] AS $text) {
			$awardlist .= $text . "<br>";
		}
*/
		$awardlist .= "</div>" . PHP_EOL;
	}
	

// Liste over arrangører
	$organizerlist = getorganizerlist($person,$this_type);

// Links and trivia
	$linklist = getlinklist($person,$this_type);
	$trivialist = gettrivialist($person,$this_type);

// Birthday
	$birth = "";
	$age_year = "";
	if ($r['birth'] && $r['birth'] != "0000-00-00" && substr($r['birth'], 0, 4) != "0000" ) { // understøtter endnu ikke fødselsdato uden år
		if ($r['death'] && $r['death'] != "0000-00-00") {
			$birth = fulldate($r['birth']);
		} else {
			$birth = fulldate($r['birth']);
			$age_year = birthage($r['birth']);

		}
	}

	$death = "";
	if ($r['death'] && $r['death'] != "0000-00-00")	{
		if ($r['birth'] && $r['birth'] != "0000-00-00")	{
			$death = fulldate($r['death']);
			$age_year = birthage($r['birth'], $r['death']);
		} else {
			$death = fulldate($r['death']);
		}
	}

// Evt. billede
	$available_pic = 0;

	// Create thumbnail
	if (file_exists("gfx/person/l_".$person.".jpg") && !file_exists("gfx/person/s_".$person.".jpg")) {
		image_rescale_save('gfx/person/l_'.$person.'.jpg','gfx/person/s_'.$person.'.jpg',200,200);
#		$dir = dirname($_SERVER["SCRIPT_FILENAME"]).'/gfx/person/';
#		$cmd = "convert 2>&1 -quality 95 -resize 200x200 ".$dir."l_".$person.".jpg ".$dir."s_".$person.".jpg";
#		print `$cmd`;
	}

	if (file_exists("gfx/person/s_".$person.".jpg")) {
		$personpic = "<a href=\"gfx/person/l_".$person.".jpg\"><img src=\"gfx/person/s_".$person.".jpg\" alt=\"Billede af ".htmlspecialchars($r['name'])."\" style=\"float:right; border: 1px solid black; margin: 2px;\" /></a>";
		$available_pic = 1;
	}

// Smarty

	$t->assign('pagetitle',$r['name']);
	$t->assign('type',$this_type);

	$t->assign('id',$person);
	$t->assign('name',$r['name']);
	$t->assign('pic',$available_pic);
	$t->assign('ogimage', getimageifexists($person, 'person') );
	$t->assign('alias',$aliaslist);
	$t->assign('birth',$birth);
	$t->assign('death',$death);
	$t->assign('age',$age_year);
	$t->assign('slist',$slist);
	$t->assign('award',$awardlist);
	$t->assign('organizerlist',$organizerlist);
	$t->assign('trivia',$trivialist);
	$t->assign('link',$linklist);

	$t->display('data.tpl');

}

?>
