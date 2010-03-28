// lot marker HTML
var html = "<b>{lotName}</b><br>" +
	"<i>{lotDescription}</i><br>" +
	"<br>" +
	"<u>Current Acceptable Pass Types</u><br>" +
	"{currentPassTypes}";
	
// map options (will eventually come from database)
var myOptions = {
	zoom: 14,
	center: new google.maps.LatLng(34.6825, -82.8379),
	navigationControl: true,
	navigationControlOptions: { style: google.maps.NavigationControlStyle.SMALL },
	mapTypeControl: true,
	mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
	scaleControl: false,
	mapTypeId: google.maps.MapTypeId.ROADMAP
}

function initialize() {
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}

function createPolygon(index, paths, strokeColor, strokeOpacity, strokeWeight, fillColor, fillOpacity) {
	var lot = new google.maps.Polygon({
		paths: paths,
		strokeColor: strokeColor,
		strokeOpacity: strokeOpacity,
		strokeWeight: strokeWeight,
		fillColor: fillColor,
		fillOpacity: fillOpacity});
	lot.setMap(map);
	return lot;
}
function populateHTML(lotName, lotDescription, currentPassTypes) {
	/* LOT HTML
	var html = 
		"<b>{lotName}</b><br>" +
		"<i>{lotDescription}</i><br>" +
		"<br>" +
		"<u>Current Acceptable Pass Types</u><br>" +
		"{currentPassTypes}";
	*/
	var output = html;
	output = output.replace("{lotName}", lotName);
	output = output.replace("{lotDescription}", lotDescription);
	output = output.replace("{currentPassTypes}", currentPassTypes);
	return output;
}