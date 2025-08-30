<?php
require_once "adm.inc.php";
require_once "base.inc.php";
chdir("..");
require_once "rpgconnect.inc.php";
require_once "base.inc.php";
$this_type = 'locations';

$id = (int) $_REQUEST['id'];
$action = (string) $_REQUEST['action'];
$name = trim((string) $_REQUEST['name']);
$address = trim((string) $_REQUEST['address']);
$city = trim((string) $_REQUEST['city']);
$country = trim((string) $_REQUEST['country']);
$note = trim((string) $_REQUEST['note']);
$latlon = (string) $_REQUEST['latlon'];
$locationreference = (string) $_REQUEST['locationreference'];
$relation_id = (int) $_REQUEST['relation_id'];
$gamerun_id = (int) $_REQUEST['gamerun_id'];
$convention_id = (int) $_REQUEST['convention_id'];
$event_locations = (array) $_REQUEST['event_locations'];
$new = false;


if ($action == "createlocation") {
  doquery("INSERT INTO locations (name, address, city, country, note) VALUES ('','','','','')");
  $id = dbid();
  $action = "updatelocation";
  $new = true;
}

// Edit location
if ($action == "updatelocation" && $id) {
  [$latitude, $longitude] = explode(",", str_replace('°', '', $latlon));
  $geosql = 'null';
  if (is_numeric($latitude) && is_numeric($longitude)) {
    $geosql = "ST_GeomFromText('POINT($latitude $longitude)', 4326)";
  }
  $q = "UPDATE locations SET " .
    "name = '" . dbesc($name) . "', " .
    "address = '" . dbesc($address) . "', " .
    "city= '" . dbesc($city) . "', " .
    "country = '" . dbesc($country) . "', " .
    "note = '" . dbesc($note) . "', " .
    "geo = " . $geosql . " " .
    "WHERE id = $id";
  $r = doquery($q);
  print dberror();
  $createdupdated = ($new ? 'created' : 'updated');
  if ($r) {
    chlog($id, $this_type, "Location $createdupdated: " . $name);
  }
  $_SESSION['admin']['info'] = "Location $createdupdated! " . dberror();
  rexit($this_type, ['id' => $id]);
}

// Delete location
if ($action == "Delete" && $id) {
  $error = [];
  if (getCount('lrel', $id, false, 'location')) $error[] = "convention/game run";
  if ($error) {
    $_SESSION['admin']['info'] = "Can't delete. The location still has the following references: " . implode(", ", $error);
    rexit($this_type, ['id' => $id]);
  } else {
    $name = getone("SELECT name FROM locations WHERE id = $id");

    $q = "DELETE FROM locations WHERE id = $id";
    $r = doquery($q);
    if ($r) {
      chlog($id, $this_type, "Location deleted: $name");
    }
    $_SESSION['admin']['info'] = "Location deleted! " . dberror();
    rexit($this_type, []);
  }
}

// Add relation
if ($action == "createrelation" && $id) {
  $relation = preg_match('_^(c|gr)(\d+)_', $locationreference, $matches);
  if (!$relation) {
    $_SESSION['admin']['info'] = "Didn't find id in relation! " . dberror();
    rexit($this_type, ['id' => $id]);
  }
  $convention_id = $gamerun_id = null;
  if ($matches['1'] == 'c') {
    $convention_id = $matches[2];
  } elseif ($matches['1'] == 'gr') {
    $gamerun_id = $matches[2];
  }
  // no duplicates
  $existing = 0;
  if ($convention_id) {
    $existing = getone("SELECT COUNT(*) FROM lrel WHERE location_id = $id AND convention_id = $convention_id AND gamerun_id IS null");
  } elseif ($gamerun_id) {
    $existing = getone("SELECT COUNT(*) FROM lrel WHERE location_id = $id AND convention_id IS null AND gamerun_id = $gamerun_id");
  }
  if ($existing) {
    $_SESSION['admin']['info'] = "The relation already exists. " . dberror();
    rexit($this_type, ['id' => $id]);
  }
  doquery("INSERT INTO lrel (location_id, convention_id, gamerun_id) values ($id, " . sqlifnull($convention_id) . ", " . sqlifnull($gamerun_id) . ")");
  chlog($id, $this_type, "Relation added: $locationreference");
  $_SESSION['admin']['info'] = "Relation added! " . dberror();
  rexit($this_type, ['id' => $id]);
}


// Remove relation
if ($action == "removerelation" && $id && $relation_id) {
  if (! getone("SELECT COUNT(*) FROM lrel WHERE location_id = $id AND id = $relation_id")) {
    $_SESSION['admin']['info'] = "The relation does not exist. " . dberror();
    rexit($this_type, ['id' => $id]);
  }
  $relationstring = getone("SELECT IF(convention_id IS NOT null, CONCAT('c', convention_id), CONCAT('gr', gamerun_id)) AS relationstring FROM lrel WHERE location_id = $id AND id = $relation_id");
  doquery("DELETE FROM lrel WHERE location_id = $id AND id = $relation_id");
  chlog($id, $this_type, "Relation removed: $relationstring");
  $_SESSION['admin']['info'] = "Relation removed! " . dberror();
  rexit($this_type, ['id' => $id]);
}

// Update relations for gamerun
if ($action == "updateevent" && ($gamerun_id || $convention_id)) {
  if ($gamerun_id) {
    $game_id = getone("SELECT game.id FROM game INNER JOIN gamerun ON game.id = gamerun.game_id WHERE gamerun.id = $gamerun_id");
    if (!$game_id) {
      $_SESSION['admin']['info'] = "Didn't find id in relation! " . dberror();
      rexit($this_type, ['gamerun_id' => $gamerun_id]);
    }
    doquery("DELETE FROM lrel WHERE gamerun_id = $gamerun_id");
  } elseif ($convention_id) {
    $convention_id = getone("SELECT id FROM convention WHERE id = $convention_id");
    if (!$convention_id) {
      $_SESSION['admin']['info'] = "Didn't find id in relation! " . dberror();
      rexit($this_type, ['convention_id' => $convention_id]);
    }
    doquery("DELETE FROM lrel WHERE convention_id = $convention_id");
  }
  foreach ($event_locations as $location) {
    $location_id = intval($location);
    if ($location_id) {
      if ($gamerun_id) {
        doquery("INSERT INTO lrel (location_id, gamerun_id) VALUES ($location_id, $gamerun_id)");
      } elseif ($convention_id) {
        doquery("INSERT INTO lrel (location_id, convention_id) VALUES ($location_id, $convention_id)");
      }
    }
  }
  if ($gamerun_id) {
    chlog($game_id, 'game', "Updating location relations for game run #$gamerun_id");
    $_SESSION['admin']['info'] = "Locations updated for game run! " . dberror();
    rexit($this_type, ['gamerun_id' => $gamerun_id]);
  } elseif ($convention_id) {
    chlog($convention_id, 'convention', "Updating location relations");
    $_SESSION['admin']['info'] = "Locations updated for convention! " . dberror();
    rexit($this_type, ['convention_id' => $convention_id]);
  }
}

$head = '
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin=""/>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"
     integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
     crossorigin=""></script>
';

htmladmstart("Locations", $head);

function trEdit($label, $attribute, $value, $editable = true, $placeholder = "", $extra_field_id = "", $extra_field_value = "")
{
  $html = '<tr>' .
    '<td>' . htmlspecialchars($label) . '</td>';
  if ($editable) {
    $html .= '<td><input type="text" size="50" name="' . htmlspecialchars($attribute) . '" id="' . htmlspecialchars($attribute) . '" value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '"></td>' .
      ($extra_field_id ? '<td id="' . $extra_field_id . '">' . htmlspecialchars($extra_field_value) . '</td>' : '') .
      '</tr>' . PHP_EOL;
  } else {
    $html .= '<td>' . htmlspecialchars($value) . '</td>';
  }
  return $html;
}

function generateJSMapHTML($latitude, $longitude, $zoom, $marker)
{
  $js = <<<EOD
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-control-geocoder/dist/Control.Geocoder.css" />
	<script src="https://cdn.jsdelivr.net/npm/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
	<script>
var marker;

function onMapClick(e) {
	var latlon = '' + e.latlng.lat + ', ' + e.latlng.lng;
	$("#latlon").val(latlon);
	$("#latlon").css("color", "#3F3").animate({ color: "#000"}, 500);
	// Clear marker)
	if (marker) {
		marker.remove();
	}
	marker = L.marker(e.latlng).addTo(map);
}

var osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
});

var wmsOrtoLayer = L.tileLayer.wms('https://api.dataforsyningen.dk/orto_foraar_DAF?service=WMS&token=5d6c5118e3f2ab00b8b2aa21e9140087&', {
	layers: 'orto_foraar_12_5',
	attribution: 'Indeholder data fra Styrelsen for Dataforsyning og Infrastruktur, Ortofoto Forår, WMS-tjeneste'
});

var Thunderforest_SpinalMap = L.tileLayer('https://{s}.tile.thunderforest.com/spinal-map/{z}/{x}/{y}.png?apikey=35178872612640c0abf67975149afa20', {
	attribution: '&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
	apikey: '35178872612640c0abf67975149afa20',
	maxZoom: 19
});

var map = L.map('map', {
	center: [$latitude, $longitude],
	zoom: $zoom,
	layers: [osmLayer]
});

map.on('click', onMapClick);

var baseMaps = {
	"OpenStreetMap": osmLayer,
	"Aerial imagery (Denmark only)": wmsOrtoLayer,
	"Spinal Map": Thunderforest_SpinalMap,
}

L.Control.geocoder().addTo(map);
var layerControl = L.control.layers(baseMaps).addTo(map);
L.control.scale().addTo(map);


var smallIcon = new L.Icon({
	iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
	shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
	iconSize: [13, 21],
	iconAnchor: [6, 21],
	popupAnchor: [1, -17],
	shadowSize: [21, 21]
});


$.getJSON("../xmlrequest.php?action=getlocations", ( locations ) => {
	for(place_id in locations) {
		var place = locations[place_id];
		if (place.data.hasGeo) {
			var highlight = false;
			var markerText = '<a href="locations.php?id=' + place_id + '"><b>' + place.data.name + '</b></a><br>';
			if (place.data.city) {
				markerText += place.data.city; // escape!
			}
			if (place.data.city && place.data.country) {
				markerText += ', ';
			}
			if (place.data.country) {
				markerText += place.data.country; // escape!
			}
			if (place.data.city || place.data.country) {
				markerText += '<br>';
			}
			markerText += '<br>';
			for(event of place.events) {
				var link = (event.type == 'convention' ? 'convention.php?con=' : 'game.php?game=') + event.data_id;
				var className = (event.type == 'convention' ? 'con' : 'game');
				var classCancelled = (event.data_cancelled == '1' ? 'cancelled' : '');
				var div = document.createElement('div');
				var node = document.createTextNode(event.data_label);
				div.appendChild(node);
				markerText += `<a href="\${link}" class="\${className} \${classCancelled}" title="\${event.nicedateset}">\${div.innerHTML}</a><br>`;
			}
			if (place.data.note) {
				var div = document.createElement('div');
				var node = document.createTextNode(place.data.note);
				div.appendChild(node);
				markerText += '<br><span class="locationnote">' + div.innerHTML + '<br> </span>';
			}
			var marker = L.marker([place.data.latitude, place.data.longitude], {icon: smallIcon}).addTo(map);
			marker.bindTooltip(place.data.name).bindPopup(markerText);
		}
	}
});


EOD;
  if ($marker) {
    $js .= <<<EOD
marker = L.marker([$latitude, $longitude]).addTo(map);
EOD;
  }
  $js .= <<<EOD
</script>
EOD;
  return $js;
}

function getConnections($id)
{
  $html = '';
  $connections = getall("
		SELECT l.id, l.convention_id, l.gamerun_id, c.name, c.begin, c.year, COALESCE(c.place, gr.location) AS location, g.title, g.id AS game_id, gr.begin, COALESCE(c.begin, c.year, gr.begin) AS starttime
		FROM lrel l
		LEFT JOIN convention c ON l.convention_id = c.id
		LEFT JOIN gamerun gr ON l.gamerun_id = gr.id
		LEFT JOIN game g ON gr.game_id = g.id
		WHERE l.location_id = $id
		ORDER BY starttime
	");
  $html .= '<table><thead><tr><th>ID</th><th>Name</th><th>Date</th><th>Type</th><th>Noted location</th></tr></thead><tbody>' . PHP_EOL;
  foreach ($connections as $connection) {
    $type = $name = $editlink = '';
    if ($connection['convention_id']) {
      $event_id = 'c' . $connection['convention_id'];
      $type = 'convention';
      $name = $connection['name'] . " (" . ($connection['year'] ? yearname($connection['year']) : '?') . ")";
      $editlink = 'convention.php?con=' . $connection['convention_id'];
    } elseif ($connection['gamerun_id']) {
      $event_id = 'gr' . $connection['gamerun_id'];
      $type = 'gamerun';
      $name = $connection['title'];
      $editlink = 'run.php?id=' . $connection['game_id'];
    }
    $date = fulldate($connection['starttime']);
    $deleteurl = 'locations.php?action=removerelation&id=' . $id . '&relation_id=' . $connection['id'];

    $html .= '<tr>' .
      '<td>' . $event_id . '</td>' .
      '<td><a href="' . $editlink . '">' . htmlspecialchars($name) . '</a></td>' .
      '<td class="number">' . htmlspecialchars($date) . '</td>' .
      '<td>' . ucfirst($type) . '</td>' .
      '<td>' . htmlspecialchars($connection['location']) . '</td>' .
      '<td><a href="#" onclick="if(confirm(\'Confirm that you want to delete the connection for the location to ' . htmlspecialchars($name) . '\')) { location.href=\'' . $deleteurl . '\'; return false;} else {return false;}">[Delete]</a></td>' .
      '</tr>' . PHP_EOL;
  }
  $html .= '</tbody></table>';
  return $html;
}

function showLocations()
{
  $result = getall("
		SELECT l.id, l.name, l.address, l.city, l.country, l.geo, COUNT(lrel.id) AS connections
		FROM locations l
		LEFT JOIN lrel ON l.id = lrel.location_id
		GROUP BY l.id
		ORDER BY l.id, l.name
	");
  print '<p><a href="locations.php?action=new">[New location]</a></p>' . PHP_EOL;
  print '<table><thead><tr><th>ID</th><th>Name</th><th>Address</th><th>City</th><th>Country</th><th>Geo</th><th>Connections</th></tr></thead>';
  print '<tbody>' . PHP_EOL;
  foreach ($result as $location) {
    print '<tr>' .
      '<td class="number">' . $location['id'] . '</td>' .
      '<td>' . htmlspecialchars($location['name']) . '</td>' .
      '<td>' . htmlspecialchars($location['address']) . '</td>' .
      '<td>' . htmlspecialchars($location['city']) . '</td>' .
      '<td>' . htmlspecialchars(getCountryName($location['country'])) . '</td>' .
      '<td>' . ($location['geo'] ? 'Yes' : '<b>No</b>') . '</td>' .
      '<td class="number">' . $location['connections'] . '</td>' .
      '<td><a href="locations.php?id=' . $location['id'] . '">[Edit]</a></td>' .
      '</tr>' . PHP_EOL;
  }
  print '<tr><td colspan="7"></td><td><a href="locations.php?action=new">[New location]</a></td></tr>';
  print '</tbody></table>';
}

function showLocation($id = null)
{
  global $this_type;
  if ($id) {
    $location = getrow("
			SELECT l.id, l.name, l.address, l.city, l.country, l.note, l.geo, ST_X(geo) AS latitude, ST_Y(geo) AS longitude, COUNT(lrel.id) AS connections
			FROM locations l
			LEFT JOIN lrel ON l.id = lrel.location_id
			WHERE l.id = $id
			GROUP BY l.id
			ORDER BY l.id, l.name
		");
    if (!$location) {
      print "Can't find id.";
      return false;
    }
  } else {
    $location = [];
  }
  $latlon = "";
  if ($location['geo']) {
    $latlon = $location['latitude'] . ", " . $location['longitude'];
  }
  $countryname = getCountryName($location['country']);
  $action = ($id ? 'updatelocation' : 'createlocation');
  $actionlabel = ($id ? 'Update location' : 'Create location');
  print '<div><a href="locations.php?action=new">New location</a> - <a href="locations.php" accesskey="w">All locations</a></div>';
  print '<form action="locations.php" method="post"><input type="hidden" name="action" value="' . $action . '"><input type="hidden" name="id" value="' . $location['id'] . '">';
  print '<table><tr><td>ID</td><td>' . $location['id'] . ($location['id'] ? ' - <a href="../locations?id=' . $location['id'] . '" accesskey="q">Show location page</a> - <a href="showlog.php?category=locations&data_id=' . $location['id'] . '">Show log</a>' : '') .
    trEdit('Name', 'name', $location['name'], true, '', 'namenote', '') .
    trEdit('Address', 'address', $location['address']) .
    trEdit('City', 'city', $location['city']) .
    trEdit('Country code', 'country', $location['country'], true, "Two letter ISO code, e.g.: se", "countrynote", $countryname) .
    trEdit('Coordinate', 'latlon', $latlon, true, "Latitude,Longitude (WGS84)") .
    trEdit('Note', 'note', $location['note']) .
    changealias($id, $this_type) .
    trEdit('Connections', 'connections', $location['connections'], false) .
    '<tr><td></td><td><input type="submit" value="' . $actionlabel . '">' . ($id ? ' <input type="submit" name="action" value="Delete" onclick="return confirm(\'Delete location?\n\nAs a safety mecanism it will be checked if all references are removed.\');" class="delete">' : '') . '</td></tr>' .
    '</table>';
  print '</form>' . PHP_EOL;

  if ($id) {
    print '<h2>Connections</h2>';
    print '<div><form action="locations.php" method="post"><input type="hidden" name="action" value="createrelation"><input type="hidden" name="id" value="' . $id . '">';
    print 'Add event: <input type="text" id="locationreference" name="locationreference" accesskey="a" placeholder="Convention or game run" size="60"> ';
    print '<input type="submit" value="Add relation to location">';
    print '</form></div>';
    if ($location['connections'] > 0) {
      print getConnections($id);
    }
  }
  // set up map
  print '<h2>Map</h2>' . PHP_EOL;
  print '<div id="map" style="height: 500px; width: 100%; border: 1px solid black; z-index: 90;"></div>';
  $longitude = 11;
  $latitude = 56;
  $zoom = 7;
  $marker = false;
  if ($location['geo']) {
    $longitude = $location['longitude'];
    $latitude = $location['latitude'] - 0.0001; // More south = in front of existing marker
    $zoom = 16;
    $marker = true;
  }
  $js = generateJSMapHTML($latitude, $longitude, $zoom, $marker);
  print $js;
  return true;
}

function showEvent($type, $id)
{
  if ($type == 'gamerun') {
    $game = getrow("
			SELECT g.id, g.title, gr.id AS gr_id, gr.location
			FROM gamerun gr
			INNER JOIN game g ON gr.game_id = g.id
			WHERE gr.id = $id
		");
    if (!$game) {
      print "Can't find gamerun id.";
      return false;
    }
    $event_locations = getall(
      "
			SELECT lrel.id AS lrel_id, l.id, l.name, l.city, l.country
			FROM lrel
			INNER JOIN locations l ON lrel.location_id = l.id
			WHERE lrel.gamerun_id = " . $id
    );
  } elseif ($type == 'convention') {
    $convention = getrow("
			SELECT c.id, c.name, c.year, c.place AS location
			FROM convention c
			WHERE c.id = $id
		");
    if (!$convention) {
      print "Can't find convention id.";
      return false;
    }
    $event_locations = getall(
      "
			SELECT lrel.id AS lrel_id, l.id, l.name, l.city, l.country
			FROM lrel
			INNER JOIN locations l ON lrel.location_id = l.id
			WHERE lrel.convention_id = " . $id
    );
  } else {
    print "Unknown type";
    return false;
  }
  print '<p><a href="locations.php?action=new">[New location]</a></p>';
  if ($type == 'gamerun') {
    print '<p>Locations for <a href="game.php?game=' . $game['id'] . '">' . htmlspecialchars($game['title']) . '</a>, <a href="run.php?id=' . $game['id'] . '" accesskey="q">game run #' . $game['gr_id'] . '</a><br><em>(' . htmlspecialchars($game['location']) . ')</em></p>';
  } else {
    print '<p>Locations for <a href="convention.php?con=' . $convention['id'] . '">' . htmlspecialchars($convention['name'] . ' (' . $convention['year'] . ')') . ' </a><br><em>(' . htmlspecialchars($convention['location']) . ')</em></p>';
  }

  print '<form action="locations.php" method="post"><input type="hidden" name="action" value="updateevent">';
  if ($type == 'gamerun') {
    print '<input type="hidden" name="gamerun_id" value="' . $id . '">' . PHP_EOL;
  } elseif ($type == 'convention') {
    print '<input type="hidden" name="convention_id" value="' . $id . '">' . PHP_EOL;
  }
  print '<table id="locationstable"><thead><tr><th>ID</th><th>Location</th></tr></thead><tbody>' . PHP_EOL;
  $event_locations[] = []; // New
  $rows = 0;
  foreach ($event_locations as $runlocation) {
    $rows++;
    if (!$runlocation['id']) {
      $label = '';
    } else {
      $label = $runlocation['id'] . ' - ' . $runlocation['name'];
      if ($runlocation['city']) {
        $label .= ', ' . $runlocation['city'];
      }
      if ($runlocation['country']) {
        $label .= ', ' . getCountryName($runlocation['country']);
      }
    }
    $idlabel = $runlocation['lrel_id'] ?? 'New';
    $autofocus = $runlocation['lrel_id'] ? '' : 'autofocus';
    print '<tr><td class="number">' . $idlabel . '</td><td><input type="text" placeholder="Location name" name="event_locations[]" value="' . htmlspecialchars($label) . '" style="width: 500px;" class="gamerunlocation" ' . $autofocus . '></td><td><a href="locations.php?id=' . $runlocation['id'] . '">[show location]</a></td></tr>' . PHP_EOL;
  }
  print '<tr><td></td><td><input type="submit" value="Update"></td></tr>';
  print '</tbody></table></form>' . PHP_EOL;
}

if ($action == 'new') {
  showLocation();
} elseif ($id) {
  showLocation($id);
} elseif ($gamerun_id) {
  showEvent('gamerun', $gamerun_id);
} elseif ($convention_id) {
  showEvent('convention', $convention_id);
} else {
  showLocations();
}
?>
<script>
  $(function() {
    $("#country").change(function() {
      $.get("lookup.php", {
        type: 'countrycode',
        label: $("#country").val()
      }, function(data) {
        $("#countrynote").text(data);
      });
    });
    $("#name").change(function() {
      $.get("lookup.php", {
        type: 'locationname',
        label: $("#name").val()
      }, function(data) {
        console.log('Data', data);
        if (data > 0) {
          $("#namenote").text("⚠ Note: A location with the same title already exists. You can still submit this new location.");
        } else {
          $("#namenote").text("");
        }
      });
    });

    $("input#locationreference").autocomplete({
      source: 'lookup.php?type=locationreference',
      autoFocus: true,
      minLength: 3,
      delay: 100
    })

    $("input.gamerunlocation").autocomplete({
      source: 'lookup.php?type=locationwithid',
      autoFocus: true,
      minLength: 3,
      delay: 100
    })

    $("input.gamerunlocation").change(function() {
      this.parentElement.nextSibling.firstChild.href = 'locations.php?id=' + parseInt(this.value);
    });

  });
</script>

<?php

print "</body>\n</html>\n";
