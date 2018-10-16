<?php
require_once("./connect.php");
require_once("base.inc");

function getFullEntry ($cat, $data_id) {
	$result = array();
	$data_id = intval($data_id);
	switch ($cat) {
		case 'aut':
			$r = getrow("SELECT id, CONCAT(firstname,' ',surname) AS name, birth, WEEKDAY(birth) AS birthday, picfile FROM aut WHERE id = $data_id");
			if ($r) {
				$result = ['id' => (int) $r['id'], 'name' => $r['name'], 'picfile' => $r['picfile']];
				$scenarios = getall("
					SELECT
						sce.id,
						sce.title AS title,
						title.title AS auttitle,
						title.iconfile,
						title.iconwidth,
						title.iconheight,
						MIN(convent.year) AS firstyear,
						MIN(convent.begin) AS firstbegin,
						COUNT(files.id) AS files
					FROM
						asrel,
						title,
						sce
					LEFT JOIN csrel ON
						csrel.sce_id = sce.id
					LEFT JOIN convent ON
						csrel.convent_id = convent.id
					LEFT JOIN files ON
						sce.id = files.data_id AND files.category = 'sce' AND files.downloadable = 1
					WHERE
						sce.id = asrel.sce_id AND
						asrel.tit_id = title.id AND
						asrel.aut_id = '$data_id' 
					GROUP BY
						sce.id
					ORDER BY
						firstyear,
						firstbegin,
						title.id,
						sce.title
				");
				$result['scenarios'] = $scenarios;
			}
			break;
		case 'sce':
			$r = getrow("SELECT id, CONCAT(firstname,' ',surname) AS name, birth, WEEKDAY(birth) AS birthday, description, picfile FROM aut WHERE id = $date_id");
			break;
		default:
			$r = array();
	}	
	return $result;
}
?>
