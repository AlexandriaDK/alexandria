<?php
$json = file_get_contents("https://alexandria.dk/en/export?dataset=all");
$js = <<<EOD
function loadAlexandria() {
data = $json
}
EOD;
file_put_contents('AlexandriaOffline/data/alexandria_content.js', $js);
?>