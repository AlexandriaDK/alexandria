<?php
// Import LARP data from lavjhistoria.se
require __DIR__ . "/../www/rpgconnect.inc.php";
require __DIR__ . "/../www/base.inc.php";

$url = 'https://lajvhistoria.se/export.php';
$localfile = __DIR__ . '/lajvhistoria_export.json';
$alexandria_lavjhistoria_user_id = 21;

$organizations = [
    'ALF',
    'Alternaliv',
    'Arrangörsföreningen Mollberg',
    'Arrangörsgruppen Fenix',
    'Avesta Bjurfors RollspelsFörening',
    'Enhörningen',
    'Ett Glas',
    'FF International inc.',
    'FF International',
    'Fabel',
    'Förbundet Vi Unga (Key Bjuhr)',
    'Föreningen Solnedgång',
    'Galadrim',
    'Gyllene Hjorten',
    'Halvhörningen',
    'Interaktiva Uppsättningar',
    'Krok',
    'LRS/ORoK/Unga Örnar',
    'LRS/ORoK/Vi Unga',
    'Lajvsällskapet Romantiska Sagor',
    'Lajvsällskapet Romantiska Sagor',
    'MASK',
    'Militärhistoriska Sällskapet',
    'Oroboros',
    'RSK Härskarringen',
    'Riksteatern',
    'Särimners Vänner',
    'Teater K',
    'The Story Lab',
    'Ulricehamns Liveförening',
    'Wyrd',
];

$orgmap = [
    'Arrangörsgruppen Fenix (Anna Nyberg)' => ['person' => ['Anna Nyberg'], 'organization' => 'Arrangörsgruppen Fenix'],
    'Daniel Westberg (Chilla)' => ['person' => ['Daniel Westberg']],
    'Draco Argenteus (Nina & Anders Svensson)' => ['person' => ['Nina Svensson', 'Anders Svensson'], 'organization' => 'Draco Argenteus'],
    'FNYFF (Kerstin Bohman, Victoria Wiik, Cilla de Mander)' => ['person' => ['Kerstin Bohman', 'Victoria Wiik', 'Cilla de Mander'], 'organization' => 'FNYFF'],
    'Unionen (Dennis Rundqvist)' => [ 'person' => ['Dennis Rundqvist'], 'organization' => 'Unionen'],
];

$file = $localfile;

$games = json_decode( file_get_contents( $file ) );
if ( ! $games ) {
    die("Can't load games" . PHP_EOL);
}

foreach ($games AS $game) {
    $aid = getone("SELECT id FROM sce WHERE title = '" . dbesc($game->name) . "'");
    if ($aid) { // skip if we already "know" the scenario
        // :TODO: Insert link into new data table
        continue;
    }
    $organization = [];
    $authors = (string) $game->org;
    if (isset($orgmap[$authors])) {
        if (isset($orgmap[$authors]['organization']) ) {
            $organization[] = $orgmap[$authors]['organization'];
        }
        $authors = $orgmap[$authors];
    } elseif (preg_match('/[\(\)]/', $authors)) {
        $authors = explode(", ", $game->org);

    #print $game->name . PHP_EOL;
    print str_replace(', ', PHP_EOL, $game->org) . PHP_EOL;
// handle "mfl"
}
?>
