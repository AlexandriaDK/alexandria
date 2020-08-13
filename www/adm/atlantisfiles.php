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
	'Skyggespor i Helvedets Forgård' => 'Skyggedans i helvedes forgård',
	'Dommedag 2056 - New Orleans' => 'Dommedag 2056: New Orleans',
	'MS Titanic II' => 'M/S Titanic II',
	'Min Lille Skat 2' => 'Min Lille Skat 2: Næsten Hjemme',
	'New York Coppers' => 'New York Coppers: Gaden uden nåde!',
	'Røde Helte' => 'Røde Helte: Hånden i hvepseboet',
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
		$auts = getcol( "SELECT CONCAT(firstname, ' ', surname) FROM aut INNER JOIN asrel ON aut.id = asrel.aut_id WHERE asrel.sce_id = $sid" );
		$files = getcol( "SELECT filename FROM files WHERE category = 'sce' AND data_id = $sid" );
	}
	$result[] = [
		'basename' => $basename,
		'title' => $scenario,
		'id' => $sid,
		'auts' => $auts,
		'files' => $files,
	];
}

$candidates = 0;
$html  = '<html><head><title>Atlantis to Alexandria PDFs</title></head><body>';
$html .= '<table style="vertical-align: top"><thead><tr><th>Atlantis file</th><th>Title</th><th>Authors</th><th>Files</th></tr></thead>';
$html .= '<tbody>';
foreach ( $result AS $row ) {
	$color = "white";
	if (! $row['id'] ) {
		$color = "#c33";
	} elseif (! $row['files'] ) {
		$color = "#3c3";
		$candidates++;
	} elseif ( strpos( implode(" ", $row['files']), '.pdf') === FALSE ) {
		$color = "#c90";
	}
	$html .= '<tr style="background-color: ' . $color . ';">';
	$html .= '<td><a href="https://loot.alexandria.dk/files/Scenariedatabasen/Scenarier/' . rawurlencode( $row['basename'] ) . '">[download]</a></td>';
	$html .= '<td>' . ($row['id'] ? '<a href="https://alexandria.dk/en/data?scenarie=' . $row['id'] .'">' . $row['title'] . '</a>' : $row['title'] ) . '</td>';
	$html .= '<td>' . implode(', ', $row['auts'] ) . '</td>';
	$html .= '<td>';
	foreach($row['files'] AS $file) {
		$html .= '<a href="https://alexandria.dk/download/scenario/' . $row['id'] . '/' . rawurlencode($file) . '">' . htmlspecialchars($file) . '</a><br>';
	}
	$html .= '</td>';
	$html .= '</tr>' . PHP_EOL;
}
$html .= '</tbody></table>';

$html .= "<p>Candidates: $candidates</p>";

print $html;

?>
