<?php
// fetch list of scenarios from an arcon

$mainurl = 'https://www.spillfestival.no/arcon35/program.php';
$tournamenturl = 'https://www.spillfestival.no/arcon35/turnering.php';
$mainpath = pathinfo( $mainurl );
$tmp = 'tmp/' . $mainpath['basename'];

function savecache( $url ) {
	$path = pathinfo( $url );
	$tmp = 'tmp/' . $path['basename'];
	if ( ! file_exists( $tmp ) ) {
		$html = file_get_contents( $url );
		file_put_contents( $tmp, $html );
	} else {
		$html = file_get_contents($tmp);
	}
	return $html;
}

if ( ! file_exists( $tmp ) ) {
	$html = file_get_contents($mainurl);
	file_put_contents( $tmp, $html );
} else {
	$html = file_get_contents($tmp);
}

if ( preg_match_all( '_' . $tournamenturl . '\?id=\d+_', $html, $urls) ) {
	foreach ( $urls[0] AS $url ) {
		print "Fetching $url" . PHP_EOL;
		savecache( $url );
	}
} else {

}
?>
