<?php
$this_type = 'sys';
$this_type_new = 'system';
$this_id = $system;

if ($_SESSION['user_id']) {
	$userlog = getuserloggames($_SESSION['user_id']);
}

// achievements
if ($system == 33) award_achievement(53); // Vampire
if ($system == 23) award_achievement(54); // MERP
if ($system == 3)  award_achievement(68); // Paranoia

$r = getrow("SELECT id, name, description FROM sys WHERE id = '$system'");
$showname = $sysname = $r['name'];

if ($r['id'] == 0) {
	$t->assign('content', $t->getTemplateVars('_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
	$t->display('default.tpl');
	exit;
}
$q = getall("
	SELECT sce.id, sce.title, convent.name, convent.id AS con_id, convent.year, convent.begin, convent.end, convent.cancelled, convent.country, aut_extra, COUNT(files.id) AS files, aut.id AS aut_id, CONCAT(aut.firstname,' ',aut.surname) AS autname, pre.id AS pre_id, pre.event_label, pre.iconfile, pre.textsymbol, COALESCE(alias.label, sce.title) AS title_translation
	FROM sce
	LEFT JOIN asrel ON asrel.sce_id = sce.id AND asrel.tit_id IN (1,5)
	LEFT JOIN aut ON asrel.aut_id = aut.id
	LEFT JOIN csrel ON csrel.sce_id = sce.id
	LEFT JOIN convent ON csrel.convent_id = convent.id
	LEFT JOIN pre ON csrel.pre_id = pre.id
	LEFT JOIN files ON sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
	LEFT JOIN alias ON sce.id = alias.data_id AND alias.category = 'sce' AND alias.language = '" . LANG . "' AND alias.visible = 1
	WHERE sys_id = '$system'
	GROUP BY sce.id, convent.id, aut.id
	ORDER BY title_translation, convent.year, convent.begin, convent.end, aut.surname, aut.firstname
");

$gamelist = [];

if (count($q) > 0) {
	foreach($q AS $rs) { // Put all together
		$sce_id = $rs['id'];
		if ( ! isset($gamelist[$rs['id']]) ) {
			$gamelist[$rs['id']] = ['game' => ['title' => $rs['title_translation'], 'origtitle' => $rs['title'], 'person_extra' => $rs['aut_extra'], 'files' => $rs['files'] ], 'person' => [], 'convent' => [] ];
		}
		if ($rs['aut_id']) {
			$gamelist[$rs['id']]['person'][$rs['aut_id']] = $rs['autname'];
		}
		if ($rs['con_id']) {
			$gamelist[$rs['id']]['convent'][$rs['con_id']] = ['id' => $rs['con_id'], 'name' => $rs['name'], 'year' => $rs['year'], 'begin' => $rs['begin'], 'end' => $rs['end'], 'cancelled' => $rs['cancelled'], 'country' => $rs['country'], 'iconfile' => $rs['iconfile'], 'textsymbol' => $rs['textsymbol'], 'event_label' => $rs['event_label'], 'pre_id' => $rs['pre_id'] ];
		}
	}

	if ($_SESSION['user_id']) {
		foreach ($gamelist AS $id => $game) {
			foreach( ['read','gmed','played'] AS $type) {
				$gamelist[$id]['userdata']['html'][$type] = getdynamicscehtml($id,$type,$userlog[$id][$type] ?? FALSE );
			}
		}
	}
}

// List of aliases, alternative title?
$alttitle = getcol("SELECT label FROM alias WHERE data_id = $system AND category = '$this_type' AND language = '$lang' AND visible = 1");
if ( count( $alttitle ) == 1 ) {
	$showname = $alttitle[0];
	$aliaslist = getaliaslist($system, $this_type, $showname);
	if ( $aliaslist ) {
		$aliaslist = "<b title=\"" . $t->getTemplateVars( "_sce_original_title" ) . "\">" . htmlspecialchars( $sysname ) . "</b>, " . $aliaslist;
	} else {
		$aliaslist = "<b title=\"" . $t->getTemplateVars( "_sce_original_title" ) . "\">" . htmlspecialchars( $sysname ) . "</b>";
	}
} else {
	$aliaslist = getaliaslist($system, $this_type);
}

// List of files
$filelist = getfilelist($system,$this_type);

// Trivia, links and articles
$trivialist = gettrivialist($system,$this_type);
$linklist = getlinklist($system,$this_type);
$articles = getarticles($system,$this_type_new);

// Thumbnail
$available_pic = hasthumbnailpic($system, $this_type);

// Smarty
$t->assign('pagetitle', $showname);
$t->assign('type',$this_type);

$t->assign('id',$system);
$t->assign('name',$showname);
$t->assign('pic',$available_pic);
$t->assign('ogimage', getimageifexists($this_id, $this_type_new) );
$t->assign('alias',$aliaslist);
$t->assign('description',$r['description']);
$t->assign('gamelist',$gamelist);
$t->assign('trivia',$trivialist);
$t->assign('link',$linklist);
$t->assign('articles',$articles);
$t->assign('filelist',$filelist);
$t->assign('filedir', getcategorydir($this_type) );

$t->display('data.tpl');
?>
