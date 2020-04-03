<?php
$this_type = 'sys';

// achievements
if ($system == 33) award_achievement(53); // Vampire
if ($system == 23) award_achievement(54); // MERP
if ($system == 3)  award_achievement(68); // Paranoia

$r = getrow("SELECT id, name, description FROM sys WHERE id = '$system'");

if ($r['id'] == 0) {
	$t->assign('content',"Beklager - intet system fundet!");
	$t->assign('pagetitle',"Ikke fundet");
	$t->display('default.tpl');
} else {
	if ($_SESSION['user_id']) {
		$q = getall("SELECT sce.id, title, name, convent.id AS con_id, convent.year, convent.begin, convent.end, aut_extra, SUM(type = 'read') AS `read`, SUM(type = 'gmed') AS gmed, SUM(type = 'played') AS played, COUNT(files.id) AS files FROM sce LEFT JOIN csrel ON csrel.sce_id = sce.id AND csrel.pre_id = 1 LEFT JOIN convent ON csrel.convent_id = convent.id LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1 LEFT JOIN userlog ON sce.id = userlog.data_id AND userlog.category = 'sce' AND userlog.user_id = '{$_SESSION['user_id']}' WHERE sys_id = '$system' GROUP BY sce.id, convent.id ORDER BY title");
	} else {
		$q = getall("SELECT sce.id, title, convent.name, convent.id AS con_id, convent.year, convent.begin, convent.end, aut_extra, COUNT(files.id) AS files FROM sce LEFT JOIN csrel ON csrel.sce_id = sce.id AND csrel.pre_id = 1 LEFT JOIN convent ON csrel.convent_id = convent.id LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1 WHERE sys_id = '$system' GROUP BY sce.id, convent.id ORDER BY title");
	}

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

// List of aliases
	$aliaslist = getaliaslist($system,$this_type);

// Links and trivia
	$linklist = getlinklist($system,$this_type);
	$trivialist = gettrivialist($system,$this_type);

// Evt. forside-billede
	$available_pic = 0;
	// Create thumbnail
	if (file_exists("gfx/system/l_".$system.".jpg") && !file_exists("gfx/system/s_".$system.".jpg")) {
		image_rescale_save('gfx/system/l_'.$system.'.jpg','gfx/system/s_'.$system.'.jpg',200,200);
#		$dir = dirname($_SERVER["SCRIPT_FILENAME"]).'/gfx/system/';
#		$cmd = "convert 2>&1 -quality 95 -resize 200x200 ".$dir."l_".$system.".jpg ".$dir."s_".$system.".jpg";
#		print `$cmd`;
	}

	if (file_exists("gfx/system/s_".$system.".jpg")) {
		$available_pic = 1;
	}

// Smarty

	$t->assign('pagetitle',$r['name']);
	$t->assign('type',$this_type);

	$t->assign('id',$system);
	$t->assign('name',$r['name']);
	$t->assign('pic',$available_pic);
	$t->assign('alias',$aliaslist);
	$t->assign('description',$r['description']);
	$t->assign('slist',$slist);
	$t->assign('trivia',$trivialist);
	$t->assign('link',$linklist);

	$t->display('data.tpl');

}
?>
