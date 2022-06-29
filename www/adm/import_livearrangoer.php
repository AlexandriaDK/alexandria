<?php
// import data from old table live_arrangoer - not sure of origin
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";

$title = $_REQUEST['title'] ?? NULL;
$persons = $_REQUEST['persons'] ?? NULL;
$internal = "Autoimport from table live_arrangoer /pb";

if ($title) {
    $person_list = [];
    foreach(explode(",",$persons) AS $person) {
        $person_list[] = ['name' => $person, 'role_id' => 4];
    }
    $game = ['title' => $title, 'persons' => $person_list, 'gamesystem_id' => 73];
    $game_id = create_game($game, $internal);
    header("Location: game.php?game=" . $game_id);
    exit;
}

htmladmstart("Import live_arrangoer");

$lives = getall("SELECT titel, GROUP_CONCAT(arrangoer) AS names FROM live_arrangoer WHERE titel NOT IN (SELECT title FROM game WHERE gamesystem_id = 73) GROUP BY titel ORDER BY titel");
print '<h1>live_arrangoer import</h1>';

print '
<table>
<thead>
<tr><th>Title</th><th>Organizers</th></tr>
</thead>
<tbody>
';
foreach($lives AS $live) {
    $titel = $live['titel'];
    $names = $live['names'];
    $saveurl = 'import_livearrangoer.php?title=' . rawurlencode($titel) . '&persons=' . rawurlencode($names);
    $searchurl = '/en/find?search_title=' . rawurlencode($titel) . '&search_type=findspec';
    print '<tr>' .
        '<td><a href="'. $saveurl . '">[Save]</a>' .
        '<td><a href="' . $searchurl . '">' . htmlspecialchars($titel) . '</a></td>' .
        '<td>' . htmlspecialchars($names) . '</td>' .
        '</tr>';
}
print '</tbody></table>';

?>

</body>
</html>
