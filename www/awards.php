<?php
require("./connect.php");
require("base.inc.php");

$this_type = 'awards';

$html = "";

$ucid = $cid = (int) ($_GET['cid'] ?? 0);
if ($ucid == 1) award_achievement(96); // Fastaval conset

$awards = getall("SELECT a.id, a.name, a.conset_id, a.description, b.name AS conset_name FROM awards a LEFT JOIN conset b ON a.conset_id = b.id ORDER BY b.name, a.conset_id, a.id");
// $award_categories = getall("SELECT a.id, a.name, a.convent_id, a.description, b.name AS con_name, b.year FROM award_categories a LEFT JOIN convent b ON a.convent_id = b.id ORDER BY b.year DESC, a.id");
if (!$cid) {
	//$award_nominees = getall("SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.sce_id, b.id AS category_id, b.convent_id, b.name AS category_name, c.year, c.name AS con_name, c.conset_id, d.title FROM award_nominees a INNER JOIN award_categories b ON a.award_category_id = b.id LEFT JOIN convent c ON b.convent_id = c.id LEFT JOIN sce d ON a.sce_id = d.id ORDER BY c.year DESC, a.winner DESC, a.id");
	$award_nominees = [];
} else {
	$award_nominees = getall("
	SELECT a.id, a.name, a.award_category_id, a.nominationtext, a.winner, a.ranking, a.sce_id, b.id AS category_id, b.convent_id, b.name AS category_name, c.year, c.name AS con_name, c.conset_id, d.title, COALESCE(e.label,d.title) AS title_translation
	FROM award_nominees a
	INNER JOIN award_categories b ON a.award_category_id = b.id
	LEFT JOIN convent c ON b.convent_id = c.id
	LEFT JOIN sce d ON a.sce_id = d.id
	LEFT JOIN alias e ON d.id = e.data_id AND e.category = 'sce' AND e.language = '" . LANG . "' AND e.visible = 1
	WHERE c.conset_id = $cid
	ORDER BY c.year DESC, a.winner DESC, a.id
");
}
$awardset = [];
$awardnominees = [];
foreach($awards AS $award) {
	$cid = $award['conset_id'];
	if (!$cid) $cid = 0;
	$awardset[$cid]['name'] = $award['conset_name'];
	$awardset[$cid]['data'][] = ['name' => $award['name'] ];
}
// Kan slås sammen til én - og dermed fjerne ovenstående
foreach ($award_nominees AS $nominee) {
	$cid = $nominee['conset_id'];
	$con_id = $nominee['convent_id'];
	$cat_id = $nominee['category_id'];
	if (!$cid) $cid = 0;
	$awardnominees[$cid][$con_id]['name'] = $nominee['con_name'];
	$awardnominees[$cid][$con_id]['year'] = yearname( $nominee['year'] );
	$awardnominees[$cid][$con_id]['categories'][$cat_id]['name'] = $nominee['category_name'];
	$awardnominees[$cid][$con_id]['categories'][$cat_id]['nominees'][] = ['id' => $nominee['id'], 'name' => $nominee['name'], 'nominationtext' => $nominee['nominationtext'], 'winner' => $nominee['winner'], 'ranking' => $nominee['ranking'], 'sce_id' => $nominee['sce_id'], 'title' => $nominee['title_translation'], 'origtitle' => $nominee['title'] ];
}

if (!$ucid) {
	$html .= "<div>";
	foreach($awardset AS $list_cid => $award) {
		if ($list_cid) {
			$html .= "<h3><a href=\"awards?cid=" . $list_cid . "\">" . htmlspecialchars($award['name']) . "</a></h3>";
		} else {
			$html .= "<h3>" . htmlspecialchars($award['name']) . "</h3>" . PHP_EOL;
		}
	}
	$html .= "</div>" . PHP_EOL;
} else {
	$html .= "<h2>" . htmlspecialchars($awardset[$ucid]['name']) . "</h2>";
	$csname = $awardset[$ucid]['name'];
	$years = [];
	$categories = [];
	foreach($awardnominees[$cid] AS $convent) {
		$years[$convent['year']] = TRUE;
		foreach($convent['categories'] AS $category) {
			$categories[$category['name']] = TRUE;
		}
	}
	$html .= "<ul class=\"awardlist\">";
	foreach($years AS $year => $true) {
		//$html .= "<li class=\"yearselector\" data-year=\"$year\" onclick=\"selectAwardOption(this.dataset.year, 'year');\">" . $year . "</li>";
		$html .= "<li class=\"yearselector\" data-year=\"$year\" onclick=\"toggleAwardOptions(this.dataset.year, 'year');\">" . $year . "</li>";
	}
	$html .= "</ul>" . PHP_EOL;

	// Mangler award-info, fx "Otto" eller "Den Gyldne Svupper" - skal nok alligevel lægges sammen
	$html .= "<ul class=\"awardlist\">";
	foreach($categories AS $category => $true) {
//		$html .= "<li class=\"categoryselector\" data-category=\"" . htmlspecialchars($category) . "\" onclick=\"selectAwardOption(this.dataset.category, 'category');\">" . $category . "</li>";
		$html .= "<li class=\"categoryselector\" data-category=\"" . htmlspecialchars($category) . "\" onclick=\"toggleAwardOptions(this.dataset.category, 'category');\">" . $category . "</li>";
		//$html .= "<li class=\"categoryselector\" data-category=\"" . htmlspecialchars($category) . "\" onclick=\"selectAwardCategory(this.getAttribute('data-category'));\">" . $category . "</li>";
	}
	$html .= "</ul>" . PHP_EOL;

	$html .= "<div class=\"clear\"></div>" . PHP_EOL;

	foreach ($awardnominees[$cid] AS $conid => $convent) {
		$htmlid = "con" . $conid;
		$html .= "<div class=\"awardyear\" data-year=\"" . $convent['year'] . "\">";
		$html .= "<h3 id=\"$htmlid\">" . getdatahtml('convent', $conid, $convent['name'] . " (" . ( $convent['year'] ) . ")") . "</h3>";
		$html .= "<div class=\"awardblock\">" . PHP_EOL;
		foreach($convent['categories'] AS $category) {
			$html .= PHP_EOL . "<div class=\"awardcategory\" data-category=\"" . htmlspecialchars($category['name']) . "\">" . PHP_EOL;
			$html .= "<h4>" . htmlspecialchars($category['name']) . "</h4>" . PHP_EOL;
			foreach($category['nominees'] AS $nominee) {
				$class = ($nominee['winner'] == 1 ? "winner" : "nominee");
				$html .= "<div class=\"" . $class . "\">";
				$html .= "<h5 class=\"" . $class . "\">";
				$html .= "<span class=\"" . $class . "\">";
				if ($nominee['sce_id']) {
					$html .= getdatahtml('sce', $nominee['sce_id'], $nominee['title']);
				} else {
					$html .= htmlspecialchars($nominee['name']);
				}
				$html .= "</span>";
				if ($nominee['nominationtext']) {
					$nt_id = "nominee_text_" . $nominee['id'];
					$html .= " <span onclick=\"document.getElementById('$nt_id').style.display='block'; this.style.display='none'; return false;\" class=\"atoggle\" title=\"" . htmlspecialchars($t->getTemplateVars('_award_show_nominationtext') ) ."\">[+]</span>";

				}
				$html .=  "</h5>";
				if ($nominee['ranking']) {
					$html .= "<div class=\"ranking\">(" . htmlspecialchars($nominee['ranking']) . ")</div>" . PHP_EOL;
				}
				if ($nominee['nominationtext']) {
					$html .= "<div class=\"nomtext\" style=\"display: none;\" id=\"$nt_id\">" . nl2br(htmlspecialchars(trim($nominee['nominationtext'])), FALSE) . "</div>" . PHP_EOL;
				}

				$html .= "</div>" . PHP_EOL;
			}
			$html .= "</div>" . PHP_EOL;
		}
		$html .= "</div>" . PHP_EOL;
		$html .= "</div>" . PHP_EOL;
		
		
	}
	$html .= "<div class=\"clear\"></div>" . PHP_EOL;
}

$t->assign('html_content', $html);
$t->assign('type',$this_type);
$t->assign('id', $ucid);
$t->assign('cid', $ucid);
$t->assign('csname', $csname ?? "");
$t->assign('ogimage', 'gfx/fastaval_otto_original.jpg');
$t->display('awards.tpl');

?>
