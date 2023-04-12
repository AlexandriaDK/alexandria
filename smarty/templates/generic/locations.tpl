{assign var="pagetitle" value="Locations"}
{include file="head.tpl"}

<div id="content">
	<h2 class="pagetitle">
		Locations
	</h2>

	<div id="map" style="height: 700px; width: 100%;">
<script>
var locations = {$locations};
var startlocation = {$startlocation};
var start_id = {$start_id};
var convention_id = {$convention_id};
var conset_id = {$conset_id};
{literal}
if (startlocation) {
	startlocation.zoom = 16;
} else {
	startlocation = {'latitude': 56, 'longitude': 11, 'zoom': 5}
}
var map = L.map('map', { fullscreenControl: true} ).setView([startlocation.latitude, startlocation.longitude], startlocation.zoom);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var highlightIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

for(place_id in locations) {
	var place = locations[place_id];
	if (place.data.hasGeo) {
		var highlight = false;
		var markerText = '<a href="locations?id=' + place_id + '"><b>' + place.data.name + '</b></a><br>';
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
			markerText += `<a href="${link}" class="${className} ${classCancelled}">${div.innerHTML}</a><br>`;
			if (event.type == 'convention' && (event.data_id == convention_id || event.conset_id == conset_id) ) {
				highlight = true;
			}
		}
		if (place.data.note) {
			var div = document.createElement('div');
			var node = document.createTextNode(place.data.note);
			div.appendChild(node);
			markerText += '<br><span class="locationnote">' + div.innerHTML + '<br> </span>';
		}
		if (start_id == place_id || highlight == true) {
			var marker = L.marker([place.data.latitude, place.data.longitude], {icon: highlightIcon}).addTo(map);
			marker.bindPopup(markerText).openPopup();
		} else {
			var marker = L.marker([place.data.latitude, place.data.longitude]).addTo(map);
			marker.bindPopup(markerText);
		}
	}
}
{/literal}
</script>

</div>

{if $start_id}
{assign "id" $start_id}
{/if}
{include file="updatelink.tpl"}
{include file="end.tpl"}
