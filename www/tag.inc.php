<?php
$this_type = 'tag';

list ($tag_id, $ttag, $description) = getrow("SELECT id, tag, description FROM tag WHERE tag = '" . dbesc($tag) . "'");

$tag = getone("SELECT tag FROM tags WHERE tag = '" . dbesc($tag) . "'");
if (!$tag && !$tag_id) {
	$t->assign('content', $t->getTemplateVars('_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
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
			if ($_SESSION['user_id']) {
				foreach(array('read','gmed','played') AS $type) {
					$slist[$sl][$type] = getdynamicscehtml($rs['id'],$type,$rs[$type]);
				}
			}
			$sce_id = $rs['id'];
			// query-i-løkke... skal optimeres!
			$slist[$sl]['files'] = $rs['files'];
			$slist[$sl]['link'] = "data?scenarie=".$rs['id'];
			$slist[$sl]['title'] = $rs['title'];

			$forflist = array();
			$qq = getall("SELECT aut.id, CONCAT(firstname,' ',surname) AS name FROM aut, asrel WHERE asrel.sce_id = '$sce_id' AND asrel.aut_id = aut.id AND asrel.tit_id = '1' ORDER BY firstname, surname");
			foreach($qq AS $thisforfatter) {
				list($forfid,$forfname) = $thisforfatter;
				$forflist[] = "<a href=\"data?person={$forfid}\" class=\"person\">$forfname</a>";
			}
			if (!$forflist && $rs['aut_extra']) {
				$forflist[] = $rs['aut_extra'];
			}
			if ($forflist) {
				$slist[$sl]['forflist'] = join("<br />",$forflist);
			} else {
				$slist[$sl]['forflist'] = "&nbsp;";
			}

			if ($rs['con_id']) {
		    $coninfo = nicedateset($rs['begin'],$rs['end']);
				$slist[$sl]['coninfo'] = $coninfo;
				$slist[$sl]['conlink'] = "data?con=".$rs['con_id'];
				$slist[$sl]['conname'] = $rs['name'] . " (" . yearname($rs['year']) . ")";
			} else {
				$slist[$sl]['conname'] = "&nbsp;";
			}

			$sl++;
		}
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
	$t->assign('slist',$slist);
	$t->assign('trivia',$trivialist);
	$t->assign('link',$linklist);

	$t->display('data.tpl');

}
?>
