<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
chdir("adm/");

$action = (string) $_REQUEST['action'];
$year = (int) ($_REQUEST['year']); // Year of con
$format = (string) $_REQUEST['format'];

$title = (string) $_REQUEST['title'];
$authors = (string) $_REQUEST['authors'];
$organizer = (string) $_REQUEST['organizer'];
$players = (string) $_REQUEST['players'];
$participants_extra = (string) $_REQUEST['participants_extra'];
$description = (string) $_REQUEST['description'];
$url = (string) $_REQUEST['url'];
$tags = (string) $_REQUEST['tags'];
$internal = (string) $_REQUEST['internal'];
$larp = (int) (bool) $_REQUEST['larp'];

$con_id = getone("SELECT id FROM convent WHERE conset_id = 183 AND year = $year");

if ($action == 'creategame') {
    $author_list = [];
    foreach(explode("#",$authors) AS $author_name) {
        $author_list[] = ['name' => $author_name, 'role_id' => 1]; // assuming author
    }
    list ($players_min, $players_max) = strSplitParticipants($players);
    $game = [
        'title' => $title,
        'persons' => $author_list,
        'participants_extra' => $participants_extra,
        'organizer' => $organizer,
        'sys_id' => ($larp ? 73 : NULL), // LARP, assuming games are LARPs
        'cons' => [ $con_id ],
        'descriptions' => ['en' => trim($description)],
        'urls' => [ $url ],
        'players_min' => $players_min,
        'players_max' => $players_max,
        'internal' => $internal,
        'tags' => explode("#", $tags),
    ];
    $game_id = create_game($game, ($internal ? $internal : 'Autoimport: Import Golden Cobra Challenge') );
    if ($game_id) {
        if ($format == 'json') {
            header("Content-Type: text/json");
            print json_encode(["success" => 1, "game_id" => $game_id]);
            exit;
        }
        $_SESSION['admin']['info'] = "Game created: $title " . dberror();
        header("Location: import_goldencobra.php?year=$year");
        exit;
    } else {
        if ($format == 'json') {
            header("Content-Type: text/json");
            print json_encode(["success" => 0]);
            exit;
        }
        print "<p>Fejl:</p><p>";
        var_dump($game);
        print "</p>";
        exit();
    }
}

function create_game_form($title, $authors, $organization, $players, $participants_extra, $description, $fulldescription, $internal, $dataset, $link = '') {
    global $year;
    $d_parts = preg_split('_\r?\n\r?\n_',$description);
    $descriptionfix = '';
    foreach($d_parts AS $d_part) {
        $descriptionfix .= preg_replace('_\s+_',' ',$d_part) . "\r\n\r\n";
    }
    $descriptionfix = preg_replace('_^ _m', '', $descriptionfix);
    $descriptionfix = trim($descriptionfix) . "\r\n";
    $descriptionfix = preg_replace('_(\r\n){3,}_',"\r\n\r\n",$descriptionfix); // collapse 3+ newlines into 2
    $url = $link;
    if ($url == '' && preg_match('_a href="(.*?)"_i', $fulldescription, $matches)) {
        $url = $matches[1];
    }
    $authorfix = preg_replace('_^by\s*_','',$authors);
    $authorfix = preg_replace('_, ?(and )?| and | ?[&/] ?_', '#',$authorfix);
    $islarp = preg_match('_live.?action|LARP_i',$description);
    $html  = '<form method="post" class="creategame"><table>';
    $html .= '<tr><td>Title:</td><td><input type="text" size="100" name="title" value="' . htmlspecialchars($title) . '"></td></tr>';
    $html .= '<tr><td>Authors (#):</td><td><input type="text" size="100"  name="authors" value="' . htmlspecialchars($authorfix) . '"></td></tr>';
    $html .= '<tr><td>Organizer:</td><td><input type="text" size="100"  name="organizer" value="' . htmlspecialchars($organization) . '"></td></tr>';
    $html .= '<tr><td>Players:</td><td><input type="text" size="100"  name="players" value="' . htmlspecialchars($players) . '"></td></tr>';
    $html .= '<tr><td>Players extra:</td><td><input type="text" size="100"  name="participants_extra" value="' . htmlspecialchars($participants_extra) . '"></td></tr>';
    $html .= '<tr><td>Description:</td><td><textarea name="description" cols="200" rows="10">' . htmlspecialchars($descriptionfix) . '</textarea></td></tr>';
    $html .= '<tr><td>Internal:</td><td><textarea name="internal" cols="200" rows="3">' . htmlspecialchars($internal) . '</textarea></td></tr>';
    $html .= '<tr><td>URL:</td><td><input type="text" size="100"  name="url" value="' . htmlspecialchars($url) . '"></td></tr>';
    $html .= '<tr><td>Tags (#):</td><td><input type="text" size="100"  name="tags" value="' . htmlspecialchars($organization) . '"></td></tr>';
    $html .= '<tr><td>Larp?:</td><td><input type="checkbox" name="larp" ' . ($islarp ? 'checked' : '' ) . '></td></tr>';
    $html .= '<tr><td></td><td><input type="submit"><input type="hidden" name="action" value="creategame"><input type="hidden" name="year" value="' . $year . '"><input type="hidden" name="submitted" value="0"></td></tr>';
    $html .= '<tr><td colspan="2"><pre>' . htmlspecialchars($dataset) . '</pre></td></tr>';
    $html .= '</table></form>' . PHP_EOL;
    return $html;
}

// Download submissions and winners:
// for year in {2014..2021}; do wget 'https://www.goldencobra.org/submissions'$year'.html' 'https://www.goldencobra.org/'$year'winners.html' ; done

htmladmstart("Import Golden Cobra");
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

?>
<h1>Import Golden Cobra Challenge</h1>
<form action="import_goldencobra.php">
    <table>
        <tr><td>Year:</td><td><input type="number" size="4" maxlength="4" name="year" value="<?php print htmlspecialchars($year); ?>"></td></tr>
        <tr><td></td><td><input type="submit"><input type="hidden" name="action" value="getdata"></td></tr>
    </table>
</form>

<?php
if (!$con_id) {
    htmladmend();
    exit;
}

list($con_name, $con_year) = getrow("SELECT name, year FROM convent WHERE id = $con_id");
$gamecount = getone("SELECT COUNT(*) FROM csrel WHERE convent_id = $con_id");
print "<p><a href=\"convent.php?con=" . $con_id . "\">" . htmlspecialchars($con_name) . " ($con_year)</a> ($gamecount " . ($gamecount == 1 ? 'game' : 'games') . ")</p>";
print "<hr>";

$file = '../../imports/goldencobra/submissions' . $year . '.html';
if (! file_exists($file)) {
    die("Error: File " . $file . " does not exist.\n");
}
$data = file_get_contents($file);

// HTML scraper
    // $pattern = '_<h3>(.*?)</h3>\s*<p><b>Authors?:</b> (.*?)<br />\s*<b>Players:</b>\s*(.*?)</p>\s*<p>(.*?)<!--_';
    // $pattern = '_<h3>(.*?)</h3>\s*<p><b>Authors?:</b> (.*?)<br />\s*<b>Players:</b>\s*(.*?)</p>\s*<p>(.*?)<!--_sm';
if ($year == 2014 || $year == 2015) {
    $data = str_replace('<h2>','<!--SPLIT--><h2>', $data);
    $outerpattern = '_<!--SPLIT-->_sm';
    $pattern = '_<h2>(.*?)</h2>\s*(?:<h3>(.*?)</h3>)\s*(.*)_sm';
} else {
    $outerpattern = '_<hr>_';
    $pattern = '_(<h2>(?:.*?)</h2>|<a(?:.*?)</a>)\s*(?:<h3>(.*?)</h3>)\s*(.*)_sm';
}
foreach(preg_split($outerpattern, $data) AS $dataset) {
    if ( preg_match($pattern, $dataset, $game) ) {
        $link = '';
        if (preg_match('_ href="(.*?)"_i', $game[1], $matches)) {
            $link = $matches[1];
        }
        $title = html_entity_decode(strip_tags($game[1]));
        $title = preg_replace("_\s+_", " ", $title);
        $title = trim($title);
        $authors = html_entity_decode(strip_tags(trim($game[2])));
        $organization = '';
        $players = '';
        $participants_extra = '';
        $fulldescription = $game[3];
        $description = html_entity_decode(strip_tags(str_replace("</a>","</a>\r\n",str_replace("</p>","</p>\n\n",$fulldescription))));
        $description = preg_replace('_^\(.*?\)_',"\\0\r\n", $description);
        $internal = '';
        $existing = getone("SELECT COUNT(*) FROM sce WHERE title = '" . dbesc($title) . "'") + getone("SELECT COUNT(*) FROM alias WHERE category = 'sce' AND label = '" . dbesc($title) . "'");
        print "<p>" . htmlspecialchars($title) . ($existing > 0 ? ' <a href="find.php?find=' . rawurlencode($title) . '" target="_blank" title="' . $existing .' existing">⚠️</a>' : '' ) . "</p>";
        print create_game_form($title, $authors, $organization, $players, $participants_extra, $description, $fulldescription, $internal, $dataset, $link);
        print "<hr>";
    } else {
        // for manual checks, e.g. special events
        if (strpos($dataset, '<h3>') !== FALSE) {
            print "<pre>" . htmlspecialchars($dataset) . "</pre><hr>";
        }
    }
}

?>
<script>
$( ".creategame" ).submit(function( event ) {
    event.preventDefault();
    if ($(this).find("input[name=submitted]").val() == 1) {
        return false;
    }
    $(this).find("input[name=submitted]").val(1);
    var query = $(this).serialize() + '&format=json';
    var myform = this;
    $.post("import_goldencobra.php", query, function (data) {
        if (data.success == 1) {
            $(myform).hide('fast');
            $(myform).prev().append(' <a href="game.php?game=' + data.game_id + '" target="_blank">✔️</a>');
        } else {
            $(myform).prev().append(" ❌");
        }
    }, "json");

});
</script>
<?php
htmladmend();
?>