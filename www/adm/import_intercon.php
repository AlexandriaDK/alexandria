<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
chdir("adm/");

$action = (string) $_REQUEST['action'];
$intercon_letter = (string) ($_REQUEST['intercon_letter']); // Name of con, e.g. H for "Intercon H"
$con_id = intval($_REQUEST['con_id']); // Alexandria ID
$format = (string) $_REQUEST['format'];

$title = (string) $_REQUEST['title'];
$authors = (string) $_REQUEST['authors'];
$gms = (string) $_REQUEST['gms'];
$organizer = (string) $_REQUEST['organizer'];
$players = (string) $_REQUEST['players'];
$participants_extra = (string) $_REQUEST['participants_extra'];
$description = (string) $_REQUEST['description'];
$url = (string) $_REQUEST['url'];
$tags = (string) $_REQUEST['tags'];
$internal = (string) $_REQUEST['internal'];

if ($action == 'creategame') {
  $author_list =  $gm_list = [];
  foreach (explode("#", $authors) as $author_name) {
    $author_list[] = ['name' => $author_name, 'role_id' => 1]; // assuming author
  }
  foreach (explode("#", $gms) as $gm_name) {
    $gm_list[] = ['name' => $gm_name, 'role_id' => 4]; // assuming organizer
  }
  list($players_min, $players_max) = strSplitParticipants($players);
  $game = [
    'title' => $title,
    'persons' => $author_list,
    'gms' => $gm_list,
    'participants_extra' => $participants_extra,
    'organizer' => $organizer,
    'gamesystem_id' => 73, // LARP, assuming games are LARPs
    'cons' => [$con_id],
    'descriptions' => ['en' => trim($description)],
    'urls' => [$url],
    'players_min' => $players_min,
    'players_max' => $players_max,
    'internal' => $internal,
    'tags' => explode("#", $tags),
  ];
  $game_id = create_game($game, ($internal ? $internal : 'Autoimport: Import Intercon'));
  if ($game_id) {
    if ($format == 'json') {
      header("Content-Type: text/json");
      print json_encode(["success" => 1, "game_id" => $game_id]);
      exit;
    }
    $_SESSION['admin']['info'] = "Game created: $title " . dberror();
    header("Location: import_intercon.php?intercon_letter=$intercon_letter&con_id=$con_id");
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

function create_game_form($title, $authors, $organizers, $organization, $players, $participants_extra, $description, $fulldescription, $internal, $dataset)
{
  global $intercon_letter;
  global $con_id;
  $d_parts = preg_split('_\r?\n\r?\n_', $description);
  $descriptionfix = '';
  foreach ($d_parts as $d_part) {
    $descriptionfix .= preg_replace('_\s+_', ' ', $d_part) . "\r\n\r\n";
  }
  $descriptionfix = trim($descriptionfix) . "\r\n";
  $url = '';
  if (preg_match('_a href="(.*?)"_i', $fulldescription, $matches)) {
    $url = $matches[1];
  }
  $regex_and = '_, ?(and )?| and | ?[&/] ?_';
  $authorfix = preg_replace($regex_and, '#', $authors);
  $organizerfix = preg_replace($regex_and, '#', $organizers);
  $html  = '<form method="post" class="creategame"><table>';
  $html .= '<tr><td>Title:</td><td><input type="text" size="100" name="title" value="' . htmlspecialchars($title) . '"></td></tr>';
  $html .= '<tr><td>Authors (#):</td><td><input type="text" size="100"  name="authors" value="' . htmlspecialchars($authorfix) . '"></td></tr>';
  $html .= '<tr><td>Team members (#):</td><td><input type="text" size="100"  name="gms" value="' . htmlspecialchars($organizerfix) . '"></td></tr>';
  $html .= '<tr><td>Organizer:</td><td><input type="text" size="100"  name="organizer" value="' . htmlspecialchars($organization) . '"></td></tr>';
  $html .= '<tr><td>Players:</td><td><input type="text" size="100"  name="players" value="' . htmlspecialchars($players) . '"></td></tr>';
  $html .= '<tr><td>Players extra:</td><td><input type="text" size="100"  name="participants_extra" value="' . htmlspecialchars($participants_extra) . '"></td></tr>';
  $html .= '<tr><td>Description:</td><td><textarea name="description" cols="200" rows="10">' . htmlspecialchars($descriptionfix) . '</textarea></td></tr>';
  $html .= '<tr><td>Internal:</td><td><textarea name="internal" cols="200" rows="3">' . htmlspecialchars($internal) . '</textarea></td></tr>';
  $html .= '<tr><td>URL:</td><td><input type="text" size="100"  name="url" value="' . htmlspecialchars($url) . '"></td></tr>';
  $html .= '<tr><td>Tags (#):</td><td><input type="text" size="100"  name="tags" value="' . htmlspecialchars($organization) . '"></td></tr>';
  $html .= '<tr><td></td><td><input type="submit"><input type="hidden" name="action" value="creategame"><input type="hidden" name="intercon_letter" value="' . $intercon_letter . '"><input type="hidden" name="con_id" value="' . $con_id . '"><input type="hidden" name="submitted" value="0"></td></tr>';
  $html .= '<tr><td colspan="2"><pre>' . htmlspecialchars($dataset) . '</pre></td></tr>';
  $html .= '</table></form>' . PHP_EOL;
  return $html;
}

// Two different formats
// Intercon A-C is pure HTML, needs to be scraped
// Intercon D-U+ is JSON, can be requested with large pageSize (e.g. pageSize: 1000)

// Download A-C HTML:
// for letter in A B C; do wget 'https://interactiveliterature.org/'$letter'/' -O $letter.html; done

// Download D-U JSON:
// for letter in {d..u}; do curl --compressed 'https://'$letter'.interconlarp.org/graphql' -X POST -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0' -H 'Accept: */*' -H 'Accept-Language: da,en-US;q=0.7,en;q=0.3' -H 'Accept-Encoding: gzip, deflate, br' -H 'Referer: https://d.interconlarp.org/events' -H 'content-type: application/json' -H 'x-csrf-token: yufEVkOjvu2CaZzIMFg6tIMDqySHUI9GR5zsyeSJXGsw3vHIuCSt4bprOEdNcTr5cLjyE6vtu+DpD46sPeP3lQ==' -H 'x-intercode-user-timezone: Europe/Copenhagen' -H 'Origin: https://'$letter'.interconlarp.org' -H 'Connection: keep-alive' -H 'Cookie: _intercode_session=5f8695e46beddb33262c5355ffe818ce' -H 'Sec-Fetch-Dest: empty' -H 'Sec-Fetch-Mode: no-cors' -H 'Sec-Fetch-Site: same-origin' -H 'Pragma: no-cache' -H 'Cache-Control: no-cache' --data-raw '{"operationName":"EventListEventsQuery","variables":{"page":1,"pageSize":1000,"sort":[{"field":"title","desc":false}],"filters":{"category":[]}},"query":"query EventListEventsQuery($page: Int, $pageSize: Int, $filters: EventFiltersInput, $sort: [SortInput!]) {\n  currentAbility {\n    can_read_schedule\n    __typename\n  }\n  convention: conventionByRequestHost {\n    id\n    ...CommonConventionData\n    events_paginated(\n      page: $page\n      per_page: $pageSize\n      filters: $filters\n      sort: $sort\n    ) {\n      total_entries\n      total_pages\n      current_page\n      per_page\n      entries {\n        id\n        title\n        created_at\n        short_blurb_html\n        form_response_attrs_json\n        my_rating\n        event_category {\n          id\n          name\n          team_member_name\n          teamMemberNamePlural\n          __typename\n        }\n        runs {\n          id\n          starts_at\n          __typename\n        }\n        team_members {\n          id\n          display_team_member\n          user_con_profile {\n            id\n            last_name\n            name_without_nickname\n            gravatar_enabled\n            gravatar_url\n            __typename\n          }\n          __typename\n        }\n        __typename\n      }\n      __typename\n    }\n    __typename\n  }\n}\n\nfragment CommonConventionData on Convention {\n  id\n  name\n  starts_at\n  ends_at\n  site_mode\n  timezone_name\n  timezone_mode\n  ticket_name\n  ticket_mode\n  event_categories {\n    id\n    name\n    scheduling_ui\n    default_color\n    full_color\n    signed_up_color\n    __typename\n  }\n  __typename\n}\n"}' --output $letter.json; done


htmladmstart("Import Intercon");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<h1>Import Intercon</h1>
<form action="import_intercon.php">
  <table>
    <tr>
      <td>Letter:</td>
      <td><input type="text" size="1" maxlength="1" name="intercon_letter" value="<?php print htmlspecialchars($intercon_letter); ?>"></td>
    </tr>
    <tr>
      <td>Con ID:</td>
      <td><input type="text" size="5" name="con_id" value="<?php print htmlspecialchars($con_id); ?>"></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit"><input type="hidden" name="action" value="getdata"></td>
    </tr>
  </table>
</form>

<?php
if (strlen($intercon_letter) != 1 || !$con_id) {
  htmladmend();
  exit;
}

list($con_name, $con_year) = getrow("SELECT name, year FROM convention WHERE id = $con_id");
$gamecount = getone("SELECT COUNT(*) FROM cgrel WHERE convention_id = $con_id");
print "<p><a href=\"convention.php?con=" . $con_id . "\">" . htmlspecialchars($con_name) . " ($con_year)</a> ($gamecount " . ($gamecount == 1 ? 'game' : 'games') . ")</p>";
print "<hr>";


$type = in_array(strtoupper($intercon_letter), ['A', 'B', 'C']) ? 1 : 2;
$file = '../../imports/intercon/' . ($type == 1 ? strtoupper($intercon_letter) . '.html' : strtolower($intercon_letter) . '.json');
if (!file_exists($file)) {
  die("Error: File " . $file . " does not exist.\n");
}
$data = file_get_contents($file);

if ($type == 1) { // HTML scraper
  // $pattern = '_<h3>(.*?)</h3>\s*<p><b>Authors?:</b> (.*?)<br />\s*<b>Players:</b>\s*(.*?)</p>\s*<p>(.*?)<!--_';
  // $pattern = '_<h3>(.*?)</h3>\s*<p><b>Authors?:</b> (.*?)<br />\s*<b>Players:</b>\s*(.*?)</p>\s*<p>(.*?)<!--_sm';
  $pattern = '_<!--(.*?)-->_sm';
  foreach (preg_split($pattern, $data) as $dataset) {
    $pattern = '_\s*<h3>(.*?)</h3>\s*<p><b>Authors?:</b> (.*?)<br />\s*<b>Players:</b>\s*(.*?)</p>\s*<(?:p|div)[^>]*>(.*)_sm';
    if (preg_match($pattern, $dataset, $game)) {
      $title = html_entity_decode(strip_tags($game[1]));
      $title = preg_replace("_\s+_", " ", $title);
      $authors = strip_tags($game[2]);
      $organizers = ''; // :TODO: Missing in HTML scraper
      $organization = '';
      $players = strip_tags($game[3]);
      $participants_extra = '';
      $fulldescription = $game[4];
      $description = html_entity_decode(strip_tags($fulldescription));
      $internal = '';
      $existing = getone("SELECT COUNT(*) FROM game WHERE title = '" . dbesc($title) . "'");
      print "<p>" . htmlspecialchars($title) . ($existing > 0 ? ' <a href="find.php?find=' . rawurlencode($title) . '" target="_blank" title="' . $existing . ' existing">⚠️</a>' : '') . "</p>";
      print create_game_form($title, $authors, $organizers, $organization, $players, $participants_extra, $description, $fulldescription, $internal, $dataset);
      print "<hr>";
    } else {
      // for manual checks, e.g. special events
      if (strpos($dataset, '<h3>') !== false) {
        print "<pre>" . htmlspecialchars($dataset) . "</pre><hr>";
      }
    }
  }
} elseif ($type == 2) { // JSON scraper
  $dataset = json_decode($data);
  $con = $dataset->data->convention;
  $conname = $con->name;
  foreach ($con->events_paginated->entries as $entry) {
    $intercon_id = $entry->id;
    $title = html_entity_decode($entry->title);
    $form = json_decode($entry->form_response_attrs_json);
    #        print "<pre>"; var_dump($form); print "</pre>";
    $category = $entry->event_category->name;
    if ($category != 'Larp') {
      print "<p>" . htmlspecialchars("$title ($category)") . "</p>";
      print "<hr>";
      continue;
    }

    $fulldescription = html_entity_decode(mojibakefix($form->description));
    $organization = html_entity_decode(mojibakefix($form->organization));
    $authors = html_entity_decode(mojibakefix($form->author));
    $length_hours = $form->length_seconds / 3600;
    $description = strip_tags($fulldescription);
    $team_members = [];
    foreach ($entry->team_members as $member) {
      $team_members[] = $member->user_con_profile->name_without_nickname;
    }
    $min_players = 0;
    $max_players = 0;
    $playernotes = [];
    foreach ($form->registration_policy->buckets as $bucket) {
      $min = $bucket->minimum_slots;
      $max = $bucket->total_slots;
      $min_players += $min;
      $max_players += $max;
      $playernotes[] = $bucket->description . ": " . ($min == $max ? $min : $min . "-" . $max);
      $last_bucket_key = $bucket->key;
    }
    $players = $min_players . "-" . $max_players;
    if (count($playernotes) == 1 && $last_bucket_key == 'signups') {
      $participants_extra = '';
    } else {
      $participants_extra = implode(", ", $playernotes);
    }
    $members = implode(', ', $team_members); // For internal note
    $internal = "Autoimport: Import Intercon\nIntercon ID: $intercon_id\nDuration: $length_hours hours\nGMs: $members\n\nEntry: " . json_encode($entry) . "\n\nForm: " . json_encode($form) . "\n";

    $existing = getone("SELECT COUNT(*) FROM game WHERE title = '" . dbesc($title) . "'");
    print "<p>" . htmlspecialchars($title) . ($existing > 0 ? ' <a href="find.php?find=' . rawurlencode($title) . '" target="_blank" title="' . $existing . ' existing">⚠️</a>' : '') . "</p>";
    print create_game_form($title, $authors, $members, $organization, $players, $participants_extra, $description, $fulldescription, $internal, '');
    print "<hr>";
  }
}
?>
<script>
  $(".creategame").submit(function(event) {
    event.preventDefault();
    if ($(this).find("input[name=submitted]").val() == 1) {
      return false;
    }
    $(this).find("input[name=submitted]").val(1);
    var query = $(this).serialize() + '&format=json';
    var myform = this;
    $.post("import_intercon.php", query, function(data) {
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