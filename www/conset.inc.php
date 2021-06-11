<?php
$this_type = 'conset';

if ($_SESSION['user_id']) {
	$userlog = getuserlogconvents($_SESSION['user_id']);
}
$condata = [];

$r = getrow("SELECT id, name, description, intern FROM conset WHERE id = '$conset'");
if ($r['id'] == 0) {
	$t->assign('content', $t->getTemplateVars('_nomatch') );
	$t->assign('pagetitle', $t->getTemplateVars('_find_nomatch') );
	$t->display('default.tpl');
	exit;
}
$intern = ( ( $_SESSION['user_editor'] ?? FALSE ) ? $r['intern'] : ""); // only set intern if editor
$showtitle = $cname = ($r['id'] == 40 ? $t->getTemplatevars('_cons_other') : $r['name'] );
$q = getall("
	SELECT convent.id, convent.name, convent.begin, convent.end, convent.year, convent.place, convent.cancelled, COALESCE(convent.country, conset.country) AS country
	FROM convent 
	LEFT JOIN conset ON convent.conset_id = conset.id
	WHERE conset_id = '$conset'
	ORDER BY year, begin, name
");

foreach($q AS $rs) {
	$coninfo = nicedateset($rs['begin'],$rs['end']);
	$condata[] = [
		'id' => $rs['id'],
		'dateset' => $coninfo,
		'userdyn' => ( $_SESSION['user_id'] ? getdynamicconventhtml($rs['id'],'visited', in_array($rs['id'], $userlog) ) : '' ),
		'name' => $rs['name'],
		'year' => $rs['year'],
		'begin' => $rs['begin'],
		'end' => $rs['end'],
		'place' => $rs['place'],
		'country' => $rs['country'],
		'cancelled' => $rs['cancelled']
	];
}

// List of aliases, alternative title?
$alttitle = getcol("SELECT label FROM alias WHERE data_id = '$conset' AND category = '$this_type' AND language = '$lang' AND visible = 1");
if ( count( $alttitle ) == 1 ) {
	$showtitle = $alttitle[0];
	$aliaslist = getaliaslist($conset, $this_type, $showtitle);
	if ( $aliaslist ) {
		$aliaslist = htmlspecialchars( $cname ) . ", " . $aliaslist;
	} else {
		$aliaslist = htmlspecialchars( $cname );
	}
} else {
	$aliaslist = getaliaslist($conset, $this_type);
}

// Trivia, links and articles
$trivialist = gettrivialist($conset,$this_type);
$linklist = getlinklist($conset,$this_type);
$articles = getarticles($conset,$this_type);

$filelist = getfilelist($conset,$this_type);

// Thumbnail
$available_pic = hasthumbnailpic($conset, $this_type);

// Smarty
$t->assign('pagetitle',$showtitle);
$t->assign('type',$this_type);

$t->assign('id',$conset);
$t->assign('name',$showtitle);
$t->assign('pic',$available_pic);
$t->assign('description',$r['description']);
$t->assign('intern',$intern);
$t->assign('condata',$condata);
$t->assign('trivia',$trivialist);
$t->assign('link',$linklist);
$t->assign('articles',$articles);
$t->assign('alias',$aliaslist);
$t->assign('filelist',$filelist);
$t->assign('filedir', getcategorydir($this_type) );

$t->display('data.tpl');
?>
