<?php
require("./connect.php");
require("base.inc");
require("template.inc");

// skal laves om til en funktion
$value = "";

$data_id = (int) $_REQUEST['data_id'];
$cat = $_REQUEST['cat'];
$label = getentry($cat,$data_id);

$content = "";
if ($label) {
	$content .= "<tr><td>";
	$content .= "<input type=\"hidden\" name=\"cat\" value=\"".htmlspecialchars($cat)."\" />\n";
	$content .= "<input type=\"hidden\" name=\"data_id\" value=\"".htmlspecialchars($data_id)."\" />\n";
	$content .= "Indsend rettelse for:</td><td><b>".htmlspecialchars($label)."</b></td></tr>\n";
} else {
	$content .= "<tr><td>Vælg en kategori:</td><td>";
	$content .= '<select name="cat"><option value="sce">Scenarie</option><option value="aut">Person</option><option value="convent">Con</option><option value="conset">Con-serie</option><option value="system">System</option><option>---</option><option value="other">Andet</option></select>';
	$content .= "</td></tr>\n";
	$content .= "<tr><td>Indtast navn eller titel:</td><td><input type=\"text\" name=\"data_label\" size=\"30\" maxlength=\"250\" /><br /><span class=\"noteindtast\">Fx \"Oculus Tertius\" eller \"Spiltræf XII\"</span></td></tr>\n";
}

$content .= "<tr><td>Indtast din rettelse eller tilføjelse:</td><td><textarea name=\"data_description\" cols=\"30\" rows=\"8\"></textarea></td></tr>\n";

$content .= "<tr><td>Dit navn?</td><td><input type=\"text\" name=\"user_name\" size=\"30\" value=\"".htmlspecialchars($_SESSION['user_name'])."\" /></td></tr>\n";
$content .= "<tr><td>Din e-mail-adresse?</td><td><input type=\"email\" name=\"user_email\" size=\"30\" /><br /><span class=\"noteindtast\">Vi skriver kun til dig, hvis vi har evt. spørgsmål</span></td></tr>\n";
$content .= "<tr><td>Hvad er din kilde?</td><td><textarea name=\"user_source\" cols=\"30\" rows=\"3\"></textarea><br /><span class=\"noteindtast\">Angiv evt. en URL, et con-program, \"mig selv\", \"fra hukommelsen\" eller lignende</span></td></tr>\n";
$content .= "<tr><td>Indtast bogstavet <b>A</b>:<br />(for spamsikring)</td><td><input type=\"text\" name=\"human\" value=\"\" size=\"3\" /></td></tr>\n";


$t->assign('content',$content);
$t->assign('category',$cat);
$t->assign('data_id',$data_id);
$t->assign('label',$label);
$t->display('update_input.tpl');
?>
