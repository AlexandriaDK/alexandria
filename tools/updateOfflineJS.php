<?php
$json = file_get_contents("https://alexandria.dk/en/export?dataset=all");
if ( strlen($json) < 15000000) { // sanity check - must be at least 15 MB
    exit(1);
}

$js = <<<EOD
function loadAlexandria() {
data = $json
}
EOD;
file_put_contents('AlexandriaOffline/data/alexandria_content.js', $js);
