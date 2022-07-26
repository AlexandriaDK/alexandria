<?php
require "adm.inc.php";
require "base.inc.php";

$cat = $_REQUEST['cat'];
$id = $_REQUEST['data_id'];

switch ($cat) {
	case 'awards':
		$returl = 'awards.php?category=convent&data_id=';
		break;

	case 'sce':
	case 'game':
	case 'scenarie':
		$returl = 'game.php?game=';
		break;

	case 'conset':
		$returl = 'conset.php?conset=';
		break;

	case 'sys':
	case 'system':
	case 'gamesystem':
		$returl = 'gamesystem.php?system=';
		break;

	case 'con':
	case 'convent':
	case 'convention':
		$returl = 'convention.php?con=';
		break;

	case 'review':
		$returl = 'review.php?review_id=';
		break;

	case 'issue':
		$returl = 'magazine.php?issue_id=';
		break;

	case 'magazine':
		$returl = 'magazine.php?magazine_id=';
		break;

	case 'tag':
		if (ctype_digit($id)) {
			$returl = 'tag.php?tag_id=';
		} else {
			$returl = 'tag.php?tag=';
		}
		break;

	case 'aut':
	case 'person':
	default:
		$returl = 'person.php?person=';
}

header("Location: " . $returl . rawurlencode($id));
