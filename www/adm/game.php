<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$this_type = 'game';
$this_type_old = 'sce';

$action = $_REQUEST['action'] ?? FALSE;
$jsenabled = $_REQUEST['jsenabled'] ?? FALSE;

if ( $action ) {
	validatetoken( $token );
}

$game = (int) ($_REQUEST['game'] ?? FALSE);
$title = (string) ($_REQUEST['title'] ?? '');
$descriptions = (array) ($_REQUEST['descriptions'] ?? []);
$langlock = $_COOKIE['langlock'] ?? LANG;
if (!$descriptions) {
	$descriptions = [1 => [ 'id' => 1, 'language' => $langlock, 'description' => '', 'note' => '' ] ];
}
$internal = $_REQUEST['internal'] ?? FALSE;
$gamesystem_id = (int) ($_REQUEST['gamesystem_id'] ?? FALSE);
$gamesystem_extra = $_REQUEST['gamesystem_extra'] ?? '';
$aut = $_REQUEST['aut'] ?? FALSE;
$person_extra = $_REQUEST['person_extra'] ?? '';
$con = $_REQUEST['con'] ?? FALSE;
$boardgame = (int) (bool) ($_REQUEST['boardgame'] ?? FALSE);
$person = (array) ($_REQUEST['person'] ?? []);

$gms = (string) ($_REQUEST['gms'] ?? '');
$players = (string) ($_REQUEST['players'] ?? '');
list ($gms_min, $gms_max) = strSplitParticipants($gms);
list ($players_min, $players_max) = strSplitParticipants($players);

$participants_extra = $_REQUEST['participants_extra'] ?? '';

$this_id = $game;

if (!$action && $game) {
	$row = getrow("SELECT * FROM game WHERE id = '$game'");
	if ($row) {
		$title = $row['title'];
		$descriptions = getall("SELECT id, description, language, note FROM game_description WHERE game_id = $game ORDER BY priority, language");
		if (!$descriptions) {
			$descriptions = [1 => [ 'id' => 1, 'language' => $langlock, 'description' => '', 'note' => '' ] ];
		}
		$internal = $row['internal'];
		$gamesystem_id = $row['gamesystem_id'];
		$gamesystem_extra = $row['gamesystem_extra'];
		$person_extra = $row['person_extra'];
		$gms_min = $row['gms_min'];
		$gms_max = $row['gms_max'];
		$players_min = $row['players_min'];
		$players_max = $row['players_max'];
		$participants_extra = $row['participants_extra'];
		$boardgame = $row['boardgame'];

		$gms = ($gms_max != $gms_min ? $gms_min . "-" . $gms_max : $gms_min);
		$players = ($players_max != $players_min ? $players_min . "-" . $players_max : $players_min);
		
	} else {
		$game = FALSE;
	}
}

// Update game
if ($action == "update" && $game) {
	if (!$title) {
		$_SESSION['admin']['info'] = "You are missing a title!";
	} else {
		$title = trim($title);
		$q = "UPDATE game SET " .
		     "title = '".dbesc($title)."', " .
		     "gamesystem_id = " . sqlifnull($gamesystem_id) . ", " .
		     "gamesystem_extra = '".dbesc($gamesystem_extra)."', " .
		     "person_extra = '".dbesc($person_extra)."', " .
		     "internal = '".dbesc($internal)."', " .
		     "gms_min = " . strNullEscape($gms_min).", " .
		     "gms_max = " . strNullEscape($gms_max).", " .
		     "players_min = " . strNullEscape($players_min).", " .
		     "players_max = " . strNullEscape($players_max).", " .
		     "participants_extra = '".dbesc($participants_extra)."', " .
		     "boardgame = $boardgame " .
		     "WHERE id = $game";
		$r = doquery($q);
		if ($r) {
			doquery("DELETE FROM game_description WHERE game_id = $game");
			$inserts = [];
			foreach($descriptions AS $d) {
				if ($d['description'] !== "") {
					$inserts[] = "($game, '" . dbesc(trim($d['description'])) . "', '" . dbesc(trim($d['language'])) . "','" . dbesc(trim($d['note'])) . "')";
				}
			}
			if ($inserts) {
				$sql = "INSERT INTO game_description (game_id, description, language, note) VALUES " . implode(",", $inserts);
				$r = doquery($sql);
			}

			$q = "DELETE FROM pgrel WHERE game_id = '$game'";
			$r = doquery($q);
			foreach( $person AS $autdata) {

				$person_id = (int) $autdata['name'];
				$title_id = (int) $autdata['title'];
				$note = trim( (string) $autdata['note']);
				if ($title_id && $person_id) {
					$q = "INSERT INTO pgrel (game_id, person_id, title_id, note) ".
					     "VALUES ($game, $person_id, $title_id, '" . dbesc( $note ) ."')";
					$r = doquery($q);
					print dberror();
				}
			}
	
		}
		print dberror();
		if ($r) {
			chlog($game,$this_type,"Game updated");
		}

// Only change relations if javascript is enabled
		if ($jsenabled == "1") {
	
	// Add person-game relations
	// Add game-con relations
	
			$q = "DELETE FROM cgrel WHERE game_id = '$game'";
			$r = doquery($q);
			foreach( (array) $con AS $condata) {
				list($presentation_id,$con_id) = explode("_",$condata);
				$presentation_id = (int) $presentation_id;
				$con_id = (int) $con_id;
				if ($presentation_id && $con_id) {
					$q = "INSERT INTO cgrel (game_id, convention_id, presentation_id) ".
					     "VALUES ($game, $con_id, $presentation_id)";
					$r = doquery($q);
					print dberror();
				}
			}
		}

// Done!
		$_SESSION['admin']['info'] = "Game updated! " . dberror();
		rexit($this_type, ['game' => $game] );
	}
}

// Delete game
if ($action == "Delete" && $game) { // should check if game exists
	$error = [];
	if (getCount('pgrel', $this_id, FALSE, $this_type) ) $error[] = "person";
	if (getCount('cgrel', $this_id, FALSE, $this_type) ) $error[] = "con";
	if (getCount('ggrel', $this_id, FALSE, $this_type) ) $error[] = "genre";
	if (getCount('gamerun', $this_id, FALSE, $this_type) ) $error[] = "run";
	if (getCount('trivia', $this_id, FALSE, $this_type) ) $error[] = "trivia";
	if (getCount('links', $this_id, FALSE, $this_type) ) $error[] = "link";
	if (getCount('files', $this_id, FALSE, $this_type) ) $error[] = "file";
	if (getCount('alias', $this_id, FALSE, $this_type) ) $error[] = "alias";
	if (getCount('tags', $this_id, FALSE, $this_type) ) $error[] = "tags";
	if (getCount('article', $this_id, FALSE, $this_type) ) $error[] = "article";
	if (getCount('userlog', $this_id, TRUE, $this_type_old) ) $error[] = "user log (requires admin)";
	if ($error) {
		$_SESSION['admin']['info'] = "Can't delete. The game still has the following references: " . implode(", ",$error);
		rexit($this_type, ['game' => $game] );
	} else {
		$title = getone("SELECT title FROM game WHERE id = $game");

		doquery("DELETE FROM game_description WHERE game_id = $game");
		$q = "DELETE FROM game WHERE id = $game";
		$r = doquery($q);

		if ($r) {
			chlog($game,$this_type,"Game deleted: $title");
		}
		$_SESSION['admin']['info'] = "Game deleted! " . dberror();
		rexit($this_type, ['game' => $game] );
	}
}

// Create game
if ($action == "create") {
	if (!$title) {
		$info = "Title is missing!";
	} else {
		$title = trim($title);
		$q = "INSERT INTO game (id, title, internal, gamesystem_id, gamesystem_extra, person_extra, gms_min, gms_max, players_min, players_max, participants_extra, boardgame) " .
			 "VALUES (NULL, '".dbesc($title)."', '".dbesc($internal)."', ".sqlifnull($gamesystem_id).", '".dbesc($gamesystem_extra)."', '".dbesc($person_extra)."', " . strNullEscape($gms_min) . ", " . strNullEscape($gms_max) . ", " . strNullEscape($players_min) . ", " . strNullEscape($players_max) . ", '" . dbesc($participants_extra) . "', $boardgame)";
		$r = doquery($q);
		if ($r) {
			$game = dbid();
			$inserts = [];
			$insertcount = 0;
			foreach($descriptions AS $d) {
				if ($d['description'] !== "") {
					$insertcount++;
					$inserts[] = "($game, '" . dbesc($d['description']) . "', '" . dbesc($d['language']) . "','" . dbesc($d['note']) . "')";
				}
			}
			if ($insertcount > 0) { // only run query if at least one description; otherwise SQL is invalid
				$sql = "INSERT INTO game_description (game_id, description, language, note) VALUES " . implode(",", $inserts);
				$r = doquery($sql);
			}
			chlog($game,$this_type,"Game created");
		}
		$_SESSION['admin']['info'] = "Game created! " . dberror();

// Add person-game relations
		foreach( $person AS $autdata) {

			$person_id = (int) $autdata['name'];
			$title_id = (int) $autdata['title'];
			$note = trim((string) $autdata['note']);
			if ($title_id && $person_id) {
				$q = "INSERT INTO pgrel (game_id, person_id, title_id, note) ".
				     "VALUES ($game, $person_id, $title_id, '" . dbesc( $note ) ."')";
				$r = doquery($q);
				print dberror();
			}
		}

// Relation system for cons only works if javascript is enabled
		if ($jsenabled == "1") {
	
	// Add game-con relations
			foreach( (array) $con AS $condata) {
				unset($presentation_id,$con_id);
				list($presentation_id,$con_id) = explode("_",$condata);
				if ($presentation_id && $con_id) {
					$q = "INSERT INTO cgrel (game_id, convention_id, presentation_id) ".
					     "VALUES ('$game', '$con_id', '$presentation_id')";
					$r = doquery($q);
					print dberror();
				}
			}
		}

// Done!
	rexit($this_type, ['game' => $game] );
	}
}

# Get existing person relations

if ($game) {
	$qrel = getall("
	SELECT pgrel.id AS relid, p.id, CONCAT(p.firstname,' ',p.surname) AS name, pgrel.note, pgrel.title_id AS titid, title.title
	FROM pgrel
	INNER JOIN person p ON pgrel.person_id = p.id
	LEFT JOIN title ON pgrel.title_id = title.id
	WHERE pgrel.game_id = $game
	ORDER BY title.priority, pgrel.title_id, p.surname, p.firstname
");
	print dberror();
}

# Get existing con relations

if ($game) {
	$qcrel = getall("
		SELECT
			cgrel.id AS relid,
			c.id,
			c.name,
			c.year,
			c.begin,
			c.end,
			p.id AS preid,
			p.event,
			conset.name AS setname
		FROM cgrel
		INNER JOIN convention c ON cgrel.convention_id = c.id
		INNER JOIN presentation p ON cgrel.presentation_id = p.id
		LEFT JOIN conset ON c.conset_id = conset.id
		WHERE
			cgrel.game_id='$game'
		ORDER BY
			p.id,
			c.year,
			c.begin,
			c.end,
			setname,
			c.name
	");
	print dberror();
}

// Get all cons
$con = [];
$q = getall("SELECT c.id, c.name, year, conset.name AS setname FROM convention c LEFT JOIN conset ON c.conset_id = conset.id ORDER BY setname, year, begin, end, name") OR die(dberror());
foreach($q AS $r) {
	$con[$r['id']] = $r['name']." (".$r['year'].")";
}

// Get all systems
$gamesystem = [];
$gamesystem[0] = "[unknown or unspecified RPG system]";
$q = getall("SELECT id, name FROM gamesystem ORDER BY name");
foreach($q AS $r) {
	$gamesystem[$r['id']] = $r['name'];
}


// Get all person titles
$tit = [];
$q = getall("SELECT id, title FROM title ORDER BY id");
foreach($q AS $r) {
	$tit[$r['id']] = $r['title'];
}

// Get all types of presentations
$pre = [];
$q = getall("SELECT id, event FROM presentation ORDER BY id");
foreach($q AS $r) {
	$pre[$r['id']] = $r['event'];
}

$people = [];
$r = getall("SELECT id, firstname, surname FROM person ORDER BY firstname, surname");
foreach($r AS $row) {
	$people[] = $row['id'] . " - " . $row['firstname'] . " " . $row['surname'];
}

$titles = getcolid("SELECT id, title FROM title ORDER BY id");
?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - Game</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/uistyle.css">
<link rel="icon" type="image/png" href="/gfx/favicon_ti_adm.png">
<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="adm.js"></script>
<script type="text/javascript">
var availableNames = <?php print json_encode($people); ?>;

$(document).on("keydown", ":input:not(textarea):not(:submit)", function(event) {
	if (event.key == "Enter" && ! event.ctrlKey) {
		if ( event.target.id !== 'ffind' ) {
			event.preventDefault();
		}
	}
});

$(document).on("keydown", "#theForm textarea", function(event) {
	if (event.key == "Enter" && event.ctrlKey) {
		$('#theForm').submit();
	}
});


$(function() {
	$( ".personlookup" ).autocomplete({
		source: availableNames,
		delay: 30,
		minLength: 3
	});
	$(".addnext").click( function() {
		for (let i = 0; i < 5; i++) { // five name inputs at a time
			var acount = $( "#persontable tr" ).length;
			var newcount = acount + 1;
			var options = '<?php print titleoptions( $titles, 'NEWCOUNT' ); ?>'.replace( 'NEWCOUNT', newcount );
			var dynhtml = '<tr data-personid="' + newcount + '"><td><input class="personlookup" name="person[' + newcount + '][name]" placeholder="Name"></td><td>' + options + '</td><td><input name="person[' + newcount + '][note]" placeholder="Optional note"></td><td><span class="atoggle" onclick="disabletoggle(' + newcount + ');">🗑️</span> <span title="Add new person" class="atoggle glow" onclick="addperson(' + newcount + ');">👤</span></td></tr>';
			var newtr = $( "#persontable" ).append( dynhtml );
			var bar = $( "#persontable" ).find('tr:last input:first')
				.autocomplete({
					source: availableNames,
					delay: 30,
					minLength: 3
				})
				.change( checkperson )
			;
		}

	});

	// add blank row for ease
	$(".addnext").click();
});

function removefrom(mm) {
	mmlen = mm.length ;
	for ( i=(mmlen-1); i>=0; i--) {
		if (mm.options[i].selected == true ) {
			mm.options[i] = null;
		}
	}
}

function addtocon(mm,contype) {
	m3len = m3.length;
	if (contype == 1) {
		prefix = '1_';
		suffix = '(Premiere)';
	} else if (contype == 2) {
		prefix = '2_';
		suffix = '(Re-run)';
	} else if (contype == 3) {
		prefix = '3_';
		suffix = '(Re-run, mod)';
	} else if (contype == 42) {
		prefix = '42_';
		suffix = '(Test run)';
	} else if (contype == 99) {
		prefix = '99_';
		suffix = '(Cancelled)';
	}
	for ( i=0; i<m3len ; i++){
		if (m3.options[i].selected == true ) {
			mmlen = mm.length;
			mm.options[mmlen]= new Option(m3.options[i].text+' '+suffix, prefix+m3.options[i].value);
		}
	}
}

function doSubmit() {
	for (i=0;i<m3.length;i++) {
		m3.options[i].selected = false;
	}
	for (i=0;i<m4.length;i++) {
		m4.options[i].selected = true;
	}
	if ( $('.personunsure:enabled').length + $('.persondoesnotexist:enabled').length > 0 ) {
		return confirm('Some people (marked with yellow and red background) are not confirmed and will be removed. Continue?');
	}
	return true;
}

// tabs
var countTabs = <?php print count($descriptions); ?>;
var tabs;

  $( function() {
    var tabTitle = $( "#tab_title" ),
      tabContent = $( "#tab_content" ),
      tabTemplate = "<li><a href='#{href}' data-id='#{id}' ondblclick='changeLanguage(this)' style='#{style}' title='#{notice}' >#{label}</a></li>",
      tabCounter = countTabs,
      tabContentTemplate = '<input type="hidden" name="descriptions[NUMBER][language]" value="MYLANGUAGE">' +
                           '<input type="hidden" name="descriptions[NUMBER][note]" value="">' +
                           '<textarea name="descriptions[NUMBER][description]" style="width: 100%;" rows=10></textarea>';
 
    tabs = $( "#tabs" ).tabs();
 
    $( "#add_my_tab" )
      .on( "click", function() {
	var language = prompt("Language (da for Danish, sv for Swedish, etc.)", "en");
	if (language) {
		tabCounter++;
		var errorStyle = 'text-decoration: maroon wavy underline';
		var label = language, notice = '', style = '';
		if (label.substr(0,2) == 'dk') {
			notice = 'Did you mean "da" for "Danish"?';
			style = errorStyle;
		}
		if (label.substr(0,2) == 'se') {
			notice = 'Did you mean "sv" for "Swedish"?';
			style = errorStyle;
		}

		var id = "d-" + tabCounter,
			li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ).replace( /#\{style\}/g, style ).replace( /#\{notice\}/g, notice ).replace( /#\{id\}/g, tabCounter ) ) ,
			content = tabContentTemplate.replace( /NUMBER/g, tabCounter ).replace( /MYLANGUAGE/, language),
			tabContentHtml = "Tab " + tabCounter + " content.";

		tabs.find( ".ui-tabs-nav" ).append( li );
		tabs.append( "<div id='" + id + "'>" + content + "</div>" );
		tabs.tabs( "refresh" );
		tabs.tabs("option", "active", (tabCounter - 1) );
		var selector = '#' + id + ' textarea';
		$(selector).focus();
		

	}
	return false;
      });
    
  } );

function changeLanguage( elem ) {
	console.log(elem);
	dcount = elem.getAttribute( 'data-id' );
	language = elem.innerHTML;
	var language = prompt("Language (da for Danish, sv for Swedish, etc.)", language);
	if ( language ) {
		var errorStyle = 'text-decoration: maroon wavy underline';
		var style = '';
		var notice = '';
		var lid = '#d-' + dcount + ' input:first-child';

		if (language.substr(0,2) == 'dk') {
			notice = 'Did you mean "da" for "Danish"?';
			style = errorStyle;
		}
		if (language.substr(0,2) == 'se') {
			notice = 'Did you mean "sv" for "Swedish"?';
			style = errorStyle;
		}

		$(elem).text( language );
		$(elem).attr( 'style', style );
		$(elem).attr( 'title', notice );
		$(lid).attr( 'value', language );

	}
}

</script>
</head>

<body>

<?php
include("links.inc.php");

printinfo();

function titleoptions ( $titles, $count, $default = FALSE ) {
	$html = '<select name="person[' . $count . '][title]">';
	foreach( $titles AS $id => $title ) {
		$html .= '<option value="' . $id . '"' . ($id == $default ? ' selected' : '') . '>' . htmlspecialchars( $title ) . '</option>';
	}
	$html .= '</select>';
	return $html;
}

print "<form action=\"game.php\" method=\"post\" id=\"theForm\" name=\"theForm\" onsubmit=\"return doSubmit();\">\n";
print '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
if (!$game) print "<input type=\"hidden\" name=\"action\" value=\"create\">\n";
else {
	print "<input type=\"hidden\" name=\"action\" value=\"update\">\n";
	print "<input type=\"hidden\" name=\"game\" value=\"$game\">\n";
}

print "<a href=\"./game.php\">New game</a>";

print "<table border=\"0\">\n";

if ($game) {
	print "<tr><td>ID</td><td>$game - <a href=\"../data?scenarie=$game\" accesskey=\"q\">Show game page</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$game\">Show log</a>";
	}
	print "\n</td></tr>\n";
}

print "<tr><td>Title</td><td><input type=text name=\"title\" id=\"title\" value=\"" . htmlspecialchars($title) . "\" size=50> <span id=\"titlenote\"></span></td></tr>\n";
print "<tr><td>Description<br><a href='#' id='add_my_tab' accesskey='e' title='Hotkey: E'>[+]</a></td><td style=\"width: 100%; margin-top; 0; padding-top: 0;\">";
$dcount = 0;
$lihtml = $inputhtml = '';
foreach($descriptions AS $d) {
	$dcount++;
	$lihtml .= "<li><a href=\"#d-" . $dcount . "\" data-id=\"" . $dcount . "\" ondblclick=\"changeLanguage(this)\">" . htmlspecialchars($d['language']) . ($d['note'] != '' ? " (" . htmlspecialchars($d['note']) . ")" : "") . "</a></li>" . PHP_EOL;
	$inputhtml .= "<div id=\"d-" . $dcount . "\" style=\"padding: 4px;\">" . PHP_EOL;
	$inputhtml .= "<input type=\"hidden\" name=\"descriptions[" . $dcount . "][language]\" value=\"" . htmlspecialchars($d['language']) . "\">" . PHP_EOL;
	$inputhtml .= "<input type=\"hidden\" name=\"descriptions[" . $dcount . "][note]\" value=\"" . htmlspecialchars($d['note']) . "\">" . PHP_EOL;
	$inputhtml .= "<textarea name=\"descriptions[" . $dcount . "][description]\" style=\"width: 100%;\" rows=10>" . htmlspecialchars($d['description']) . PHP_EOL . "</textarea>" . PHP_EOL;
	$inputhtml .= "</div>" . PHP_EOL . PHP_EOL;

}
print "<div id=\"tabs\" style=\"margin-top: 0;\">" . PHP_EOL;
print "<ul>" . $lihtml . "</ul>" . PHP_EOL;
print $inputhtml;
print "</div>";
print "</td></tr>\n";
print "<tr valign=top><td>Internal note</td><td style=\"width: 100%\"><textarea name=internal style=\"width: 100%\" rows=6>\n" . htmlspecialchars($internal) . "</textarea></td></tr>\n";


### Participants ###

print "<tr valign=top><td>Participants</td>";
print "<td>\n";
print "GMs: <input type=\"text\" name=\"gms\" value=\"" . $gms . "\" size=\"2\" \> - ";
print "Players: <input type=\"text\" name=\"players\" value=\"" . $players . "\" size=\"2\" \> - ";
print "possible more details: <input type=text name=\"participants_extra\" value=\"".htmlspecialchars($participants_extra)."\" size=30>\n";
print "<br /><span style=\"font-size: 0.8em;\">You can enter a range of players. E.g. type <i>4-6</i> for 4 to 6 players</span>\n";
print "</td>";
print "</tr>\n\n";

### Board game? ###

print "<tr><td>Board&nbsp;game?</td>";
print "<td>\n";
print "<input type=\"checkbox\" name=\"boardgame\" " . ($boardgame ? "checked=\"checked\"" : "") . "/>\n";
print "</td>\n";
print "</tr>\n\n";


### System? ###

print "<tr><td>RPG System</td>";
print "<td>\n";
print "<select name=\"gamesystem_id\" id=\"gamesystem_id\">\n";

foreach ($gamesystem AS $id => $name) {
	$selected = ($id == $gamesystem_id ? "selected" : "");
	print "<option value=\"$id\" $selected>" . htmlspecialchars($name) . "</option>" . PHP_EOL;
}
print "</select>\n";
print '(<span onclick="document.getElementById(\'gamesystem_id\').value=73; return false;" title="Set system to LARP" style="text-decoration-line: underline; text-decoration-style: dashed; cursor: pointer;">← LARP</span>) ';
print "- possible note: <input type=text name=gamesystem_extra value=\"".htmlspecialchars($gamesystem_extra)."\" size=30>";
print "</td>";
print "</tr>\n\n";

### persons ###
print '
	<tr valign="top">
		<td>
			By <span accesskey="+" title="Hotkey: +" class="addnext atoggle">➕</span>
		</td>
		<td colspan="2">
			<table border="0" id="persontable">
';
$acount = 0;
if ($game) {
	foreach($qrel AS $row) {
		$acount++;
		print '<tr data-personid="' . $acount . '"><td>';
		print '<input class="personlookup personexists" type="text" name="person[' . $acount . '][name]" value="' . $row['id'] . ' - ' . htmlspecialchars( $row['name'] ) . '" placeholder="Name">';
		print '</td><td>';
		print titleoptions( $titles, $acount, $row['titid'] );		
#		print '<input type="text" name="person[' . $acount . '][title]" value="' . $row['titid'] . ' - ' . htmlspecialchars( $row['title'] ) . '" placeholder="Title">';
		print '</td><td>';
		print '<input type="text" name="person[' . $acount . '][note]" value="' . htmlspecialchars( $row['note'] ) . '" placeholder="Optional note">';
		print '</td><td>';
		print '<span class="atoggle" onclick="disabletoggle(' . $acount . ');">🗑️</span>';
		print '<span title="Add new person" class="atoggle glow" onclick="addperson(' . $acount . ');"> 👤</span>';
		print '</td></tr>' . PHP_EOL;
	}
}

print '		
			</table>
		</td>
	</tr>
';


tr("Optional organizer","person_extra",$person_extra);

### List of cons: ###

print '
	<tr valign="top">
		<td>
			Con
		</td>
		<td colspan="2">
			<table border="0">
				<tr>
					<td>
						<select name="con[]" id="con" multiple size="7" class="personselect">
';

if ($game) {
        foreach($qcrel AS $row) {
		print "<option value=\"{$row['preid']}_{$row['id']}\">{$row['name']} ({$row['year']}) ({$row['event']})</option>\n";
	}
}

print '
						</select>
					</td>
					<td>
						<input type="button" class="flytknap" value="← Premiere" onClick="addtocon(m4,1)" title="Premiere (first official run)"><br>
						<input type="button" class="flytknap" value="← Re-run" onClick="addtocon(m4,2)" title="Re-run (about same version as original)"><br>
						<input type="button" class="flytknap" value="← Re-run (mod.)" onClick="addtocon(m4,3)" title="Re-run (modified from original)"><br>
						<input type="button" class="flytknap" value="← Test run" onClick="addtocon(m4,42)" title="Test run (announced but officially scheduled for another con)"><br>
						<input type="button" class="flytknap" value="← Cancelled" onClick="addtocon(m4,99)" title="Cancelled (was annunced in the programme but was not run)"><br>
						<input type="button" class="flytknapright" value="Remove →" onClick="removefrom(m4)" ><br>
					</td>

					<td>
						<select name="bigselectcon" id="bigselectcon" multiple size="7">
';
if ($conlock) { // default con
	print "<option value=\"$conlock\" ondblclick=\"addtocon(m4,1);\" selected>{$con[$conlock]}\n";
	print "<option value=\"\">-----------\n";
}

if($game && $qcrel && count($qcrel) > 0) {
        foreach($qcrel AS $row) {
		print "<option value=\"{$row['id']}\">{$row['name']} ({$row['year']})</option>\n";
	}
	if (count($qcrel) > 0) {
		print "<option value=\"\">-----------\n";
	}
}

foreach ($con AS $conid => $conname) {
	print "<option value=\"$conid\" ondblclick=\"addtocon(m4,1);\">$conname\n";
}
print '
						</select>
					</td>

				</tr>

			</table>
		</td>
	</tr>
';


print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($game ? "Update" : "Create").' game">' . ($game ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete game?\n\nAs a safety mecanism it will be checked if all references are removed.\');" class="delete">' : '') . '</td></tr>';

$this_type = $this_type_old;
if ($game) {
	print changetags($game,$this_type);
	print changelinks($game,$this_type);
	print changetrivia($game,$this_type);
	print changealias($game,$this_type);
	print changegenre($game,$this_type);
	print changerun($game,$this_type);
	print changefiles($game,$this_type);
	print changeuserlog($game,$this_type);
	print showpicture($game,$this_type);
	print showtickets($game,$this_type);
	$articles = getall("
		SELECT i.id AS iid, m.id AS mid, i.title, m.name
		FROM article a
		INNER JOIN issue i ON a.issue_id = i.id
		INNER JOIN magazine m ON i.magazine_id = m.id
		WHERE a.game_id = $game
		ORDER BY i.releasedate, a.id
		
	");
	print "<tr><td>Articles:</td><td>";
	if ( $articles ) {
		foreach ($articles AS $article) {
			print '<a href="magazine.php?magazine_id=' . $article['mid'] . '&issue_id='. $article['iid'] . '">' . htmlspecialchars($article['title'] . ' - ' . $article['name']) . '</a><br>';
		}
	} else {
		print 'None';
	}
	print "</td></tr>";

	print "<tr><td>Reviews:</td><td>";
	if ( $reviews ?? FALSE ) {
		foreach ($reviews AS $rid => $title ) {
			print '<a href="review.php?review_id=' . $rid . '">' . ( $title !== "" ? htmlspecialchars( $title ) : "(unknown)" ) . '</a><br>';
		}
	} else {
		print "None";
	}
	print "</td></tr>";
}
?>

</table>

<script type="text/javascript">
	document.write('<input type="hidden" name="jsenabled" value="1">');
</script>

</form>

<script type="text/javascript">
var m3 = document.theForm.bigselectcon;
var m4 = document.theForm.con;


$("#title").change(function() {
	$.get( "lookup.php", { type: 'sce', label: $("#title").val() } , function( data ) {
		if (data > 0) {
			$("#titlenote").text("⚠ Note: A game with the same title already exists. You can still submit this new game.");
		} else {
			$("#titlenote").text("");
		}
	});
});

$(".personlookup").change( checkperson );

function checkperson( dom ) {
	var input = $( dom.target );
	input.removeClass("personexists personunsure persondoesnotexist")
	var val = input.val();
	if ( val != '' ) {
		var personId = parseInt(val);
		var foundperson = availableNames.filter(s => s.startsWith(personId + " - ") )[0];
		if ( foundperson ) {
			input.val( foundperson );
			input.addClass("personexists");
		} else {
			input.addClass("personunsure");
			return false;
		}
	}

}

function disabletoggle( personid ) {
	dodelete = ! $("tr [data-personid=" + personid + "]").prop("dodelete");
	$("tr [data-personid=" + personid + "] input").prop("disabled", dodelete);
	$("tr [data-personid=" + personid + "] select").prop("disabled", dodelete);
	$("tr [data-personid=" + personid + "]").prop("dodelete", dodelete);
}

function addperson( personid ) {
	var input = $("tr [data-personid=" + personid + "] input:first");
	var newpersonname = input.val();
	$.getJSON( "lookup.php", { type: 'addperson', label: newpersonname }, function( data ) {
		if ( data.error == false ) {
			if ( data.new == true ) {
				var newlabel = data.id + ' - ' + newpersonname;
				availableNames.push( newlabel );
			}
			var inputlabel = availableNames.filter(s => s.startsWith(data.id + " - ") )[0];
			input.val( inputlabel );
			input.removeClass("persondoesnotexist personunsure").addClass("personexists");
		} else {
			input.removeClass("personexists personunsure").addClass("persondoesnotexist");
		}
	});
}

</script>


</body>
</html>
