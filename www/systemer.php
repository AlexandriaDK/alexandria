<?php
require("./connect.php");
require("base.inc.php");

$result = getall("
	SELECT sys.id, name, COALESCE(alias.label, sys.name) AS translation_name
	FROM sys
	LEFT JOIN alias ON sys.id = alias.data_id AND alias.category = 'sys' AND alias.language = '" . LANG . "' AND alias.visible = 1
	GROUP BY sys.id
	ORDER BY translation_name, sys.id
");
$syslist = [];
foreach($result AS $r) {
	$syslist[$r['id']] = $r['translation_name'];
}

// Smarty
$t->assign('list',$list);
$t->assign('syslist',$syslist);
$t->display('systems.tpl');
?>
