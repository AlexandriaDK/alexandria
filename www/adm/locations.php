<?php
require "adm.inc.php";
require "base.inc.php";
chdir("..");
require "rpgconnect.inc.php";
require "base.inc.php";
$this_type = 'locations';

$id = (int) $_REQUEST['id'];
$action = (string) $_REQUEST['action'];
$name = (string) $_REQUEST['name'];
$address = (string) $_REQUEST['address'];
$city = (string) $_REQUEST['city'];
$country = (string) $_REQUEST['country'];
$note = (string) $_REQUEST['note'];
$latlon = (string) $_REQUEST['latlon'];
$locationreference = (string) $_REQUEST['locationreference'];
$relation_id = (int) $_REQUEST['relation_id'];
$new = FALSE;

if ($action == "createlocation") {
	doquery("INSERT INTO locations (name, address, city, country, note) VALUES ('','','','','')");
	$id = dbid();
	$action = "updatelocation";
	$new = TRUE;
}

// Edit location
if ($action == "updatelocation" && $id) {
	[$latitude,$longitude] = explode(",",$latlon);
	$geosql = 'NULL';
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
	rexit( $this_type, [ 'id' => $id ] );
}

// Delete location
if ($action == "Delete" && $id) {
	$error = [];
	if (getCount('lrel', $id, FALSE, 'location')) $error[] = "convention/game run";
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
	$convention_id = $gamerun_id = NULL;
	if ($matches['1'] == 'c') {
		$convention_id = $matches[2];
	} elseif ($matches['1'] == 'gr') {
		$gamerun_id = $matches[2];
	}
	// no duplicates
	$existing = 0;
	if ($convention_id) {
		$existing = getone("SELECT COUNT(*) FROM lrel WHERE location_id = $id AND convention_id = $convention_id AND gamerun_id IS NULL");
	} elseif ($gamerun_id) {
		$existing = getone("SELECT COUNT(*) FROM lrel WHERE location_id = $id AND convention_id IS NULL AND gamerun_id = $gamerun_id");
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
	if (! getone("SELECT COUNT(*) FROM lrel WHERE location_id = $id AND id = $relation_id") ) {
		$_SESSION['admin']['info'] = "The relation does not exist. " . dberror();
		rexit($this_type, ['id' => $id]);
	}
	$relationstring = getone("SELECT IF(convention_id IS NOT NULL, CONCAT('c', convention_id), CONCAT('gr', gamerun_id)) AS relationstring FROM lrel WHERE location_id = $id AND id = $relation_id");
	doquery("DELETE FROM lrel WHERE location_id = $id AND id = $relation_id");
	chlog($id, $this_type, "Relation removed: $relationstring");
	$_SESSION['admin']['info'] = "Relation removed! " . dberror();
	rexit($this_type, ['id' => $id]);
}

$head = '
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
     integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
     crossorigin=""></script>
';

htmladmstart("Locations", $head);

function trEdit($label, $attribute, $value, $editable = TRUE, $placeholder = "", $extra_field_id = "", $extra_field_value = "") {
	$html = '<tr>' . 
	        '<td>' . htmlspecialchars($label) . '</td>';
	if ($editable) {
		$html .= '<td><input type="text" size="50" name="' . htmlspecialchars($attribute) .'" id="' . htmlspecialchars($attribute) .'" value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '"></td>' . 
			($extra_field_id ? '<td id="' . $extra_field_id . '">' . htmlspecialchars($extra_field_value) . '</td>' : '') .
			'</tr>' . PHP_EOL;
	} else {
		$html .= '<td>' . htmlspecialchars($value) . '</td>';
	}
	return $html;
}

function generateJSMapHTML($latitude, $longitude, $zoom, $marker) {
	$js = <<<EOD
	<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
	<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
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
	
var map = L.map('map').setView([$latitude, $longitude], $zoom);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
L.Control.geocoder().addTo(map);
map.on('click', onMapClick);
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

function getConnections($id) {
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
	$html .= '<table><thead><tr><th>Name</th><th>Date</th><th>Type</th><th>Noted location</th></tr></thead><tbody>' . PHP_EOL;
	foreach($connections AS $connection) {
		$type = $name = $editlink = '';
		if ($connection['convention_id']) {
			$type = 'convention';
			$name = $connection['name'] . " (" . ($connection['year'] ? yearname($connection['year']) : '?') . ")";
			$editlink = 'convention.php?con=' . $connection['convention_id'];
		} elseif ($connection['gamerun_id']) {
			$type = 'gamerun';
			$name = $connection['title'];
			$editlink = 'run.php?id=' . $connection['game_id'];
		}
		$date = fulldate($connection['starttime']);
		$deleteurl = 'locations.php?action=removerelation&id=' . $id . '&relation_id=' . $connection['id'];

		$html .= '<tr>' . 
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

function showLocations() {
	$result = getall("
		SELECT l.id, l.name, l.address, l.city, l.country, l.geo, COUNT(lrel.id) AS connections
		FROM locations l
		LEFT JOIN lrel ON l.id = lrel.location_id
		GROUP BY l.id
		ORDER BY l.id, l.name
	");
	print '<table><thead><tr><th>ID</th><th>Name</th><th>Address</th><th>City</th><th>Country</th><th>Geo</th><th>Connections</th></tr></thead>';
	print '<tbody>' . PHP_EOL;
	foreach ($result AS $location) {
		print '<tr>' .
		'<td class="number">' . $location['id'] . '</td>' .
		'<td>' . htmlspecialchars($location['name']) . '</td>' .
		'<td>' . htmlspecialchars($location['address']) . '</td>' .
		'<td>' . htmlspecialchars($location['city']) . '</td>' .
		'<td>' . htmlspecialchars(getCountryName($location['country'])) . '</td>' .
		'<td>' . ($location['geo'] ? 'Yes' : '<b>No</b>' ) . '</td>' .
		'<td class="number">' . $location['connections'] . '</td>' .
		'<td><a href="locations.php?id=' . $location['id'] . '">[Edit]</a></td>' .
		'</tr>' . PHP_EOL;
	}
	print '<tr><td colspan="7"></td><td><a href="locations.php?action=new">[New location]</a></td></tr>';
	print '</tbody></table>';
}

function showLocation($id = NULL) {
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
	print '<table><tr><td>ID</td><td>' . $location['id'] . ($location['id'] ? ' - <a href="../locations?id=' . $location['id'] . '" accesskey="q">Show location page</a>' : '') .	
	trEdit('Name', 'name', $location['name'], TRUE, '', 'namenote', '') .
	trEdit('Address', 'address', $location['address']) .
	trEdit('City', 'city', $location['city']) .
	trEdit('Country code', 'country', $location['country'], TRUE, "Two letter ISO code, e.g.: se", "countrynote", $countryname) .
	trEdit('Coordinate', 'latlon', $latlon, TRUE, "Latitude,Longitude (WGS84)") .
	trEdit('Note', 'note', $location['note']) .
	trEdit('Connections', 'connections', $location['connections'], FALSE) . 
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
	print '<div id="map" style="height: 500px; width: 100%; border: 1px solid black;"></div>';
	$longitude = 11;
	$latitude = 56;
	$zoom = 7;
	$marker = FALSE;
	if ($location['geo']) {
		$longitude = $location['longitude'];
		$latitude = $location['latitude'];
		$zoom = 16;
		$marker = TRUE;
	}
	$js = generateJSMapHTML($latitude, $longitude, $zoom, $marker);
	print $js;


	return TRUE;
}

if ($action == 'new') {
	showLocation();
} elseif ($id) {
	showLocation($id);
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

	$( "input#locationreference" ).autocomplete({
		source: 'lookup.php?type=locationreference',
		autoFocus: true,
		minLength: 3,
		delay: 100
	})
});
</script>

<?php

print "</body>\n</html>\n";
