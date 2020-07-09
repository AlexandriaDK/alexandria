<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$this_type = 'game';

/*
 * Ny løsning pr. 22. oktober 2002 - kræver javascript, men outputtet fylder kun 1 gang con-liste
 * og 1 gang forfatter-liste
 */

function strSplitParticipants($str) {
	if (!preg_match('/^\d+(-\d+)?$/',$str) ) {
		return [ NULL, NULL ];
	}
	list ($str_min, $str_max) = explode("-", $str);
	if (!$str_max) {
		$str_max = $str_min;
	} elseif ($str_min > $str_max) {
		$str_tmp = $str_min;
		$str_min = $str_max;
		$str_max = $str_tmp;
	}
	return [ $str_min, $str_max ];
}

$action = $_REQUEST['action'];
$jsenabled = $_REQUEST['jsenabled'];

if ( $action ) {
	validatetoken( $token );
}

$game = (int) $_REQUEST['game'];
$title = $_REQUEST['title'];
$description = $_REQUEST['description'];
$descriptions = (array) $_REQUEST['descriptions'];
if (!$descriptions) {
	$descriptions = [1 => [ 'id' => 1, 'language' => ($_COOKIE['langlock'] ? $_COOKIE['langlock'] : 'da'), 'description' => '', 'note' => '' ] ];
}
$intern = $_REQUEST['intern'];
$sys_id = (int) $_REQUEST['sys_id'];
$sys_ext = $_REQUEST['sys_ext'];
$aut = $_REQUEST['aut'];
$aut_extra = $_REQUEST['aut_extra'];
$con = $_REQUEST['con'];
$boardgame = (int) (bool) $_REQUEST['boardgame'];
$person = (array) $_REQUEST['person'] ?? [];

$gms = $_REQUEST['gms'];
$players = $_REQUEST['players'];
list ($gms_min, $gms_max) = strSplitParticipants($gms);
list ($players_min, $players_max) = strSplitParticipants($players);

$participants_extra = $_REQUEST['participants_extra'];

if (!$action && $game) {
	$row = getrow("SELECT * FROM sce WHERE id = '$game'");
	if ($row) {
		$title = $row['title'];
		$description = $row['description'];
		$descriptions = getall("SELECT id, description, language, note FROM game_description WHERE game_id = $game ORDER BY priority, language");
		if (!$descriptions) {
			$descriptions = [1 => [ 'id' => 1, 'language' => 'da', 'description' => '', 'note' => '' ] ];
		}
		$intern = $row['intern'];
		$sys_id = $row['sys_id'];
		$sys_ext = $row['sys_ext'];
		$aut_extra = $row['aut_extra'];
		$gms_min = $row['gms_min'];
		$gms_max = $row['gms_max'];
		$players_min = $row['players_min'];
		$players_max = $row['players_max'];
		$participants_extra = $row['participants_extra'];
		$boardgame = $row['boardgame'];

		$gms = ($gms_max != $gms_min ? $gms_min . "-" . $gms_max : $gms_min);
		$players = ($players_max != $players_min ? $players_min . "-" . $players_max : $players_min);
		
	} else {
		unset($game);
	}
}

//
// Edit game
//

if ($action == "update" && $game) {
	if (!$title) {
		$_SESSION['admin']['info'] = "You are missing a title!";
	} else {
		$title = trim($title);
		$q = "UPDATE sce SET " .
		     "title = '".dbesc($title)."', " .
		     "sys_id = $sys_id, " .
		     "sys_ext = '".dbesc($sys_ext)."', " .
		     "aut_extra = '".dbesc($aut_extra)."', " .
		     "intern = '".dbesc($intern)."', " .
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
					$inserts[] = "($game, '" . dbesc($d['description']) . "', '" . dbesc($d['language']) . "','" . dbesc($d['note']) . "')";
				}
			}
			if ($inserts) {
				$sql = "INSERT INTO game_description (game_id, description, language, note) VALUES " . implode(",", $inserts);
				$r = doquery($sql);
			}

			$q = "DELETE FROM asrel WHERE sce_id = '$game'";
			$r = doquery($q);
			foreach( $person AS $autdata) {

				$aut_id = (int) $autdata['name'];
				$tit_id = (int) $autdata['title'];
				$note = (string) $autdata['note'];
				if ($tit_id && $aut_id) {
					$q = "INSERT INTO asrel (sce_id, aut_id, tit_id, note) ".
					     "VALUES ($game, $aut_id, $tit_id, '" . dbesc( $note ) ."')";
					$r = doquery($q);
					print dberror();
				}
			}
	
		}
		print dberror();
		if ($r) {
			chlog($game,$this_type,"Scenarie rettet");
		}

// Relation-systemet virker kun, hvis javascript er enabled:
		if ($jsenabled == "1") {
	
	// Add person-game relations
	// Add game-con relations
	
			$q = "DELETE FROM csrel WHERE sce_id = '$game'";
			$r = doquery($q);
			foreach( (array) $con AS $condata) {
				list($pre_id,$con_id) = explode("_",$condata);
				$pre_id = (int) $pre_id;
				$con_id = (int) $con_id;
				if ($pre_id && $con_id) {
					$q = "INSERT INTO csrel (sce_id, convent_id, pre_id) ".
					     "VALUES ($game, $con_id, $pre_id)";
					$r = doquery($q);
					print dberror();
				}
			}
		}

// Færdig!
		$_SESSION['admin']['info'] = "Game updated! " . dberror();
		rexit($this_type, ['game' => $game] );
	}
}

//
// Delete game
//

if ($action == "Delete" && $game) { // should check if game exists
	$error = [];
	if (getCount('asrel', $game, FALSE, 'sce') ) $error[] = "person";
	if (getCount('csrel', $game, FALSE, 'sce') ) $error[] = "con";
	if (getCount('gsrel', $game, FALSE, 'sce') ) $error[] = "genre";
	if (getCount('scerun', $game, FALSE, 'sce') ) $error[] = "run";
	if (getCount('trivia', $game, TRUE, 'sce') ) $error[] = "trivia";
	if (getCount('links', $game, TRUE, 'sce') ) $error[] = "link";
	if (getCount('alias', $game, TRUE, 'sce') ) $error[] = "alias";
	if (getCount('files', $game, TRUE, 'sce') ) $error[] = "file";
	if (getCount('userlog', $game, TRUE, 'sce') ) $error[] = "user log (requires admin)";
	if ($error) {
		$_SESSION['admin']['info'] = "Can't delete. The game still has the following references: " . implode(", ",$error);
		rexit($this_type, ['game' => $game] );
	} else {
		$title = getone("SELECT title FROM sce WHERE id = $game");

		$q = "DELETE FROM sce WHERE id = $game";
		$r = doquery($q);

		if ($r) {
			doquery("DELETE FROM game_description WHERE game_id = $game");
			chlog($game,$this_type,"Scenarie slettet: $title");
		}
		$_SESSION['admin']['info'] = "Game deleted! " . dberror();
		rexit($this_type, ['game' => $game] );
	}
}

//
// Create game
//

if ($action == "create") {
	if (!$title) {
		$info = "Title is missingl!";
	} else {
		$title = trim($title);
		$q = "INSERT INTO sce (id, title, description, intern, sys_id, sys_ext, aut_extra, gms_min, gms_max, players_min, players_max, participants_extra, boardgame) " .
		     "VALUES (NULL, '".dbesc($title)."', '".dbesc($description)."', '".dbesc($intern)."', '".dbesc($sys_id)."', '".dbesc($sys_ext)."', '".dbesc($aut_extra)."', " . strNullEscape($gms_min) . ", " . strNullEscape($gms_max) . ", " . strNullEscape($players_min) . ", " . strNullEscape($players_max) . ", '" . dbesc($participants_extra) . "', $boardgame)";
		$r = doquery($q);
		if ($r) {
			$game = dbid();
			$inserts = [];
			foreach($descriptions AS $d) {
				if ($d['description'] !== "") {
					$inserts[] = "($game, '" . dbesc($d['description']) . "', '" . dbesc($d['language']) . "','" . dbesc($d['note']) . "')";
				}
			}
			$sql = "INSERT INTO game_description (game_id, description, language, note) VALUES " . implode(",", $inserts);
			$r = doquery($sql);
			chlog($game,$this_type,"Scenarie oprettet");
		}
		$_SESSION['admin']['info'] = "Game created! " . dberror();

// Add person-game relations
		foreach( $person AS $autdata) {

			$aut_id = (int) $autdata['name'];
			$tit_id = (int) $autdata['title'];
			$note = (string) $autdata['note'];
			if ($tit_id && $aut_id) {
				$q = "INSERT INTO asrel (sce_id, aut_id, tit_id, note) ".
				     "VALUES ($game, $aut_id, $tit_id, '" . dbesc( $note ) ."')";
				$r = doquery($q);
				print dberror();
			}
		}

// Relation system for cons only works if javascript is enabled
		if ($jsenabled == "1") {
	
	// Add scenario-con relations
			foreach( (array) $con AS $condata) {
				unset($pre_id,$con_id);
				list($pre_id,$con_id) = explode("_",$condata);
				if ($pre_id && $con_id) {
					$q = "INSERT INTO csrel (sce_id, convent_id, pre_id) ".
					     "VALUES ('$game', '$con_id', '$pre_id')";
					$r = doquery($q);
					print dberror();
				}
			}
		}

// Done!
	rexit($this_type, ['game' => $game] );
	}
}

# Find existing game persons

if ($game) {
	$qrel = getall("
	SELECT asrel.id AS relid, aut.id, CONCAT(aut.firstname,' ',aut.surname) AS name, asrel.note, asrel.tit_id AS titid, title.title
	FROM asrel
	INNER JOIN aut ON asrel.aut_id = aut.id
	LEFT JOIN title ON asrel.tit_id = title.id
	WHERE asrel.sce_id = $game
	ORDER BY title.priority, asrel.tit_id, aut.surname, aut.firstname
");
	print dberror();
}

# Find eksisterende con-tilknytninger

if ($game) {
	$qcrel = getall("
		SELECT
			csrel.id AS relid,
			convent.id,
			convent.name,
			convent.year,
			convent.begin,
			convent.end,
			pre.id AS preid,
			pre.event,
			conset.name AS setname
		FROM
			(csrel,
			convent,
			pre)
		LEFT JOIN
			conset ON convent.conset_id = conset.id
		WHERE
			csrel.sce_id='$game' AND
			csrel.convent_id = convent.id AND
			csrel.pre_id = pre.id
		ORDER BY
			pre.id,
			convent.year,
			convent.begin,
			convent.end,
			setname,
			convent.name
	");
	print dberror();
}

// Find alle con'er

$con = [];
#$con[0] = "[ingen eller ukendt con]";
$q = getall("SELECT convent.id, convent.name, year, conset.name AS setname FROM convent LEFT JOIN conset ON convent.conset_id = conset.id ORDER BY setname, year, begin, end, name") OR die(dberror());
foreach($q AS $r) {
	$con[$r['id']] = $r['name']." (".$r['year'].")";
}

// Find alle systemer

$sys = [];
$sys[0] = "[unknown or unspecified RPG system]";
$q = getall("SELECT id, name FROM sys ORDER BY name");
foreach($q AS $r) {
	$sys[$r['id']] = $r['name'];
}


// Find alle personer

// Nyeste x personer først
$autnew = [];
$q = getall("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY id DESC LIMIT 10");
foreach($q AS $r) {
	$autnew[$r['id']] = $r['name'];
}

$aut = [];
$q = getall("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY name, id");
foreach($q AS $r) {
	$aut[$r['id']] = $r['name'];
}

// Find alle arbejdstitler

unset($tit);
$q = getall("SELECT id, title FROM title ORDER BY id");
foreach($q AS $r) {
	$tit[$r['id']] = $r['title'];
}

// Find alle slags events

unset($pre);
$q = getall("SELECT id, event FROM pre ORDER BY id");
foreach($q AS $r) {
	$pre[$r['id']] = $r['event'];
}

$people = [];
$r = getall("SELECT id, firstname, surname FROM aut ORDER BY firstname, surname");
foreach($r AS $row) {
	$people[] = $row['id'] . " - " . $row['firstname'] . " " . $row['surname'];
}

$titles = getcolid("SELECT id, title FROM title ORDER BY id");
?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - Scenario</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/uistyle.css">
<link rel="icon" type="image/png" href="/gfx/favicon_ti_adm.png">
<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
var availableNames = <?php print json_encode($people); ?>;

/*
$('form').keydown(function(event) {
	console.log("foo");
	if (event.ctrlKey && event.keyCode === 13) {
		console.log("bar");
		$(this).trigger('submit');
	}
})
 */

$(document).on("keydown", ":input:not(textarea):not(:submit)", function(event) {
	if (event.key == "Enter") {
		if ( event.target.id !== 'ffind' ) {
			event.preventDefault();
		}
	}
});

$(function() {
	$( ".personlookup" ).autocomplete({
		source: availableNames,
		delay: 30
	});
	$(".addnext").click( function() {
		var acount = $( "#persontable tr" ).length;
		var newcount = acount + 1;
		var options = '<?php print titleoptions( $titles, 'NEWCOUNT' ); ?>'.replace( 'NEWCOUNT', newcount );
		var dynhtml = '<tr data-personid="' + newcount + '"><td><input class="personlookup" name="person[' + newcount + '][name]" placeholder="Name"></td><td>' + options + '</td><td><input name="person[' + newcount + '][note]" placeholder="Optional note"></td><td><span class="atoggle" onclick="disabletoggle(' + newcount + ');">🗑️</span> <span title="Add new person" class="atoggle glow" onclick="addperson(' + newcount + ');">👤</span></td></tr>';
		var newtr = $( "#persontable" ).append( dynhtml );
		var bar = $( "#persontable" ).find('tr:last input:first')
			.autocomplete({
				source: availableNames,
				delay: 30
			})
			.change( checkperson )
		;
		

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
      tabTemplate = "<li><a href='#{href}' data-id='#{id}' ondblclick='changeLanguage(this)' >#{label}</a></li>",
      tabCounter = countTabs,
      tabContentTemplate = '<input type="hidden" name="descriptions[NUMBER][language]" value="MYLANGUAGE">' +
                           '<input type="hidden" name="descriptions[NUMBER][note]" value="">' +
                           '<textarea name="descriptions[NUMBER][description]" style="width: 100%;" rows=10></textarea>';
 
    tabs = $( "#tabs" ).tabs();
 
    $( "#add_my_tab" )
      .on( "click", function() {
	var language = prompt("Language", "en");
	if (language) {
		tabCounter++;
		var label = language || "Tab " + tabCounter,
			id = "d-" + tabCounter,
			li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ).replace( /#\{id\}/g, tabCounter ) ) ,
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
	dcount = elem.getAttribute( 'data-id' );
	language = elem.innerHTML;
	var language = prompt("Language", language);
	if ( language ) {
		var id = '#ui-id-' + dcount;
		var lid = '#d-' + dcount + ' input:first-child';
		$(id).text( language );
		$(lid).attr( 'value', language )
		
	}
}

</script>
</head>

<body bgcolor="#FFCC99" link="#CC0033" vlink="#990000" text="#000000">

<?php
include("links.inc");

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
print "<tr><td>Description<br><a href='#' id='add_my_tab' accesskey='e'>[+]</a></td><td style=\"width: 100%; margin-top; 0; padding-top: 0;\">";
$dcount = 0;
$lihtml = $inputhtml = '';
foreach($descriptions AS $d) {
	$dcount++;
	$lihtml .= "<li><a href=\"#d-" . $dcount . "\" data-id=\"" . $dcount . "\" ondblclick=\"changeLanguage(this)\">" . htmlspecialchars($d['language']) . ($d['note'] != '' ? " (" . htmlspecialchars($d['note']) . ")" : "") . "</a></li>" . PHP_EOL;
	$inputhtml .= "<div id=\"d-" . $dcount . "\" style=\"padding: 4px;\">" . PHP_EOL;
	$inputhtml .= "<input type=\"hidden\" name=\"descriptions[" . $dcount . "][language]\" value=\"" . htmlspecialchars($d['language']) . "\">" . PHP_EOL;
	$inputhtml .= "<input type=\"hidden\" name=\"descriptions[" . $dcount . "][note]\" value=\"" . htmlspecialchars($d['note']) . "\">" . PHP_EOL;
	$inputhtml .= "<textarea name=\"descriptions[" . $dcount . "][description]\" style=\"width: 100%;\" rows=10>\n" . htmlspecialchars($d['description']) . "</textarea>" . PHP_EOL;
	$inputhtml .= "</div>" . PHP_EOL . PHP_EOL;

}
print "<div id=\"tabs\" style=\"margin-top: 0;\">" . PHP_EOL;
print "<ul>" . $lihtml . "</ul>" . PHP_EOL;
print $inputhtml;
print "</div>";
print "</td></tr>\n";
print "<tr valign=top><td>Internal note</td><td style=\"width: 100%\"><textarea name=intern style=\"width: 100%\" rows=6>\n" . htmlspecialchars($intern) . "</textarea></td></tr>\n";


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

print "<tr valign=top><td>Board&nbsp;game?</td>";
print "<td>\n";
print "<input type=\"checkbox\" name=\"boardgame\" " . ($boardgame ? "checked=\"checked\"" : "") . "/>\n";
print "</td>\n";
print "</tr>\n\n";


### System? ###

print "<tr valign=top><td>RPG System</td>";
print "<td>\n";
print "<select name=\"sys_id\">\n";

foreach ($sys AS $id => $name) {
	$selected = ($id == $sys_id ? "selected" : "");
	print "<option value=\"$id\" $selected>" . htmlspecialchars($name) . "</option>" . PHP_EOL;
}
print "</select>\n";
print "- possible note: <input type=text name=sys_ext value=\"".htmlspecialchars($sys_ext)."\" size=30>";

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


tr("Optional organizer","aut_extra",$aut_extra);

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
						<input type="button" class="flytknap" value="&lt;- Premiere" onClick="addtocon(m4,1)" title="Premiere (first official run)"><br>
						<input type="button" class="flytknap" value="&lt;- Re-run" onClick="addtocon(m4,2)" title="Re-run (about same version as original)"><br>
						<input type="button" class="flytknap" value="&lt;- Re-run (mod.)" onClick="addtocon(m4,3)" title="Re-run (modified from original)"><br>
						<input type="button" class="flytknap" value="&lt;- Test run" onClick="addtocon(m4,42)" title="Test run (announced but officially scheduled for another con)"><br>
						<input type="button" class="flytknap" value="&lt;- Cancelled" onClick="addtocon(m4,99)" title="Cancelled (was annunced in the programme but was not run)"><br>
						<input type="button" class="flytknapright" value="-&gt; Remove" onClick="removefrom(m4)" ><br>
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

$this_type = 'sce';
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
}	

?>

</table>

<script type="text/javascript">
<!--
	document.write('<input type="hidden" name="jsenabled" value="1">');
//-->
</script>

</form>

<script type="text/javascript">
var m3 = document.theForm.bigselectcon;
var m4 = document.theForm.con;


$("#title").change(function() {
	$.get( "lookup.php", { type: 'sce', label: $("#title").val() } , function( data ) {
		if (data > 0) {
			$("#titlenote").text("⚠ Note: A scenario with the same title already exists. You can still submit this new scenario.");
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
