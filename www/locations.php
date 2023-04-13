<?php
require("./connect.php");
require("base.inc.php");

$id = (int) $_REQUEST['id'];
$convention_id = (int) $_REQUEST['convention_id'];
$conset_id = (int) $_REQUEST['conset_id'];
$game_id = (int) $_REQUEST['game_id'];
$startlocation = [];
$location_target = FALSE;

if ($id) {
    $startlocation = getcol("SELECT id FROM locations WHERE id = $id AND locations.geo IS NOT NULL", FALSE);
    $location_target = getentryhtml('locations', $id);
} elseif ($convention_id) {
    $startlocation = getcol("
        SELECT DISTINCT locations.id
        FROM locations
        INNER JOIN lrel ON locations.id = lrel.location_id
        WHERE convention_id = $convention_id
        AND locations.geo IS NOT NULL
        ORDER BY lrel.id
    ");
    $location_target = getentryhtml('convention', $convention_id);
} elseif ($conset_id) {
    $startlocation = getcol("
        SELECT DISTINCT locations.id
        FROM locations
        INNER JOIN lrel ON locations.id = lrel.location_id
        INNER JOIN convention ON lrel.convention_id = convention.id
        WHERE conset_id = $conset_id
        AND locations.geo IS NOT NULL
    ");
    $location_target = getentryhtml('conset', $conset_id);
} elseif ($game_id) { // both individual runs and as part of conventions
    $startlocation = getcol("
        SELECT DISTINCT locations.id
        FROM locations
        INNER JOIN lrel ON locations.id = lrel.location_id
        LEFT JOIN convention ON lrel.convention_id = convention.id
        LEFT JOIN cgrel ON convention.id = cgrel.convention_id
        LEFT JOIN gamerun ON lrel.gamerun_id = gamerun.id
        WHERE (cgrel.game_id = $game_id OR gamerun.game_id = $game_id)
        AND locations.geo IS NOT NULL
    ");
    $location_target = getentryhtml('game', $game_id);
}

$locations = getall("
    SELECT l.id, l.name, l.address, l.city, l.country, l.note, geo IS NOT NULL AS hasGeo, ST_X(geo) AS latitude, ST_Y(geo) AS longitude, IF(lrel.gamerun_id IS NULL, 'convention', 'gamerun') AS type, IF(lrel.gamerun_id IS NULL, lrel.convention_id, gr.game_id) AS data_id, c.conset_id AS conset_id, IF(lrel.gamerun_id IS NULL, CONCAT(c.name, ' (', IF(c.year, c.year, '?'), ')'), CONCAT(g.title, ' (', IF(YEAR(gr.begin), YEAR(gr.begin), '?'), ')') ) AS data_label, IF(lrel.gamerun_id IS NULL, c.begin, gr.begin) AS data_begin, IF(lrel.gamerun_id IS NULL, c.cancelled, gr.cancelled) AS data_cancelled, COALESCE(c.begin, c.year, gr.begin) AS data_starttime, COALESCE(c.end, c.year, gr.end) AS data_endtime
    FROM locations l
    LEFT JOIN lrel ON l.id = lrel.location_id
    LEFT JOIN convention c ON lrel.convention_id = c.id
    LEFT JOIN gamerun gr ON lrel.gamerun_id = gr.id
    LEFT JOIN game g ON gr.game_id = g.id
    WHERE geo IS NOT NULL
    ORDER BY data_starttime
", FALSE);

$events = [];
foreach($locations AS $event) {
    $location_id = $event['id'];
    if (!isset($events[$location_id])) {
        $events[$location_id] = [
            'data' => ['name' => $event['name'], 'address' => $event['address'], 'city' => $event['city'], 'country' => getCountryName($event['country']), 'note' => $event['note'], 'hasGeo' => $event['hasGeo'], 'latitude' => $event['latitude'], 'longitude' => $event['longitude'] ],
            'events' => []
        ];
    }
    if ($event['data_id']) {
        $events[$location_id]['events'][] = ['type' => $event['type'], 'data_id' => $event['data_id'], 'data_label' => $event['data_label'], 'data_begin' => $event['data_begin'], 'data_starttime' => $event['data_starttime'], 'data_cancelled' => $event['data_cancelled'], 'conset_id' => $event['conset_id'], 'nicedateset' => nicedateset($event['data_starttime'], $event['data_endtime']) ];
    }
}

// Smarty
$t->assign('type','locations');
$t->assign('locations', json_encode($events));
$t->assign('startlocation', json_encode($startlocation));
$t->assign('start_id', $id);
$t->assign('location_target', $location_target);

$t->display('locations.tpl');
?>
