<?php
require "adm.inc";
require "base.inc";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc";
$this_type = 'language';

$admin = $_SESSION['user_admin'];
$langlock = (string) $_COOKIE['langlock'];
$setlang = (string) $_REQUEST['setlang'];
$action = (string) $_REQUEST['action'];
$do = (string) $_REQUEST['do'];
$label = (string) $_REQUEST['label'];
$newlabel = (string) $_REQUEST['newlabel'];
$text = (array) $_REQUEST['text'];
if ( ! $admin ) {
	$newlabel = $label; // only admins can change labels
}

$id = (int) $_REQUEST['id'];

function findintemplates( $string ) {
	$matches = [];
	$filelist = glob("../smarty/templates/generic/*.tpl");
	foreach( $filelist AS $file ) {
		$content = file_get_contents( $file );
		if ( preg_match( '/\$_' . $string . '\b/', $content ) ) {
			$matches[] = basename( $file, '.tpl');
		}
	}
	return $matches;
}

// Sæt sprog?
if ( $setlang ) {
	if ( $setlang == 'none') {
		setcookie( "langlock" );
	} else {
		setcookie( "langlock", $setlang );
	}
	header("Location: language.php?do=next");
	exit;
}

// Ret tekster
if ($action == "update") {
	$old = getcolid("SELECT language, text FROM weblanguages WHERE label = '".  dbesc( $label ) . "'");
	$q = "DELETE FROM weblanguages WHERE label = '" . dbesc( $label ) . "'";
	doquery($q);
	$log = [];
	foreach( $text AS $language => $string ) {
		$string = trim( $string );
		if ( strlen( $string ) > 0 ) {
			if ($string != $old[$language]) {
				if ($old[$language] == '') { //new
					$log[] = '+' . $language;
				} else { // edited
					$log[] = '~' . $language;
				}
			}
			$q = "INSERT INTO weblanguages (label, text, language) VALUES " .
			     "('" . dbesc( $newlabel ) . "','" . dbesc( $string ) . "','" . dbesc( $language )."')";
			doquery($q);
		} elseif ( strlen( $old[$language] ) > 0 ) { // deleted
			$log[] = '-' . $language;
		}
	}
	$logtext = "Tekst rettet: " . $label;
	if ( $log ) {
		$logtext .= " (" . implode(", ", $log) . ")";
	}
	chlog(NULL,$this_type, $logtext);
	$_SESSION['admin']['info'] = "Texts updated! " . dberror();
	rexit($this_type, ['label' => $newlabel] );
}


$overview = [];
$languages = [];
$languagecount = [];
$alltext = getall("SELECT label, text, language FROM weblanguages ORDER BY LOCATE('_', label), label");
foreach ( $alltext AS $text ) {
	$overview[$text['label']][$text['language']] = $text['text'];
	$languagecount[$text['language']]++;
	$languages[$text['language']] = TRUE;
}
ksort( $languages );
$labelcount = count( $overview );

htmladmstart("Translations");

// Edit?
$begin = $nextlabel = FALSE;
foreach( $overview AS $mylabel => $string ) {
	if ( $label == '' ) {
		$begin = TRUE;
	}
	if ($begin == TRUE) {
		foreach ($languages AS $language => $dummy) {
			if ( $langlock && $language != $langlock) {
				continue;
			}
			if ( $string[$language] == '') {
				$nextlabel = $mylabel;
				break;
			}
		}
	}
	if ( $mylabel == $label ) {
		$begin = TRUE;
	}
	if ( $nextlabel != FALSE) {
		break;
	}
}

if ( $do == "next" && $nextlabel != FALSE ) {
	header("Location: language.php?label=" . rawurlencode($nextlabel) );
}

if ( $label ) {
	// find next missing translation
	$matches = findintemplates( $label );
	print "<form action=\"language.php\" method=\"post\">";
	print "<input type=\"hidden\" name=\"label\" value=\"" . htmlspecialchars( $label ) . "\">";
	print "<input type=\"hidden\" name=\"action\" value=\"update\">";
	print "<table>";
	print "<tr><td>Label</td><td><input type=\"text\" name=\"newlabel\" value=\"" . htmlspecialchars( $label ) ."\" " . ( $admin ? "autofocus" : "readonly style=\"background: #ccc\"" ) . " size=\"40\" ></td></tr>";
	print "<tr><td>Used in</td><td>" . ( $matches ? implode(", ", $matches) : "[none]" ) . "</td></tr>";
	foreach ( $languages AS $language => $dummy ) {
		print "<tr><td>" . $language . "</td>";
		print "<td><textarea name=\"text[" . htmlspecialchars( $language ) . "]\" cols=\"100\" " . ( $language == $langlock ? 'class="' . ($langlock == 'de' ? 'langfocusde' : 'langfocus') . '" autofocus' : '') . ">" . htmlspecialchars( $overview[$label][$language] ) . "</textarea></td>";
		print "</tr>";
	}
	print "<tr><td></td><td><input type=\"submit\"></td></tr>";
	print "</table>";
	print "</form>";
} elseif ( $admin ) {
	print "<form action=\"language.php\"><div>New label: <input type=\"text\" name=\"label\" autofocus><input type=\"submit\"></div></form>";
}
if ( $nextlabel != FALSE ) {
	print "<div class=\"nextlanguage\"><a href=\"language.php?label=" . rawurlencode( $nextlabel ) . "\">Go to next label with missing translation</a></div>";
}

// overview
print "<table id=\"translations\"><thead>";
print "<tr><th>Label</th>";
foreach($languages AS $language => $dummy) {
	print "<th><a href=\"language.php?setlang=" . urlencode( $language ) ."\">" . htmlspecialchars( $language ) . "</a> (" . ( floor( $languagecount[$language] / $labelcount * 100 ) ) . "%)</th>";
}
print "</tr></thead><tbody>" . PHP_EOL;
if ( $admin ) {
	print "<tr><td><a href=\"language.php?newlabel=1\">New label</a></td>";
	foreach ( $languages AS $dummy ) {
		print "<td></td>";
	}
	print "</tr>";
}
foreach( $overview AS $label => $string ) {
	print "<tr onclick=\"location.href=this.firstChild.firstChild.href\">";
	print "<td><a href=\"language.php?label=" . rawurlencode( $label ) ."\" id=\"label_" . rawurlencode( $label ) ."\">" . $label . "</a></td>";
	foreach ($languages AS $language => $dummy) {
		print "<td>" . htmlspecialchars( $string[$language] ) . "</td>";
	}
	print "</tr>" . PHP_EOL;
}
print "</tbody></table>";


print "</body>\n</html>\n";

?>
