<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'review';

$review_id = (string) $_REQUEST['review_id'] ?? '';
$data_id = (int) $_REQUEST['data_id'] ?? NULL;
$category = (string) $_REQUEST['category'] ?? 'game';
$action = (string) $_REQUEST['action'] ?? '';
$review_title = (string) $_REQUEST['review_title'];
$description = (string) $_REQUEST['description'];
$spoilertext = (string) $_REQUEST['description'];
$reviewer = (string) $_REQUEST['reviewer'];
$syndicatedurl = (string) $_REQUEST['syndicatedurl'];
$published = (string) ( $_REQUEST['published'] ?? date("Y-m-d H:i:s") );
$visible = (int) (bool) $_REQUEST['visible'];
$language = (string) $_REQUEST['language'];
$user_id = (int) $_REQUEST['user_id'] ?? NULL;
$gameidtitle = "";

if ( $action ) {
	validatetoken( $token );
}

if ( ! $action && $review_id) {
	list($id, $data_id, $category, $review_title, $description, $reviewer, $syndicatedurl, $published, $visible, $language) = getrow("SELECT id, data_id, category, title, description, reviewer, syndicatedurl, published, visible, language FROM reviews WHERE id = $review_id");
	$game_title = getone( "SELECT title FROM sce WHERE id = $data_id");
	$gameidtitle = $data_id . " - " . $game_title;
}

if ( ( $action == "edit" || $action == "create" ) ) {
	$review_title = trim($review_title);
	$description = trim($description);
	if ( $review_title === '' ) {
		$_SESSION['admin']['info'] = "Title is missing!";
	} elseif ( ! $data_id) {
		$_SESSION['admin']['info'] = "Data ID is missing!";
	} elseif ( ! $category) {
		$_SESSION['admin']['info'] = "Category is missing!";
	} else {
		if ( $action == 'create') {
			$q = "INSERT INTO reviews () VALUES ()";
			$r = doquery($q);
			if ( ! $r) {
				$_SESSION['admin']['info'] = "Database error creating review!";
				rexit( $this_type );
			}
			$review_id = dbid();
		}
		$q = "UPDATE reviews SET " .
			"data_id = " . $data_id . ", " .
			"category = '" . dbesc( $category ) . "', " .
			"title = '" . dbesc( $review_title ) . "', " .
			"description = '" . dbesc( $description) . "', ".
			"reviewer = '" . dbesc( $reviewer) . "', ".
			"syndicatedurl = '" . dbesc( $syndicatedurl) . "', ".
			"published = '" . dbesc( $published) . "', ".
			"visible = '" . dbesc( $visible) . "', ".
			"language = '" . dbesc( $language) . "' ".
			"WHERE id = " . $review_id;
		$r = doquery($q);
		$logtext = "Review " . ( $action == 'edit' ? 'edited' : 'created');
		if ($r) {
			chlog($review_id, $this_type, $logtext );
		}
		$_SESSION['admin']['info'] = $logtext . "! " . dberror();
	}
	rexit( $this_type, [ 'review_id' => $review_id ] );
}

$js = '
$(function() {
	$( ".gamelookup" ).autocomplete({
		source: availableGames,
		delay: 100
	});
';

htmladmstart( "Review" );

$languagename = "";
if ( $language ) {
	$languagename = getLanguageName( $language );
}
print '<form action="review.php" method="post" onsubmit="return reviewSubmit();">';
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
if (!$review_id) {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"create\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"category\" value=\"game\">\n";
} else {
	print "<INPUT TYPE=\"hidden\" name=\"action\" value=\"edit\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"review_id\" value=\"$review_id\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"data_id\" value=\"$data_id\">\n";
	print "<INPUT TYPE=\"hidden\" name=\"category\" value=\"$category\">\n";
}

print "<a href=\"review.php\">New review</a>";

print "<table border=0>\n";

if ($review_id) {
	print "<tr><td>ID</td><td>$review_id - <a href=\"../data?review=$review_id\" accesskey=\"q\">Show public review</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$review_id\">Show log</a>";
	}
	print "\n</td></tr>\n";
}

print '<tr><td>Game</td><td><input name="data_id" type="text" id="data_id" class="gamelookup" value="' . htmlspecialchars($gameidtitle) . '"></td></tr>';

tr( "Review title", "review_title", $review_title, '', "E.g. 'Best fun ever'" );
tt( "Description", "description", $description );
tr( "Reviewer", "reviewer", $reviewer, '', 'Name of person or group who reviewed the game' );
tr( "URL", "syndicatedurl", $syndicatedurl, '', 'URL of review if imported from external site' );
tr( "Review date", "published", $published, '', 'YYYY-MM-DD - leave blank for today' );
print '<tr><td>Language code</td><td><input type="text" id="language" name="language" value="' . htmlspecialchars( $language ) . '" placeholder="Two letter ISO language code, e.g.: sv" size="40" maxlength="6"></td><td id="languagenote">' . htmlspecialchars($languagename) . '</td></tr>';
print '<tr valign=top><td>Visible?</td><td><input type="checkbox" name="visible" ' . ($visible ? 'checked="checked"' : '') . '></td></tr>';

print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($review_id ? "Update" : "Create").' review">' . ($review_id ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete review?\n\nAs a safety precaution all relations will be checked.\');" style="border: 1px solid #e00; background: #f77;">' : '') . '</td></tr>';

if ($review_id) {
// Show tickets
	print showtickets($review_id,$this_type);
}
?>

</table>
</form>

<script>
$("#language").change(function() {
	$.get( "lookup.php", { type: 'languagecode', label: $("#language").val() } , function( data ) {
		$("#languagenote").text( data );
	});
});
</script>

</body>
</html>
