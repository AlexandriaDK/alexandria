<?php
$check_min_size = 15000000; // must be at least 15 MB
$json = file_get_contents("https://alexandria.dk/en/export?dataset=all");
print "JSON downloaded. Size: " . strlen($json) . " bytes." . PHP_EOL;
if ( strlen($json) < $check_min_size) { // basic check
    print "Error: Size too small. Expected at least " . $check_min_size . " bytes. Not overwriting." . PHP_EOL;
    exit(1); // :TODO: Output error message
}

$js = <<<EOD
function loadAlexandria() {
data = $json
}
EOD;
file_put_contents('AlexandriaOffline/data/alexandria_content.js', $js);
print "alexandria_content.js saved: " . strlen($js) . " bytes." . PHP_EOL;
?>