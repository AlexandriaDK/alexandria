<?php
require("./connect.php");
require("./base.inc.php");
$output = "";

$likesearch = likeesc((string) $_REQUEST['q']);

if ($_REQUEST['action'] == "lookup") {
	if ($_REQUEST['q']) {
		$query = "
			SELECT id, CONCAT(firstname,' ',surname) AS label, 'aut' AS type, 'person' AS linkpart FROM person WHERE CONCAT(firstname,' ',surname) LIKE '$likesearch%'
			UNION ALL
			SELECT id, CONCAT(surname,', ',firstname) AS label, 'aut' AS type, 'person' AS linkpart FROM person WHERE CONCAT(surname,', ',firstname) LIKE '$likesearch%'
			UNION ALL
			SELECT id, title AS label, 'sce' AS type, 'scenarie' AS linkpart FROM person WHERE title LIKE '$likesearch%'
			UNION ALL
			SELECT id, name AS label, 'sys' AS type, 'system' AS linkpart FROM gamesystem WHERE name LIKE '$likesearch%'
			UNION ALL
			SELECT id, CONCAT(name,' (',year,')') AS label, 'convent' AS type, 'con' AS linkpart FROM convention WHERE name LIKE '$likesearch%' OR CONCAT(name,' (',year,')') LIKE '$likesearch%' OR CONCAT(name,' ',year) LIKE '$likesearch%'
			ORDER BY label
			
		";
		$result = mysqli_query($dblink,$query) or die(mysqli_error($dblink));
		$i=0;
		while($row = mysqli_fetch_row($result)) {
			print "<div class=\"result\">".getdatahtml($row[2],$row[0],$row[1])."</div>\n";
			$i++;
			if ($i > 10) {
				print "<div class=\"result\">...</div>\n";
				break;
			}
		}
	}
	exit;
} elseif ($_REQUEST['action'] == "adduserlog" && $_SESSION['user_id'] && $_REQUEST['data_id'] && $_REQUEST['category'] && $_REQUEST['type'] ) {
	$token = $_REQUEST['token'];
	if ( compare_tokens( $token, $_SESSION['token'] ) ) {
		adduserlog($_SESSION['user_id'],$_REQUEST['category'],$_REQUEST['data_id'],$_REQUEST['type']);
		$newlabel = $t->getTemplateVars('_top_' . $_REQUEST['type'] . '_pt') ?? 'Done';
		$output = [ 'newlabel' => $newlabel, 'newdirection' => 'remove', 'switch' => $t->getTemplateVars( '_switch' ) ];
		
		// achievements
		if ($_REQUEST['category'] == 'sce') {
			list($gamesystem_id, $boardgame) = getrow("SELECT gamesystem_id, boardgame FROM game WHERE id = " . (int) $_REQUEST['data_id'] );
			$fanboy_count = getone("SELECT 1 FROM userlog INNER JOIN pgrel ON userlog.data_id = pgrel.game_id AND pgrel.title_id = 1 INNER JOIN users ON userlog.user_id = users.id WHERE userlog.category = 'sce' AND userlog.type = 'played' AND users.person_id != pgrel.person_id AND user_id = " . $_SESSION['user_id'] . " GROUP BY pgrel.person_id, userlog.user_id HAVING COUNT(*) >= 10"); // played at least 10 scenario from another author
			$polandsce = getcol("SELECT DISTINCT game_id FROM gamerun WHERE country = 'pl'");
			if ($_REQUEST['type'] == 'read') {
				award_achievement(3);
			}	
			if ($_REQUEST['type'] == 'played') {
				award_achievement(4);
			}	
			if ($_REQUEST['type'] == 'gmed') {
				award_achievement(5);
			}
			if ($boardgame == 1) {
				award_achievement(87); // board game
			}
			if ($gamesystem_id == 99) { // System: Hinterlandet
				award_achievement(88); // play, read or GM Hinterlandet
			}
			if ($fanboy_count) {
				award_achievement(89); // played at least 10 scenarios written by the same author
			}
			if (in_array($_REQUEST['data_id'], $polandsce) ) { 
				award_achievement(95); // attend scenario in Poland
			}
		} elseif ($_REQUEST['category'] == 'convent') {
			$future = getone("SELECT begin > NOW() + INTERVAL 7 DAY AS future FROM convention WHERE id = " . (int) $_REQUEST['data_id'] );
			if ($future) {
				award_achievement(102); // attend a convention in the future (+ 7 days due to organizer setup)
			}
		}
	} else {
		$output = compare_token_error( $token, $_SESSION['token'] );
	}	
} elseif ($_REQUEST['action'] == "removeuserlog" && $_SESSION['user_id'] && $_REQUEST['data_id'] && $_REQUEST['category'] && $_REQUEST['type']) {
	$token = $_REQUEST['token'];
	if ( compare_tokens( $token, $_SESSION['token'] ) ) {
		removeuserlog($_SESSION['user_id'],$_REQUEST['category'],$_REQUEST['data_id'],$_REQUEST['type']);
		$newlabel = $t->getTemplateVars('_top_not_' . $_REQUEST['type'] . '_pt') ?? 'Done';
		$output = [ 'newlabel' => $newlabel, 'newdirection' => 'add', 'switch' => $t->getTemplateVars( '_switch' ) ];
	} else {
		$output = compare_token_error( $token, $_SESSION['token'] );
	}
}
if ( $output !== "" ) {
	print json_encode( $output );
}
?>
