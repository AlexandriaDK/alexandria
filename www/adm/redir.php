<?php
require "adm.inc";
require "base.inc.php";

$cat = $_REQUEST['cat'];
$id = $_REQUEST['data_id'];

switch ($cat) {

	case 'awards':
	$returl = 'awards.php?category=convent&data_id=';
	break;

	case 'sce':
	case 'game':
	$returl = 'game.php?game=';
	break;

	case 'conset':
	$returl = 'conset.php?conset=';
	break;

	case 'sys':
	$returl = 'system.php?system=';
	break;

	case 'convent':
	$returl = 'convent.php?con=';
	break;

	case 'tag':
	$returl = 'tag.php?tag_id=';
	break;

	case 'aut':
	default:
	$returl = 'person.php?person=';
}

header("Location: ".$returl.$id);
?>
