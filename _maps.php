<script type="text/javascript">
var apiURL = "./api.php";
var lots = false;

// get lots from webservice
function loadLots() {
	$.getJSON(apiURL + "?function=GetLots",
		function(data) {
			lots = data;
			createLots();
		});
}

// returns converted DB coords into array of google LatLng
function convertCoords(coords) {
	var output  = new Array();
	$.each(coords, function(i) {
		var latLng = (coords[i].split(","));
		var lat = latLng[0];
		var lng = latLng[1];
		output[output.length] = new google.maps.LatLng(lat, lng);
	});
	return output;
}

// creates polygons from lot data
function createLots() {
	$.each(lots, function(i) {
		var lot = lots[i];
		
		var scheme = lot.scheme;
		var coords = convertCoords(lot.coords);
		var middle = lot.middle.split(",");
		
		createPolygon(coords, 
			scheme.lineColor,
			scheme.lineWidth,
			scheme.lineOpacity,
			scheme.fillColor,
			scheme.fillOpacity);
			
		alert("middle (" + middle[0] + "," + middle[1] + ")");
		
		createMarker(
			new google.maps.LatLng(middle[0], middle[1]),
			lot.name,
			"<b>" + lot.name + "</b>");
	});
}
	
// map options
var myOptions = {
	zoom: <?php echo $globalSettings["mapZoom"]; ?>,
	center: new google.maps.LatLng(<?php echo $globalSettings["mapCenter"]; ?>),
	mapTypeId: <?php echo $globalSettings["mapTypeId"]; ?>,
	navigationControl: true,
	navigationControlOptions: { style: google.maps.NavigationControlStyle.SMALL },
	mapTypeControl: true,
	mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
	scaleControl: false
}

// define icon
var image = new google.maps.MarkerImage(
	'<?php echo $globalSettings["markerImage"]; ?>', // URL
	new google.maps.Size(24, 24), // icon size
	new google.maps.Point(0,0), // origin for the image is 0,0
	new google.maps.Point(0, 24) // anchor for the image is base of paw at 0,24
);
var shadow = new google.maps.MarkerImage(
	'<?php echo $globalSettings["markerShadow"]; ?>', // URL
	new google.maps.Size(37, 34), // icon size
	new google.maps.Point(0, 0), // origin ?
	new google.maps.Point(9, 34) // anchor ?
);
var shape = {
	coord: [1, 1, 1, 20, 18, 20, 18 , 1],
	type: 'poly' 
}

var map;
var lotPolygons = new Array();
var lotMarkers = new Array();
var lotInfoWindow = new Array();

function initialize() {
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}

function createPolygon(paths, strokeColor, strokeOpacity, strokeWeight, fillColor, fillOpacity) {
	var lot = new google.maps.Polygon({
		paths: paths,
		strokeColor: strokeColor,
		strokeOpacity: strokeOpacity,
		strokeWeight: strokeWeight,
		fillColor: fillColor,
		fillOpacity: fillOpacity});
	lot.setMap(map);
	lotPolygons[lotPolygons.length] = lot;
	return lot;
}

function createMarker(position, name, html) {
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		shadow: shadow,
		icon: image,
		shape: shape,
		title: name
	});
	// zIndex: index
	
	var infoWindow = new google.maps.InfoWindow({
		content: html
	});
	
	google.maps.event.addListener(marker, "mouseover",
		function() {
			for (x in lotInfoWindow) lotInfoWindow[x].close();
			infoWindow.open(map, marker);
		});
	google.maps.event.addListener(marker, "click",
		function() { infoWindow.close(); } );

	marker.setMap(map);
	lotInfoWindow[lotInfoWindow.length] = infoWindow;
	lotMarkers[lotMarkers.length] = marker;
	return marker;
}

function displayAllLots() {
	loadLots();
}
</script>
