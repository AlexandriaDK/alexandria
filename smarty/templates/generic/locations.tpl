{assign var="pagetitle" value="{$_locations_title}"}
{include file="head.tpl"}

<div id="content">
{include file="originalsearch.tpl"}

	<h2 class="pagetitle">
		{$_locations_title}
	</h2>

	{if $location_target}
	<h3>
		{$_locations_for|sprintf:$location_target}
	</h3>
	{/if}

	<div id="map" style="height: 700px; width: 100%; border: 1px solid black; z-index: 90; margin-top: 10px;">
<script>
var locations = {$locations};
var startlocation = {$startlocation};
var start_id = {$start_id};
var bounds = [];

{literal}
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

if (startlocation.length > 0) { // Find bbox
	for (location_id of startlocation) {
		bounds.push([locations[location_id].data.latitude, locations[location_id].data.longitude]);
	}
}
if (bounds.length > 0) {
	var map = L.map('map', {
		fullscreenControl: true,
		layers: [osmLayer]
	}).fitBounds(bounds, { maxZoom: 15 });
} else {
	var map = L.map('map', {
		fullscreenControl: true,
		center: [56, 11],
		zoom: 5,
		layers: [osmLayer]
	});
}

var baseMaps = {
	"OpenStreetMap": osmLayer,
	"Aerial imagery (Denmark only)": wmsOrtoLayer,
	"Spinal Map": Thunderforest_SpinalMap,
}

L.Control.geocoder({placeholder: 'Search for address...', showResultIcons: true}).addTo(map);
var layerControl = L.control.layers(baseMaps).addTo(map);
L.control.scale().addTo(map);

var highlightIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

var smallIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
  iconSize: [13, 21],
  iconAnchor: [6, 21],
  popupAnchor: [1, -17],
  shadowSize: [21, 21]
});

var markerGroups = {};

for(place_id in locations) {
	var place = locations[place_id];
	if (place.data.hasGeo) {
		var countrycode = place.data.countrycode.toLowerCase() || 'xxx';
		if (!(countrycode in markerGroups)) {
			markerGroups[countrycode] = L.markerClusterGroup();
		}
		var clusterGroup = markerGroups[countrycode];
		var highlight = false;
		var markerText = '<a href="locations?id=' + place_id + '"><b>' + place.data.name + '</b></a><br>';
		if (place.data.aliases) {
			markerText += '<span class="locationnote">(' + {/literal}'{$_aka|escape}'{literal} + ': ' + place.data.aliases + ')<br></span>'; // escape!
		}
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
			var link = (event.type == 'convention' ? 'data?con=' : 'data?scenarie=') + event.data_id;
			var className = (event.type == 'convention' ? 'con' : 'game');
			var classCancelled = (event.data_cancelled == '1' ? 'cancelled' : '');
			var div = document.createElement('div');
			var node = document.createTextNode(event.data_label);
			div.appendChild(node);
			markerText += `<a href="${link}" class="${className} ${classCancelled}" title="${event.nicedateset}">${div.innerHTML}</a><br>`;
		}
		if (place.data.note) {
			var div = document.createElement('div');
			var node = document.createTextNode(place.data.note);
			div.appendChild(node);
			markerText += '<br><span class="locationnote">' + div.innerHTML + '<br> </span>';
		}
		if (startlocation.includes(place_id.toString())) {
			var marker = L.marker([place.data.latitude, place.data.longitude], {icon: highlightIcon}).addTo(map);
			marker.bindTooltip(place.data.name).bindPopup(markerText);
			if (bounds.length == 1) {
				marker.openPopup(); // only open if exactly one location
			}
		} else {
			var marker = L.marker([place.data.latitude, place.data.longitude], {icon: smallIcon});
			marker.bindTooltip(place.data.name).bindPopup(markerText);
			clusterGroup.addLayer(marker);
		}
	}
}
for (markerGroup in markerGroups) {
	map.addLayer(markerGroups[markerGroup]);
}
{/literal}
</script>

</div>

{if $start_id}
{assign "id" $start_id}
{/if}
{include file="updatelink.tpl"}
{include file="end.tpl"}
