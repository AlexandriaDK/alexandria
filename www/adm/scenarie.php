<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";

$this_type = 'sce';

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

$scenarie = (int) $_REQUEST['scenarie'];
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

$gms = $_REQUEST['gms'];
$players = $_REQUEST['players'];
list ($gms_min, $gms_max) = strSplitParticipants($gms);
list ($players_min, $players_max) = strSplitParticipants($players);

$participants_extra = $_REQUEST['participants_extra'];

if (!$action && $scenarie) {
	$row = getrow("SELECT * FROM sce WHERE id = '$scenarie'");
	if ($row) {
		$title = $row['title'];
		$description = $row['description'];
		$descriptions = getall("SELECT id, description, language, note FROM game_description WHERE game_id = $scenarie ORDER BY priority, language");
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
		unset($scenarie);
	}
}

//
// Ret scenarie
//

if ($action == "ret" && $scenarie) {
	print "<pre>";
	if (!$title) {
		$_SESSION['admin']['info'] = "Du mangler en titel!";
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
		     "WHERE id = $scenarie";
		$r = doquery($q);
		if ($r) {
			doquery("DELETE FROM game_description WHERE game_id = $scenarie");
			$inserts = [];
			foreach($descriptions AS $d) {
				if ($d['description'] !== "") {
					$inserts[] = "($scenarie, '" . dbesc($d['description']) . "', '" . dbesc($d['language']) . "','" . dbesc($d['note']) . "')";
				}
			}
			$sql = "INSERT INTO game_description (game_id, description, language, note) VALUES " . implode(",", $inserts);
			$r = doquery($sql);
		}
		print dberror();
		if ($r) {
			chlog($scenarie,$this_type,"Scenarie rettet");
		}

// Relation-systemet virker kun, hvis javascript er enabled:
		if ($jsenabled == "1") {
	
	// Tilføj person-scenarie-relationer
			$q = "DELETE FROM asrel WHERE sce_id = '$scenarie'";
			$r = doquery($q);
			foreach( (array) $aut AS $autdata) {
				unset($tit_id,$aut_id);
				list($tit_id,$aut_id) = explode("_",$autdata);
				if ($tit_id && $aut_id) {
					$q = "INSERT INTO asrel (sce_id, aut_id, tit_id) ".
					     "VALUES ('$scenarie', '$aut_id', '$tit_id')";
					$r = doquery($q);
					print dberror();
				}
			}
	
	// Tilføj scenarie-con-relationer
	
			$q = "DELETE FROM csrel WHERE sce_id = '$scenarie'";
			$r = doquery($q);
			foreach( (array) $con AS $condata) {
				unset($pre_id,$con_id);
				list($pre_id,$con_id) = explode("_",$condata);
				if ($pre_id && $con_id) {
					$q = "INSERT INTO csrel (sce_id, convent_id, pre_id) ".
					     "VALUES ('$scenarie', '$con_id', '$pre_id')";
					$r = doquery($q);
					print dberror();
				}
			}
		}

// Færdig!
		$_SESSION['admin']['info'] = "Scenarie rettet! " . dberror();
		rexit($this_type, ['scenarie' => $scenarie] );
	}
}

//
// Slet scenarie
//

if ($action == "Slet" && $scenarie) { // burde tjekke om scenarie findes
	$error = [];
	if (getCount('asrel', $scenarie, FALSE, 'sce') ) $error[] = "person";
	if (getCount('csrel', $scenarie, FALSE, 'sce') ) $error[] = "kongres";
	if (getCount('gsrel', $scenarie, FALSE, 'sce') ) $error[] = "genre";
	if (getCount('scerun', $scenarie, FALSE, 'sce') ) $error[] = "afvikling";
	if (getCount('trivia', $scenarie, TRUE, 'sce') ) $error[] = "trivia";
	if (getCount('links', $scenarie, TRUE, 'sce') ) $error[] = "link";
	if (getCount('alias', $scenarie, TRUE, 'sce') ) $error[] = "alias";
	if (getCount('files', $scenarie, TRUE, 'sce') ) $error[] = "fil";
	if (getCount('userlog', $scenarie, TRUE, 'sce') ) $error[] = "brugerlog (kræver admin)";
	if ($error) {
		$_SESSION['admin']['info'] = "Kan ikke slette. Scenariet har stadigvæk tilknytninger: " . implode(", ",$error);
		rexit($this_type, ['scenarie' => $scenarie] );
	} else {
		$title = getone("SELECT title FROM sce WHERE id = $scenarie");

		$q = "DELETE FROM sce WHERE id = $scenarie";
		$r = doquery($q);

		if ($r) {
			doquery("DELETE FROM game_description WHERE game_id = $scenarie");
			chlog($scenarie,$this_type,"Scenarie slettet: $title");
		}
		$_SESSION['admin']['info'] = "Scenarie slettet! " . dberror();
		rexit($this_type, ['scenarie' => $scenarie] );
	}
}

//
// Opret scenarie
//

if ($action == "opret") {
	if (!$title) {
		$info = "Du mangler en titel!";
	} else {
		$title = trim($title);
		$q = "INSERT INTO sce (id, title, description, intern, sys_id, sys_ext, aut_extra, gms_min, gms_max, players_min, players_max, participants_extra, boardgame) " .
		     "VALUES (NULL, '".dbesc($title)."', '".dbesc($description)."', '".dbesc($intern)."', '".dbesc($sys_id)."', '".dbesc($sys_ext)."', '".dbesc($aut_extra)."', " . strNullEscape($gms_min) . ", " . strNullEscape($gms_max) . ", " . strNullEscape($players_min) . ", " . strNullEscape($players_max) . ", '" . dbesc($participants_extra) . "', $boardgame)";
		$r = doquery($q);
		if ($r) {
			$scenarie = dbid();
			$inserts = [];
			foreach($descriptions AS $d) {
				if ($d['description'] !== "") {
					$inserts[] = "($scenarie, '" . dbesc($d['description']) . "', '" . dbesc($d['language']) . "','" . dbesc($d['note']) . "')";
				}
			}
			$sql = "INSERT INTO game_description (game_id, description, language, note) VALUES " . implode(",", $inserts);
			$r = doquery($sql);
			chlog($scenarie,$this_type,"Scenarie oprettet");
		}
		$_SESSION['admin']['info'] = "Scenarie oprettet! " . dberror();

// Tilføj person-scenarie-relationer

// Relation-systemet virker kun, hvis javascript er enabled:
		if ($jsenabled == "1") {
	
	// Tilføj person-scenarie-relationer
			$q = "DELETE FROM asrel WHERE sce_id = '$scenarie'";
			$r = doquery($q);
			foreach( (array) $aut AS $autdata) {
				unset($tit_id,$aut_id);
				list($tit_id,$aut_id) = explode("_",$autdata);
				if ($tit_id && $aut_id) {
					$q = "INSERT INTO asrel (sce_id, aut_id, tit_id) ".
					     "VALUES ('$scenarie', '$aut_id', '$tit_id')";
					$r = doquery($q);
					print dberror();
				}
			}
	
	// Tilføj scenarie-con-relationer
	
			$q = "DELETE FROM csrel WHERE sce_id = '$scenarie'";
			$r = doquery($q);
			foreach( (array) $con AS $condata) {
				unset($pre_id,$con_id);
				list($pre_id,$con_id) = explode("_",$condata);
				if ($pre_id && $con_id) {
					$q = "INSERT INTO csrel (sce_id, convent_id, pre_id) ".
					     "VALUES ('$scenarie', '$con_id', '$pre_id')";
					$r = doquery($q);
					print dberror();
				}
			}
		}

// Færdig!
	rexit($this_type, ['scenarie' => $scenarie] );
	}
}

# Find eksisterende scenarie-medarbejdere

if ($scenarie) {
	$qrel = getall("SELECT asrel.id AS relid, aut.id, CONCAT(aut.firstname,' ',aut.surname) AS name, title.id AS titid, title.title FROM asrel, aut, title WHERE asrel.sce_id='$scenarie' AND asrel.aut_id = aut.id AND asrel.tit_id = title.id ORDER BY title.id, name");
	print dberror();
}

# Find eksisterende con-tilknytninger

if ($scenarie) {
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
			csrel.sce_id='$scenarie' AND
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
	$con[$r[id]] = $r[name]." (".$r[year].")";
}

// Find alle systemer

$sys = [];
$sys[0] = "[ukendt eller uspecificeret system]";
$q = getall("SELECT id, name FROM sys ORDER BY name");
foreach($q AS $r) {
	$sys[$r[id]] = $r[name];
}


// Find alle personer

$autnew = [];
$q = getall("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY id DESC LIMIT 5");
foreach($q AS $r) {
	$autnew[$r[id]] = $r[name];
}

$aut = [];
$q = getall("SELECT id, CONCAT(firstname,' ',surname) AS name FROM aut ORDER BY name, id");
foreach($q AS $r) {
	$aut[$r[id]] = $r[name];
}

// Find alle arbejdstitler

unset($tit);
$q = getall("SELECT id, title FROM title ORDER BY id");
foreach($q AS $r) {
	$tit[$r[id]] = $r[title];
}

// Find alle slags events

unset($pre);
$q = getall("SELECT id, event FROM pre ORDER BY id");
foreach($q AS $r) {
	$pre[$r[id]] = $r[event];
}

?>
<!DOCTYPE html>
<HTML><HEAD><TITLE>Administration - scenarie</TITLE>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
  #dialog label, #dialog input { display:block; }
  #dialog label { margin-top: 0.5em; }
  #dialog input, #dialog textarea { width: 95%; }
  #tabs { margin-top: 1em; }
  #tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
  #add_tab { cursor: pointer; }
</style>
<script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
function removefrom(mm) {
	mmlen = mm.length ;
	for ( i=(mmlen-1); i>=0; i--) {
		if (mm.options[i].selected == true ) {
			mm.options[i] = null;
		}
	}
}

function addto(mm,jobtype) {
	m1len = m1.length;
	if (jobtype == 1) {
		prefix = '1_';
		suffix = '(Forfatter)';
	} else if (jobtype == 2) {
		prefix = '2_';
		suffix = '(Illustrator)';
	} else if (jobtype == 3) {
		prefix = '3_';
		suffix = '(Layouter)';
	} else if (jobtype == 4) {
		prefix = '4_';
		suffix = '(Arrangør)';
	} else if (jobtype == 5) {
		prefix = '5_';
		suffix = '(Designer)';
	}

	for ( i=0; i<m1len ; i++){
		if (m1.options[i].selected == true ) {
			mmlen = mm.length;
			mm.options[mmlen]= new Option(m1.options[i].text+' '+suffix, prefix+m1.options[i].value);
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
	} else if (contype == 99) {
		prefix = '99_';
		suffix = '(Aflyst)';
	}
	for ( i=0; i<m3len ; i++){
		if (m3.options[i].selected == true ) {
			mmlen = mm.length;
			mm.options[mmlen]= new Option(m3.options[i].text+' '+suffix, prefix+m3.options[i].value);
		}
	}
}

function doSubmit() {
	for (i=0;i<m1.length;i++) {
		m1.options[i].selected = false;
	}
	for (i=0;i<m2.length;i++) {
		m2.options[i].selected = true;
	}
	for (i=0;i<m3.length;i++) {
		m3.options[i].selected = false;
	}
	for (i=0;i<m4.length;i++) {
		m4.options[i].selected = true;
	}
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
	var language = prompt("Sprog", "en");
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
	var language = prompt("Sprog", language);
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


print "<form action=\"scenarie.php\" method=\"post\" name=\"theForm\" onsubmit=\"doSubmit();\">\n";
if (!$scenarie) print "<input type=\"hidden\" name=\"action\" value=\"opret\">\n";
else {
	print "<input type=\"hidden\" name=\"action\" value=\"ret\">\n";
	print "<input type=\"hidden\" name=\"scenarie\" value=\"$scenarie\">\n";
}

print "<a href=\"./scenarie.php\">Nyt scenarie</a>";

print "<table border=\"0\">\n";

if ($scenarie) {
	print "<tr><td>ID:</td><td>$scenarie - <a href=\"../data?scenarie=$scenarie\" accesskey=\"q\">Vis scenarieside</a>";
	if ($viewlog == TRUE) {
		print " - <a href=\"showlog.php?category=$this_type&amp;data_id=$scenarie\">Vis log</a>";
	}
	print "\n</td></tr>\n";
}

print "<tr><td>Titel:</td><td><input type=text name=\"title\" id=\"title\" value=\"" . htmlspecialchars($title) . "\" size=50> <span id=\"titlenote\"></span></td></tr>\n";
# tr("Titel:","title",$title);
#print "<tr><td>Foromtale:<br><button id=\"add_tab\">+</button></td><td style=\"width: 100%\">";
print "<tr><td>Foromtale:<br><a href='#' id='add_my_tab' accesskey='e'>[+]</a></td><td style=\"width: 100%; margin-top; 0; padding-top: 0;\">";
$dcount = 0;
$lihtml = $inputhtml = '';
foreach($descriptions AS $d) {
	$dcount++;
	$lihtml .= "<li><a href=\"#d-" . $dcount . "\" data-id=\"" . $dcount . "\" ondblclick=\"changeLanguage(this)\">" . htmlspecialchars($d['language']) . ($d['note'] != '' ? " (" . htmlspecialchars($d['note']) . ")" : "") . "</a></li>" . PHP_EOL;
	$inputhtml .= "<div id=\"d-" . $dcount . "\">" . PHP_EOL;
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
print "<tr valign=top><td>Intern note:</td><td style=\"width: 100%\"><textarea name=intern style=\"width: 100%\" rows=6>\n" . htmlspecialchars($intern) . "</textarea></td></tr>\n";


### Deltagere ###

print "<tr valign=top><td>Deltagere:</td>";
print "<td>\n";
print "GM's: <input type=\"text\" name=\"gms\" value=\"" . $gms . "\" size=\"2\" \> - ";
print "Spillere: <input type=\"text\" name=\"players\" value=\"" . $players . "\" size=\"2\" \> - ";
print "evt. yderligere detaljer : <input type=text name=\"participants_extra\" value=\"".htmlspecialchars($participants_extra)."\" size=30>\n";
print "<br /><span style=\"font-size: 0.8em;\">Mulighed for variabelt antal spillere: Angiv fx <i>4-6</i> for 4 til 6 spillere</span>\n";
print "</td>";
print "</tr>\n\n";

### Brætspil? ###

print "<tr valign=top><td>Brætspil?</td>";
print "<td>\n";
print "<input type=\"checkbox\" name=\"boardgame\" " . ($boardgame ? "checked=\"checked\"" : "") . "/>\n";


### System? ###

print "<tr valign=top><td>System:</td>";
print "<td>\n";
print "<select name=\"sys_id\">\n";

foreach ($sys AS $id => $name) {
	print "<option value=$id";
	if ($id == $sys_id) print " selected";
	print ">$name\n";
}
print "</select>\n";
print "- evt. systemnote: <input type=text name=sys_ext value=\"".htmlspecialchars($sys_ext)."\" size=30>";

print "</td>";


print "</tr>\n\n";


### Liste over cons: ###

print '
	<tr valign="top">
		<td>
			Con:
		</td>
		<td colspan="2">
			<table border="0">
				<tr>
					<td>
						<select name="con[]" id="con" multiple size="7" class="personselect">
';

if ($scenarie) {
        foreach($qcrel AS $row) {
		print "<option value=\"{$row['preid']}_{$row['id']}\">{$row['name']} ({$row['year']}) ({$row['event']})</option>\n";
	}
}

print '
						</select>
					</td>
					<td>
						<input type="button" class="flytknap" value="&lt;- Premiere" onClick="addtocon(m4,1)" title="Premiere (første offentlige afvikling)"><br>
						<input type="button" class="flytknap" value="&lt;- Re-run" onClick="addtocon(m4,2)" title="Rerun (omtrent samme version som oprindelig udgave)"><br>
						<input type="button" class="flytknap" value="&lt;- Re-run (mod.)" onClick="addtocon(m4,3)" title="Re-run (modificeret siden oprindelig udgave)"><br>
						<input type="button" class="flytknap" value="&lt;- Aflyst" onClick="addtocon(m4,99)" title="Aflyst (var annonceret i programmet, men blev ikke kørt)"><br>
						<input type="button" class="flytknapright" value="-&gt; Fjern" onClick="removefrom(m4)" ><br>
					</td>

					<td>
						<select name="bigselectcon" id="bigselectcon" multiple size="7">
';
if ($conlock) { // default con
	print "<option value=\"$conlock\" ondblclick=\"addtocon(m4,1);\" selected>{$con[$conlock]}\n";
	print "<option value=\"\">-----------\n";
}

if($scenarie && $qcrel && count($qcrel) > 0) {
#	mysql_data_seek($qcrel,0); // allerede valgte cons
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


### Liste over navne: ###

print '
	<tr valign="top">
		<td>
			Af:
		</td>
		<td colspan="2">
			<table border="0">
				<tr>
					<td>
						<select name="aut[]" id="aut" multiple size="13" class="personselect">
';
if ($scenarie) {
        foreach($qrel AS $row) {
		print "<option value=\"{$row['titid']}_{$row['id']}\">{$row['name']} ({$row['title']})</option>\n";
	}
}
print '
						</select>
					</td>
					<td>
						<input type="button" class="flytknap" value="&lt;- Forfatter" onClick="addto(m2,1)" ><br>
						<input type="button" class="flytknap" value="&lt;- Illustrator" onClick="addto(m2,2)"><br>
						<input type="button" class="flytknap" value="&lt;- Layouter" onClick="addto(m2,3)" ><br>
						<input type="button" class="flytknap" value="&lt;- Arrangør" onClick="addto(m2,4)" ><br>
						<input type="button" class="flytknap" value="&lt;- Designer" onClick="addto(m2,5)" ><br>
						<input type="button" class="flytknapright" value="-&gt; Fjern" onClick="removefrom(m2)" ><br>
					</td>

					<td>
						<select name="bigselectaut" id="bigselectaut" multiple size="13">
';
print "<option value=\"\">--- newest five persons ---\n";
foreach($autnew AS $id =>$name) {
	print "<option value=\"$id\" ondblclick=\"addto(m2,1);\">" . htmlspecialchars($name) . "\n";
}
print "<option value=\"\">-----------\n";
while (list($id, $name) = each($aut)) {
	print "<option value=\"$id\" ondblclick=\"addto(m2,1);\">" . htmlspecialchars($name) . "\n";
}
print '
						</select>
					</td>

				</tr>

			</table>
		</td>
	</tr>
';

tr("Evt. arrangør:","aut_extra",$aut_extra);

#$ror = ($scenarie) ? "Ret" : "Opret";

print '<tr><td>&nbsp;</td><td><input type="submit" value="'.($scenarie ? "Ret" : "Opret").' scenarie">' . ($scenarie ? ' <input type="submit" name="action" value="Slet" onclick="return confirm(\'Slet scenarie?\n\nFor en sikkerheds skyld tjekkes der, om alle tilknytninger er fjernet.\');" class="delete">' : '') . '</td></tr>';

if ($scenarie) {
	// Mulighed for at rette links
	print changetags($scenarie,$this_type);

	// Mulighed for at rette links
	print changelinks($scenarie,$this_type);
	
	// Mulighed for at rette trivia
	print changetrivia($scenarie,$this_type);

	// Mulighed for at rette alias
	print changealias($scenarie,$this_type);

	// Mulighed for at rette genrer
	print changegenre($scenarie,$this_type);

	// Mulighed for at rette afvikling
	print changerun($scenarie,$this_type);

	// Mulighed for at rette filer
	print changefiles($scenarie,$this_type);

	// Hvor mange personer har markeret kongressen i deres log?
	print changeuserlog($scenarie,$this_type);

	// Vis evt. billede
	print showpicture($scenarie,$this_type);

	// Vis tickets for scenariet
	print showtickets($scenarie,$this_type);
}	

?>

<script type="text/javascript">
<!--
	document.write('<input type="hidden" name="jsenabled" value="1">');
//-->
</script>

</form>

<script type="text/javascript">
var m1 = document.theForm.bigselectaut;
var m2 = document.theForm.aut;
var m3 = document.theForm.bigselectcon;
var m4 = document.theForm.con;

$("#title").change(function() {
	$.get( "lookup.php", { type: 'sce', label: $("#title").val() } , function( data ) {
		if (data > 0) {
			$("#titlenote").text("⚠ Et scenarie med samme titel findes allerede");
		} else {
			$("#titlenote").text("");
		}
	});
});
</script>


</body>
</html>
