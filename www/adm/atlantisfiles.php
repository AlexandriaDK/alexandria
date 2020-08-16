<?php
require "adm.inc";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$path = '/home/penguin/web/loot.alexandria.dk/files/Scenariedatabasen/Scenarier/';

$map = [
	'Area51' => 'Area 51',
	'Za Rodinu' => 'Za Rodinu - For Fædrelandet',
	'USS Atlantis' => 'U.S.S. Atlantis',
	'Skyggespor i Helvedets Forgård' => 'Skyggespor i helvedets forgård',
	'Dommedag 2056 - New Orleans' => 'Dommedag 2056: New Orleans',
	'MS Titanic II' => 'M/S Titanic II',
	'New York Coppers' => 'New York Coppers: Gaden uden nåde!',
	'Husk sovepose!' => 'Husk sovepose ...',
	'Sarajevo 2001' => 'Sarajevo-2001',
	'Crossroads' => 'Crossroads (Fastaval 93)',

];

$result = [];

$filelist = glob( $path . '*' );
foreach ( $filelist AS $file ) {
	$sid = NULL;
	$auts = $files = [];
	$scenario = pathinfo( $file )['filename'];
	$basename = pathinfo( $file )['basename'];
	if ($map[$scenario]) {
		$scenario = $map[$scenario];
	}
	$sid = getone( "SELECT id FROM sce WHERE title = '" . dbesc( $scenario ) . "'" );
	if ( $sid ) {
		$auts = getcol( "SELECT CONCAT(firstname, ' ', surname) FROM aut INNER JOIN asrel ON aut.id = asrel.aut_id WHERE asrel.sce_id = $sid AND asrel.tit_id = 1" );
		$files = getcol( "SELECT filename FROM files WHERE category = 'sce' AND data_id = $sid AND downloadable = 1" );
	}
	$result[] = [
		'basename' => $basename,
		'title' => $scenario,
		'id' => $sid,
		'auts' => $auts,
		'files' => $files,
	];
}

$candidates = $filecandidates = 0;
$authorscore = [];
$htmla  = '<!DOCTYPE html><html><head><title>Atlantis to Alexandria PDFs</title></head><body>';
$htmla .= '<ul><li>Files:<ul><li><a href="https://loot.alexandria.dk/files/Scenariedatabasen/">Scenariedatabasen</a><ul><li><a href="https://loot.alexandria.dk/files/Scenariedatabasen/Artikler/">Artikler</a></li><li><a href="https://loot.alexandria.dk/files/Scenariedatabasen/Scenarier/">Scenarier</a></li><li><a href="https://loot.alexandria.dk/files/Scenariedatabasen/Fixes/">Fixes</a> (repaired and rotated PDF files)</li></ul></li></ul></li></ul>' . PHP_EOL;
$html .= '<table style="vertical-align: top"><thead><tr><th>Atlantis file</th><th>Title</th><th>Authors</th><th>Files</th></tr></thead>';
$html .= '<tbody>';
foreach ( $result AS $row ) {
	$color = "white";
	if (! $row['id'] ) {
		$color = "#c33";
	} elseif (! $row['files'] ) { // candidate
		$color = "#3c3";
		$candidates++;
		foreach ( $row['auts'] AS $aut ) {
			$authorscore[$aut]++;
		}
	} elseif ( strpos( implode(" ", $row['files']), '.pdf') === FALSE ) {
		$color = "#c90";
		$filecandidates++;
	}
	$html .= '<tr style="background-color: ' . $color . ';">';
	$html .= '<td><a href="https://loot.alexandria.dk/files/Scenariedatabasen/Scenarier/' . rawurlencode( $row['basename'] ) . '">[download]</a></td>';
	$html .= '<td>' . ($row['id'] ? '<a href="../data?scenarie=' . $row['id'] .'">' . $row['title'] . '</a>' : $row['title'] ) . '</td>';
	$html .= '<td>' . implode(', ', $row['auts'] ) . '</td>';
	$html .= '<td>';
	foreach($row['files'] AS $file) {
		$html .= '<a href="https://alexandria.dk/download/scenario/' . $row['id'] . '/' . rawurlencode($file) . '">' . htmlspecialchars($file) . '</a><br>';
	}
	$html .= '</td>';
	$html .= '</tr>' . PHP_EOL;
}
$html .= '</tbody></table>';

#ksort($authorscore);
arsort($authorscore);

print $htmla;
print "<p>Candidates for upload: $candidates (green background)<br>";
print "Candidates for possible better file: $filecandidates (orange background)</p>";
print $html;

print "<p>Authors, sorted by number of scenario candidates (green background):</p>";
print "<ul>";
foreach($authorscore AS $author => $num) {
	print "<li>" . $num . " - " . $author . "</li>";
}
print "</ul>";

print '</body></html>';


?>
