<?php
// Import data from convention Intercon
require __DIR__ . "/../../www/rpgconnect.inc.php";
require __DIR__ . "/../../www/base.inc.php";

// Two different formats
// Intercon A-C is pure HTML, needs to be scraped
// Intercon D-U+ is JSON, can be requested with large pageSize (e.g. pageSize: 1000)

$intercon_letter = (string) ($_SERVER['argv']['1'] ?? ''); // Name of con, e.g. H for "Intercon H"
$con_id = intval($_SERVER['argv']['2'] ?? 0); // Alexandria ID
$do_run = intval($_SERVER['argv']['3'] ?? 0); // Dry run or execute? 1 for execute

if (strlen($intercon_letter) != 1 || ! $con_id) {
    die("Usage: php import_intercon.php intercon_letter con_id run\nE.g.:\nphp import_intercon.php A 1573 0 # 0 for dry run, 1 for execute\n");
}

$type = in_array($intercon_letter, ['A','B','C']) ? 1 : 2;

if ($type == 1) { // HTML scraper
    $url = 'https://interactiveliterature.org/' . strtoupper($intercon_letter);
    $html = file_get_contents($url);
    #$pattern = '_<h3>(.*?)</h3>\s*<p><b>Authors?:</b> (.*?)<br />\s*<b>Players:</b>\s*(.*?)</p>\s*<p>(.*?)<!--_';
    $pattern = '_<h3>(.*?)</h3>\s*<p><b>Authors?:</b> (.*?)<br />\s*<b>Players:</b>\s*(.*?)</p>\s*<p>(.*?)<!--_sm';
    if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
        foreach($matches AS $game) {
            $title = strip_tags($game[1]);
            $authors = strip_tags($game[2]);
            $description = strip_tags($game[3]);
            print $title . "\n";
        }
    } else {
        print "No match\n";
    }
} elseif ($type == 2) { // JSON scraper
    $url = 'https://' . strtolower($intercon_letter) . '.interconlarp.org/graphql';
}


?>