<?php
require("./connect.php");
require("base.inc.php");

$result = getall("
	SELECT gamesystem.id, name, COALESCE(alias.label, gamesystem.name) AS translation_name
	FROM gamesystem
	LEFT JOIN alias ON gamesystem.id = alias.gamesystem_id AND alias.language = '" . LANG . "' AND alias.visible = 1
	GROUP BY gamesystem.id
	ORDER BY translation_name, gamesystem.id
");
$syslist = [];
foreach($result AS $r) {
	$syslist[$r['id']] = $r['translation_name'];
}

// Smarty
$t->assign('syslist',$syslist);
$t->display('gamesystems.tpl');
