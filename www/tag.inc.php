<?php
$this_type = 'tag';

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

list($tag_id, $ttag, $description) = getrow("SELECT id, tag, description FROM tag WHERE tag = '" . dbesc($tag) . "'");

$tag = getone("SELECT tag FROM tags WHERE tag = '" . dbesc($tag) . "'");
if (!$tag && !$tag_id) {
	$t->assign('content', $t->getTemplateVars('_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
	$t->display('default.tpl');
	exit;
}
if (!$tag) {
	$tag = $ttag;
}
$q = getall("
	SELECT sce.id, title, convent.name, convent.id AS con_id, convent.year, convent.begin, convent.end, aut_extra, COUNT(files.id) AS files, COALESCE(alias.label, sce.title) AS title_translation
	FROM sce
	INNER JOIN tags ON sce.id = tags.sce_id
	LEFT JOIN csrel ON csrel.sce_id = sce.id AND csrel.pre_id = 1
	LEFT JOIN convent ON csrel.convent_id = convent.id
	LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
	LEFT JOIN alias ON sce.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE tags.tag = '" . dbesc($tag). "'
	GROUP BY sce.id, convent.id
	ORDER BY title_translation
");

$slist = [];
$sl = 0;

if (count($q) > 0) {
	foreach($q AS $rs) {
		if ($_SESSION['user_id']) {
			foreach(array('read','gmed','played') AS $type) {
				$slist[$sl][$type] = getdynamicscehtml($rs['id'],$type,$userlog[$rs['id']][$type] ?? FALSE);
			}
		}
		$sce_id = (int) $rs['id'];
		// query-i-løkke... skal optimeres!
		$slist[$sl]['files'] = $rs['files'];
		$slist[$sl]['link'] = "data?scenarie=".$rs['id'];
		$slist[$sl]['title'] = $rs['title_translation'];
		$slist[$sl]['origtitle'] = $rs['title'];
		$slist[$sl]['personlist'] = "";

		$personlist = [];
		$qq = getall("
			SELECT aut.id, CONCAT(firstname,' ',surname) AS name
			FROM aut, asrel
			WHERE asrel.sce_id = $sce_id AND asrel.aut_id = aut.id AND asrel.tit_id IN(1,5)
			ORDER BY firstname, surname
		");
		foreach($qq AS $thisforfatter) {
			list($forfid,$forfname) = $thisforfatter;
			$personlist[] = "<a href=\"data?person={$forfid}\" class=\"person\">$forfname</a>";
		}
		if (!$personlist && $rs['aut_extra']) {
			$personlist[] = $rs['aut_extra'];
		}
		if ($personlist) {
			$slist[$sl]['personlist'] = join("<br />",$personlist);
		}

		if ($rs['con_id']) {
		$coninfo = nicedateset($rs['begin'],$rs['end']);
			$slist[$sl]['coninfo'] = $coninfo;
			$slist[$sl]['conlink'] = "data?con=".$rs['con_id'];
			$slist[$sl]['conname'] = $rs['name'] . " (" . yearname($rs['year']) . ")";
		}

		$sl++;
	}
}

// List of files
$filelist = getfilelist($tag_id,$this_type);

// Links and trivia
$linklist = getlinklist($tag_id,$this_type);
$trivialist = gettrivialist($tag_id,$this_type);

// Thumbnail
$available_pic = hasthumbnailpic($tag_id, $this_type);

// Smarty
$t->assign('pagetitle',$tag);
$t->assign('type',$this_type);

$t->assign('id',$tag_id);
$t->assign('tag',$tag);
$t->assign('pic',$available_pic);
$t->assign('ogimage', getimageifexists($tag_id, 'tag') );
$t->assign('description',$description);
$t->assign('slist',$slist);
$t->assign('trivia',$trivialist);
$t->assign('link',$linklist);
$t->assign('filelist',$filelist);
$t->assign('filedir', getcategorydir($this_type) );

$t->display('data.tpl');
?>
