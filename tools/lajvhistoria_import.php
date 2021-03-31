<?php
// Import LARP data from lavjhistoria.se
require __DIR__ . "/../www/rpgconnect.inc.php";
require __DIR__ . "/../www/base.inc.php";

$url = 'https://lajvhistoria.se/export.php';
$localfile = __DIR__ . '/lajvhistoria_export.json';
$alexandria_lavjhistoria_user_id = 21;

$known_organizations = [
    'ALF',
    'Alternaliv',
    'Arrangörsföreningen Mollberg',
    'Arrangörsgruppen Fenix',
    'Avesta Bjurfors RollspelsFörening',
    'Berättelsefrämjandet',
    'Drakontia Lajvsällskap',
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
    'MASK',
    'Militärhistoriska Sällskapet',
    'Oroboros',
    'Roverscoutlaget Orion',
    'RSK Härskarringen',
    'Riksteatern',
    'Stormens Öga',
    'Särimners Vänner',
    'Teater K',
    'Täby Spelsällskap',
    'The Story Lab',
    'Ulricehamns Liveförening',
    'via SLI.',
    'Wyrd',
];

$orgmap = [
    'Arrangörsgruppen Fenix (Anna Nyberg)' => ['person' => ['Anna Nyberg'], 'organization' => 'Arrangörsgruppen Fenix'],
    'Daniel Westberg (Chilla)' => ['person' => ['Daniel Westberg']],
    'Draco Argenteus (Nina & Anders Svensson)' => ['person' => ['Nina Svensson', 'Anders Svensson'], 'organization' => 'Draco Argenteus'],
    'FNYFF (Kerstin Bohman, Victoria Wiik, Cilla de Mander)' => ['person' => ['Kerstin Bohman', 'Victoria Wiik', 'Cilla de Mander'], 'organization' => 'FNYFF'],
    'Unionen (Dennis Rundqvist)' => [ 'person' => ['Dennis Rundqvist'], 'organization' => 'Unionen'],
    'Adéle Lindkvist via SLI' => [ 'person' => ['Adéle Lindkvist'], 'organization' => 'SLI'],

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
    $names = [];
    $organizations = [];
    $persons = [];
    $org = (string) $game->org;
    $org = trim($org);
    if (isset($orgmap[$org])) {
        if (isset($orgmap[$org]['organization']) ) {
            $organizations[] = $orgmap[$org]['organization'];
        }
        $names = $orgmap[$org]['person'];
    } elseif (preg_match('/[\(\)]/', $org)) {
        $organizations[] = $org;
    } else {
        $names = explode(", ", $org);
    }
    if (strpos($org, 'Adéle Lindkvist via SLI') === FALSE) {
        continue;
    }

    print "====" . PHP_EOL;
    print $game->name . PHP_EOL;
    print $org . PHP_EOL;
    foreach ($names AS $name) {
        $name = trim($name);
        $name = str_replace(' mfl.','',$name);
        print "Name: $name" . PHP_EOL;
        if (in_array($name, $known_organizations) || (strpos($name, " ", 0) === FALSE) ) { 
            $organizations[] = $name;
        } else {
            if (isset($orgmap[$name])) {
                if (isset($orgmap[$name]['organization']) ) {
                    $organizations[] = $orgmap[$name]['organization'];
                }
                $persons = array_merge($persons, $orgmap[$name]['person'] );
            } else {
                $persons[] = $name;
            }
        }
    }
    var_dump($persons, $organizations);
}
?>
