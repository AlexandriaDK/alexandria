<?php
$this_type = 'conset';

$condata = [];

$r = getrow("SELECT id, name, description FROM conset WHERE id = '$conset'");
if ($r['id'] == 0) {
	$t->assign('content',"Beklager - ingen con-serie fundet!");
	$t->assign('pagetitle',"Ikke fundet");
	$t->display('default.tpl');
} else {
	if ($_SESSION['user_id']) {
	
		$q = getall("
			SELECT convent.id, convent.name, convent.begin, convent.end, convent.year, convent.place, userlog.type IS NOT NULL AS visited
			FROM convent 
			LEFT JOIN userlog ON
				convent.id = userlog.data_id AND
				userlog.category = 'convent' AND
				userlog.user_id = '{$_SESSION['user_id']}'
			WHERE conset_id = '$conset'
			ORDER BY year, begin, name
		");
	} else {
		$q = getall("
			SELECT id, name, begin, end, year, place
			FROM convent 
			WHERE conset_id = '$conset'
			ORDER BY year, begin, name
		");
	}
	$conlist .= "<table class=\"conlist\">";
	foreach($q AS $rs) {
		$coninfo = nicedateset($rs['begin'],$rs['end']);
		$conlist .= "<tr>";
		if ($_SESSION['user_id']) {
			$conlist .= "<td>".getdynamicconventhtml($rs['id'],'visited',$rs['visited'])."</td>";
		}

		$conlist .= "<td><a href=\"data?con={$rs['id']}\" class=\"con\" title=\"$coninfo\">{$rs['name']} (" . ($rs['year'] ? $rs['year'] : "?") . ")</a></td>";
		if ($rs['place']) $conlist .= "<td style=\"padding-left: 10px;\">{$rs['place']}</td>";
		else $conlist .= "<td></td>";
		$conlist .= "</tr>\n";

		$condata[] = [
			'id' => $rs['id'],
			'dateset' => $coninfo,
			'userdyn' => ( $_SESSION['user_id'] ? getdynamicconventhtml($rs['id'],'visited',$rs['visited']) : '' ),
			'name' => $rs['name'],
			'year' => $rs['year'],
			'place' => $rs['place']
		];

	}
	$conlist .= "</table>\n";

// Liste over aliaser
	$aliaslist = getaliaslist($conset,$this_type);
// Trivia:
	$trivialist = gettrivialist($conset,$this_type);
// Links:
	$linklist = getlinklist($conset,$this_type);

	$filelist = getfilelist($conset,$this_type);

// Evt. forside-billede
	$available_pic = 0;
	// Create thumbnail
	if (file_exists("gfx/conset/l_".$conset.".jpg") && !file_exists("gfx/conset/s_".$conset.".jpg")) {
		image_rescale_save('gfx/conset/l_'.$conset.'.jpg','gfx/conset/s_'.$conset.'.jpg',200,200);
	}

	if (file_exists("gfx/conset/s_".$conset.".jpg")) {
		$consetpic = "<a href=\"gfx/conset/l_".$conset.".jpg\"><img src=\"gfx/conset/s_".$conset.".jpg\" alt=\"Forside til ".htmlspecialchars($r['name'])."\" style=\"float:right; border: 1px solid black; margin: 2px;\" /></a>";
		$available_pic = 1;
	}


// Smarty

	$t->assign('pagetitle',$r['name']);
	$t->assign('type',$this_type);

	$t->assign('id',$conset);
	$t->assign('name',$r['name']);
	$t->assign('pic',$available_pic);
	$t->assign('description',$r['description']);
	$t->assign('conlist',$conlist);
	$t->assign('condata',$condata);
	$t->assign('trivia',$trivialist);
	$t->assign('link',$linklist);
	$t->assign('alias',$aliaslist);
	$t->assign('filelist',$filelist);
	$t->assign('filedir','conset');

	$t->display('data.tpl');

}
?>
