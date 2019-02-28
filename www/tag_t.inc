<?php
$this_type = 'tag';

list ($tag_id, $ttag, $description) = getrow("SELECT id, tag, description FROM tag WHERE tag = '" . dbesc($tag) . "'");

$tag = getone("SELECT tag FROM tags WHERE tag = '" . dbesc($tag) . "'");
if (!$tag && !$tag_id) {
	$t->assign('content',"Beklager - dette tag findes ikke!");
	$t->assign('pagetitle',"Ikke fundet");
	$t->display('default.tpl');
} else {
	if (!$tag) {
		$tag = $ttag;
	}
	if ($_SESSION['user_id']) {
		$q = getall("SELECT sce.id, title, name, convent.id AS con_id, convent.year, convent.begin, convent.end, aut_extra, SUM(type = 'read') AS `read`, SUM(type = 'gmed') AS gmed, SUM(type = 'played') AS played, COUNT(files.id) AS files FROM sce INNER JOIN tags ON sce.id = tags.sce_id LEFT JOIN csrel ON csrel.sce_id = sce.id AND csrel.pre_id = 1 LEFT JOIN convent ON csrel.convent_id = convent.id LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1 LEFT JOIN userlog ON sce.id = userlog.data_id AND userlog.category = 'sce' AND userlog.user_id = '{$_SESSION['user_id']}' WHERE tags.tag = '" . dbesc($tag). "'  GROUP BY sce.id, convent.id ORDER BY title");
	} else {
		$q = getall("SELECT sce.id, title, convent.name, convent.id AS con_id, convent.year, convent.begin, convent.end, aut_extra, COUNT(files.id) AS files FROM sce INNER JOIN tags ON sce.id = tags.sce_id LEFT JOIN csrel ON csrel.sce_id = sce.id AND csrel.pre_id = 1 LEFT JOIN convent ON csrel.convent_id = convent.id LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1 WHERE tags.tag = '" . dbesc($tag). "' GROUP BY sce.id, convent.id ORDER BY title");
	}

	print dberror();

	$slist = array();
	$sl = 0;

	if (count($q) > 0) {
		foreach($q AS $rs) {
			$scenlist .= "<tr valign=\"top\">";
			if ($_SESSION['user_id']) {
				foreach(array('read','gmed','played') AS $type) {
					$scenlist .= "<td>".getdynamicscehtml($rs['id'],$type,$rs[$type])."</td>";
					$slist[$sl][$type] = getdynamicscehtml($rs['id'],$type,$rs[$type]);
				}
			}
			$scenlist .= "<td>";
			$sce_id = $rs['id'];
			// query-i-løkke... skal optimeres!
			$slist[$sl]['files'] = $rs['files'];
			$slist[$sl]['link'] = $_SERVER['PHP_SELF']."?scenarie=".$rs['id'];
			$slist[$sl]['title'] = $rs['title'];
			$scenlist .= "<a href=\"{$_SERVER['PHP_SELF']}?scenarie={$rs['id']}\" class=\"scenarie\">".htmlspecialchars($rs['title'])."</a>";
			$scenlist .= "</td>";

			$forflist = array();
			$qq = getall("SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut, asrel WHERE asrel.sce_id = '$sce_id' AND asrel.aut_id = aut.id AND asrel.tit_id = '1' ORDER BY firstname, surname");
			foreach($qq AS $thisforfatter) {
				list($forfid,$forfname) = $thisforfatter;
				$forflist[] = "<a href=\"{$_SERVER['PHP_SELF']}?person={$forfid}\" class=\"person\">$forfname</a>";
			}
			if (!$forflist && $rs['aut_extra']) {
				$forflist[] = $rs['aut_extra'];
			}
			if ($forflist) {
				$scenlist .= "<td class=\"lpad\">".join("<br />",$forflist)."</td>";
				$slist[$sl]['forflist'] = join("<br />",$forflist);
			} else {
				$scenlist .= "<td>&nbsp;</td>";
				$slist[$sl]['forflist'] = "&nbsp;";
			}

			if ($rs['con_id']) {
		    $coninfo = nicedateset($rs['begin'],$rs['end']);
				$scenlist .= "<td class=\"lpad\"><a href=\"{$_SERVER['PHP_SELF']}?con={$rs['con_id']}\" class=\"con\" title=\"$coninfo\">{$rs['name']} ({$rs['year']})</a></td>";
				$slist[$sl]['coninfo'] = $coninfo;
				$slist[$sl]['conlink'] = $_SERVER['PHP_SELF']."?con=".$rs['con_id'];
				$slist[$sl]['conname'] = "{$rs['name']} ({$rs['year']})";
			} else {
				$scenlist .= "<td>&nbsp;</td>";
				$slist[$sl]['conname'] = "&nbsp;";
			}

			$scenlist .= "</tr>\n";
			$sl++;
		}
		$scenlist = "<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"indata\">\n".$scenlist."</table>";
	}

// Links and trivia
	$linklist = getlinklist($tag_id,$this_type);
	$trivialist = gettrivialist($tag_id,$this_type);

// Smarty

	$t->assign('pagetitle',$tag);
	$t->assign('type',$this_type);

	$t->assign('id',$tag_id);
	$t->assign('tag',$tag);
	$t->assign('description',$description);
	$t->assign('alias',$aliaslist);
	$t->assign('scenlist',$scenlist);
	$t->assign('slist',$slist);
	$t->assign('trivia',$trivialist);
	$t->assign('link',$linklist);

	$t->display('data.tpl');

}
?>
