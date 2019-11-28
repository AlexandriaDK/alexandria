<?php
$this_type = 'convent';

$authors_limit = 4;
$scenlistdata = [];
$boardlistdata = [];

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

$r = getrow("SELECT convent.id, convent.name, convent.year, convent.description, begin, end, place, conset_id, confirmed, conset.name AS cname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id WHERE convent.id = '$con'");
if ($r['id'] == 0) {
	$t->assign('content',"Beklager - ingen con fundet!");
	$t->assign('pagetitle',"Ikke fundet");
	$t->display('default.tpl');
} else {
// Liste over filer
	$filelist = getfilelist($con,$this_type);


// Del af større conset?

//	$serieantal = getone("SELECT COUNT(*) FROM convent WHERE '{$r['conset_id']}' = conset_id");

// I så fald, find næste og forrige
//	if ($r['conset_id'] && $serieantal > 1) {
	if ($r['conset_id']) {
		$delafout = "<a href=\"data?conset={$r['conset_id']}\" class=\"con\">{$r['cname']}</a>";
		$qq = getall("
			SELECT id, name, year, begin, end
			FROM convent 
			WHERE conset_id = '{$r['conset_id']}'
			ORDER BY year, begin, name
		");
		unset($seriedata,$seriecount,$seriethis);
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
			if ($seriedata['id'][($seriethis-1)]) {
//				$coninfo = nicedateset($seriedata['begin'][($seriethis-1)],$seriedata['end'][($seriethis-1)]);
				$arrows['prev'] = [ 'active' => TRUE, 'conid' => $seriedata['id'][($seriethis-1)], 'name' => $seriedata['name'][($seriethis-1)] . " (".$seriedata['year'][($seriethis-1)].")" ];
			} else {
				$arrows['prev'] = [ 'active' => FALSE ];
			}
			if ($seriedata['id'][($seriethis+1)]) {
//				$coninfo = nicedateset($seriedata['begin'][($seriethis+1)],$seriedata['end'][($seriethis+1)]);
				$arrows['next'] = [ 'active' => TRUE, 'conid' => $seriedata['id'][($seriethis+1)], 'name' => $seriedata['name'][($seriethis+1)] . " (".$seriedata['year'][($seriethis+1)].")" ];
			} else {
				$arrows['next'] = [ 'active' => FALSE ];
			}
		}
		
	}

// Lav liste over scenarier

	$sce_new = $sce_rerun = $sce_cancelled = $board_new = $board_rerun = $board_cancelled = 0;
	$scenlist = $boardlist = $datalist = "";

	if ($_SESSION['user_id']) {
		$q = getall("
			SELECT sce.id, sce.title, sce.boardgame, pre.id AS preid, pre.event, pre.event_label, pre.iconfile, pre.textsymbol, sce.sys_ext, sys.id AS sys_id, sys.name AS sys_name, SUM(type = 'read') AS `read`, SUM(type = 'gmed') AS gmed, SUM(type = 'played') AS played, COUNT(files.id) AS files
			FROM csrel, pre, sce
			LEFT JOIN sys ON sce.sys_id = sys.id
			LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
			LEFT JOIN userlog ON sce.id = userlog.data_id AND userlog.category = 'sce' AND userlog.user_id = '{$_SESSION['user_id']}'
			WHERE sce.id = csrel.sce_id AND csrel.pre_id = pre.id AND csrel.convent_id = '$con'
			GROUP BY sce.id, pre.id
			ORDER BY boardgame, title
		");
	} else {
		$q = getall("
			SELECT sce.id, sce.title, sce.boardgame, pre.id AS preid, pre.event, pre.event_label, pre.iconfile, pre.textsymbol, sce.sys_ext, sys.id AS sys_id, sys.name AS sys_name, COUNT(files.id) AS files
			FROM csrel, pre, sce
			LEFT JOIN sys ON sce.sys_id = sys.id
			LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
			WHERE sce.id = csrel.sce_id AND csrel.pre_id = pre.id AND csrel.convent_id = '$con'
			GROUP BY sce.id, pre.id
			ORDER BY boardgame, title
		");
	}
	if (count($q) > 0) {
		foreach($q AS $rs) {
			$datalist = "";
			$datalistdata = [];
			$useroptions = [];
			$datalist .= "<tr>";
			if ($_SESSION['user_id']) {
				if ($rs['boardgame']) {
					$options = getuserlogoptions('boardgame');
				} else {
					$options = getuserlogoptions('scenario');
				}
				foreach($options AS $type) {
					$datalist .= "<td>";
					if ($type != NULL) {
						$datalist .= getdynamicscehtml($rs['id'],$type,$rs[$type]);
						$useroptions[$type] = getdynamicscehtml($rs['id'],$type,$rs[$type]);
					}
					
					$datalist .= "</td>";
				}
				$datalist .= "<td style=\"width: 10px;\">&nbsp;</td>";
			}

// Sæt evt. download-ikon på
			$datalist .= "<td>";
			if ($rs['files'] > 0) {
				$datalist .= "<a href=\"data?scenarie=" . $rs['id'] . "\" title=\"" . htmlspecialchars($t->getTemplateVars('_sce_downloadable')) . "\">💾</a>";
			}
			$datalist .= "</td>";

// Sæt evt. rerun/aflyst-ikoner på
			$datalist .= "<td>";
			if ($rs['textsymbol']) { // unicode-ikoner
				$datalist .= $runsymbol = "<span class=\"preicon\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) . "\">{$rs['textsymbol']}</span>";
			} elseif ($rs['iconfile']) {
				$datalist .= $runsymbol = "<img src=\"/gfx/{$rs['iconfile']}\" alt=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) . "\" title=\"" .  htmlspecialchars(ucfirst($t->getTemplateVars('_' . $rs['event_label'] ) ) ) . "\" width=\"14\" height=\"14\" />";
			} else {
				$datalist .= $runsymbol = "<img src=\"/gfx/spacer.gif\" width=\"14\" height=\"1\" alt=\"\" />";
			}
			$datalist .= "</td>";

			$datalist .= "<td>";
			$sce_id = $rs['id'];
			$datalist .= "<a href=\"data?scenarie={$rs['id']}\" class=\"scenarie\">".htmlspecialchars($rs['title'])."</a>";
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
			$datalist .= "</td>";
			$forflist = [];
			$forflistextra = [];
			$qq = getall("
				SELECT DISTINCT aut.id, CONCAT(firstname,' ',surname) AS name, surname, firstname
				FROM aut, asrel
				WHERE asrel.sce_id = '$sce_id' AND asrel.aut_id = aut.id AND asrel.tit_id IN(1,4,5)
				ORDER BY surname, firstname
			");
			$author_count = 0;
			foreach($qq AS $thisforfatter) {
				list($forfid,$forfname) = $thisforfatter;
				$author_count++;
				$forfhtml = "<a href=\"data?person={$forfid}\" class=\"person\">$forfname</a>";
				if ($author_count < $authors_limit || count($qq) == $authors_limit) {
					$forflist[] = $forfhtml;
				} else {
					$forflistextra[] = $forfhtml;
				}
			}
			$authtml = "";
			if ($forflist) {
				$authtml = join("<br>", $forflist);
				$datalist .= "<td style=\"padding-left: 10px;\">";
				$datalist .= $authtml;
				$autextrahtml =  join("<br>", $forflistextra);
				if ($forflistextra) {
					$datalist .= "<br><span onclick=\"this.nextSibling.style.display='inline';this.style.display='none';\" class=\"moreauthors\" title=\"" . count($forflistextra) . " yderligere forfatter" . (count($forflistextra) == 1 ? "" : "e") . "\">[…]</span><span class=\"authorlistextra\">" . $autextrahtml;
				}
				$datalist .= "</td>";
			} else {
				$datalist .= "<td>&nbsp;</td>";
			}
			

// System
			$sysstring = "";
			if ($rs['sys_name']) {
				$syspart = "<a href=\"data?system={$rs['sys_id']}\" class=\"system\">".htmlspecialchars($rs['sys_name'])."</a>";
				if ($rs['sys_ext']) $syspart .= " ".htmlspecialchars($rs['sys_ext']);
				$sysstring = $syspart;
			} elseif ($rs['sys_ext']) {
				$sysstring = htmlspecialchars($rs['sys_ext']);
			}
			
			if (isset($sysstring) && $sysstring) {
				$datalist .= "<td style=\"padding-left: 10px;\">$sysstring</td>";
			} else {
				$datalist .= "<td>&nbsp;</td>";
			}

			$datalist .= "</tr>\n";

			$datalistdata = [
				'id' => $rs['id'],
				'userdyn' => $useroptions,
				'filescount' => $rs['files'],
				'runsymbol' => $runsymbol,
				'title' => $rs['title'],
				'authtml' => $authtml,
				'autextracount' => count($forflistextra),
				'autextrahtml' => $autextrahtml,
				'systemhtml' => $sysstring,
				'boardgame' => $rs['boardgame'],
			];
				

			if ($rs['boardgame']) {
				$boardlist .= $datalist;
				$boardlistdata[] = $datalistdata;
			} else {
				$scenlist .= $datalist;
				$scenlistdata[] = $datalistdata;
			}
		}
// Tilføj antal scenarier, nye som reruns:
		$total = $sce_new + $sce_rerun + $sce_cancelled;
		$board_total = $board_new + $board_rerun + $board_cancelled;

		$scen_antaltxt = antaltxt($sce_new, $sce_rerun, $sce_cancelled, $total, 'sce');
		$board_antaltxt = antaltxt($board_new, $board_rerun, $board_cancelled, $board_total, 'board');

		$conflist = array(
			0 => "scenarieliste mangler",
			1 => "scenarieliste under indtastning",
			2 => "scenarieliste komplet jf. program",
			9 => "scenarieliste bekræftet"
		);
	
		if ($scenlist) {
			$scenlist = "<tr><td colspan=\"8\">$scen_antaltxt</td></tr>\n" . $scenlist;
		}
		if ($boardlist) {
			$boardlist = "<tr><td colspan=\"8\">$board_antaltxt</td></tr>\n" . $boardlist;
		}

	}
// Liste over priser

	$award_nominees = getall("SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.ranking, a.sce_id, b.id AS category_id, b.convent_id, b.name AS category_name, c.year, c.name AS con_name, c.conset_id, d.title FROM award_nominees a INNER JOIN award_categories b ON a.award_category_id = b.id LEFT JOIN convent c ON b.convent_id = c.id LEFT JOIN sce d ON a.sce_id = d.id WHERE c.id = $con ORDER BY c.year DESC, a.winner DESC, a.id");

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
		$awardnominees[$cid][$con_id]['categories'][$cat_id]['nominees'][] = ['id' => $nominee['id'], 'name' => $nominee['name'], 'nominationtext' => $nominee['nominationtext'], 'winner' => $nominee['winner'], 'ranking' => $nominee['ranking'], 'sce_id' => $nominee['sce_id'], 'title' => $nominee['title'] ];
	}

	foreach ((array) $awardnominees[$cid] AS $conid => $convent) {
		$html .= "<div class=\"awardyear\" data-year=\"" . $convent['year'] . "\">";
		$html .= "<div class=\"awardblock\">" . PHP_EOL;
		foreach($convent['categories'] AS $category) {
			$html .= PHP_EOL . "<div class=\"awardcategory\" data-category=\"" . htmlspecialchars($category['name']) . "\">" . PHP_EOL;
			$html .= "<h4>" . htmlspecialchars($category['name']) . "</h4>" . PHP_EOL;
			foreach($category['nominees'] AS $nominee) {
				$class = ($nominee['winner'] == 1 ? "winner" : "nominee");
				$html .= "<div class=\"" . $class . "\">";
				$html .= "<h5 class=\"" . $class . "\">";
				$html .= "<span class=\"" . $class . "\">";
				if ($nominee['sce_id']) {
					$html .= getdataurl('sce', $nominee['sce_id'], $nominee['title']);
				} else {
					$html .= htmlspecialchars($nominee['name']);
				}
				$html .= "</span>";
				if ($nominee['nominationtext']) {
					$nt_id = "nominee_text_" . $nominee['id'];
					$html .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"Vis nomineringstekst\">[+]</span>";

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
//		print "<pre>"; var_dump($nominees); exit;
		
		
	}
	$awardlist = $html;
	


// Liste over arrangører
	if ($_REQUEST['oo'] == 'id') { // oo = organizer order
		$organizerlist = getorganizerlist($con,$this_type, 'a.id');
	} else {
		$organizerlist = getorganizerlist($con,$this_type);
	}

// Liste over aliaser
	$aliaslist = getaliaslist($con,$this_type);
// Trivia:
	$trivialist = gettrivialist($con,$this_type);
// Links:
	$linklist = getlinklist($con,$this_type);

// Evt. forside-billede
	$available_pic = 0;
	// Create thumbnail
	if (file_exists("gfx/convent/l_".$con.".jpg") && !file_exists("gfx/convent/s_".$con.".jpg")) {
		image_rescale_save('gfx/convent/l_'.$con.'.jpg','gfx/convent/s_'.$con.'.jpg',200,200);
#		$dir = dirname($_SERVER["SCRIPT_FILENAME"]).'/gfx/convent/';
#		$cmd = "convert 2>&1 -quality 95 -resize 200x200 ".$dir."l_".$con.".jpg ".$dir."s_".$con.".jpg";
#		print `$cmd`;
	}

	if (file_exists("gfx/convent/s_".$con.".jpg")) {
		$available_pic = 1;
	}

// Userdata
	$userlog = array();
	if ($_SESSION['user_id']) {
		$userlog = getuserlog($_SESSION['user_id'],$this_type,$r['id']);
		$users_entries = getalluserentries('convent', $r['id']);
	}

//  Edit mode?
$editorganizers = ($_REQUEST['edit'] == 'organizers');
$editmode = ( $_SESSION['user_id'] && $editorganizers );
$people = [];
if ($editmode) {
	$people = getcol("SELECT CONCAT(id, ' - ', firstname, ' ', surname) AS id_name FROM aut ORDER BY firstname, surname");	
}
$json_people = json_encode($people);


// Smarty

	$t->assign('pagetitle',$r['name']." (" . ( $r['year'] ? $r['year'] : "?" ) . ")");
	$t->assign('type',$this_type);

	$t->assign('id',$con);
	$t->assign('name',$r['name']);
	$t->assign('year',($r['year'] ? $r['year'] : "?") );
	$t->assign('arrowset',$arrows);
	$t->assign('pic',$available_pic);
	$t->assign('ogimage', getimageifexists($con, 'convent') );
	$t->assign('place',$r['place']);
	$t->assign('dateset',nicedateset($r['begin'],$r['end']));
	$t->assign('partof',$delafout);
	$t->assign('confirmed',$r['confirmed']);
	$t->assign('description',$r['description']);
	$t->assign('scenlist',$scenlist);
	$t->assign('boardlist',$boardlist);
	$t->assign('scenlistdata',$scenlistdata);
	$t->assign('boardlistdata',$boardlistdata);
	$t->assign('organizerlist',$organizerlist);
	$t->assign('award',$awardlist);
	$t->assign('trivia',$trivialist);
	$t->assign('link',$linklist);
	$t->assign('alias',$aliaslist);
	$t->assign('filelist',$filelist);
	$t->assign('filedir','convent');

	$t->assign('editorganizers', $editorganizers);
	$t->assign('editmode', $editmode );
	$t->assign('json_people', $json_people );
	$t->assign('user_can_edit_organizers',$_SESSION['can_edit_organizers'] );

	$t->assign('user_visited',in_array('visited',$userlog));
	$t->assign('users_entries', $users_entries);

	$t->display('data.tpl');

}
?>
