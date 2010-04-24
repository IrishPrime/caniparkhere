// maps.js
// loadin' and doing sweet google map stuff since 2010

// google map objects
var map;
var lotPolygons = new Array();
var lotMarkers = new Array();
var lotInfoWindow = new Array();

// webservice objects
var apiURL = "./api.php";
var lots = false;
var settings = false;
var passTypes = false;

// editable lot objects
var isClosed = false;
var editLot = false; // reference to polyline/polygon object
var oldEditPoints = new google.maps.MVCArray(); // holds reference to original polygon shape for edited lots
var editPoints = new google.maps.MVCArray(); // reference to points of polygon
var editMarkers = []; // holds the edit markers for polygon

// GOOGLE MAPS methods
// starts google map on document element "map_canvas"
function initialize() {
	// split latLng up for map center
	var mapCenter = settings.mapCenter;
	
	var latLng = (mapCenter.split(","));
		var lat = latLng[0];
		var lng = latLng[1];
		
	var myOptions = {
		zoom: parseInt(settings.mapZoom),
		center: new google.maps.LatLng(parseFloat(lat), parseFloat(lng)),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		//mapTypeId: settings.mapTypeId,
		navigationControl: true,
		navigationControlOptions: { style: google.maps.NavigationControlStyle.SMALL },
		mapTypeControl: true,
		mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
		scaleControl: false,
		disableDoubleClickZoom: true
		//scrollwheel: false
	}
	
	// create map on page
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	//google.maps.event.addListener(map, 'dblclick', function() { log("!");});
	
	
	google.maps.event.addListener(map, 'click', function(point) {
		var lotName = null;
		var inPolygon = false;
		
		for (x in lotPolygons) {
			if (isInPolygon(lotPolygons[x], point.latLng)) {
				alert("You're in a lot!");
				inPolygon = true;
				break;
			}
		}
		if (!inPolygon) alert("You're outside. :-(");
		
		
		// === A method for testing if a point is inside a polygon
		// === Returns true if poly contains point
		// === Algorithm shamelessly stolen from http://alienryderflex.com/polygon/ 
		/*
		function isInPolygon(polygon, point) {
			var j = 0;
			var oddNodes = false;
			var x = point.lng();
			var y = point.lat();
			var path = polygon.getPath();
			
			for (var i=0; i < path.getLength(); i++) {
			  var pathPoint = path.getAt(i);
			  var lat = pathPoint.lat();
			  var lng = pathPoint.lng();
			  j++;
			  
			  if (j == path.getLength()) {j = 0;}
			  if (((lat < y) && (lat >= y)) || ((lat < y) && (lat >= y))) {
				if (lng + (y - lat) /  (lat - lat) *  (lng - lng) < x ) {
				  oddNodes = !oddNodes
				}
			  }
			}
			return oddNodes;
		} */
	});
}
// creates specified polygon on map
// reference stored in lotPolygons
function createPolygon(id, paths, strokeColor, strokeOpacity, strokeWeight, fillColor, fillOpacity) {
	var lot = new google.maps.Polygon({
		map: map,
		paths: paths,
		strokeColor: strokeColor,
		strokeOpacity: strokeOpacity,
		strokeWeight: strokeWeight,
		fillColor: fillColor,
		fillOpacity: fillOpacity});
		
	lotPolygons[id] = lot;
}
// creates marker on map with an info window (HTML)
// references stored in lotMarkers, lotInfoWindow
// mouseover pops up infowindow closing other opens ones
// left clicking will close current info window
function createInfoMarker(id, position, name, html) {
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		title: name
	});
	
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

	lotInfoWindow[id] = infoWindow;
	lotMarkers[id] = marker;
}
// creates marker on map with edit options
// left click turns lot editable
// right click deletes lot
function createEditMarker(id, position, name) {
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		title: name
	});
	
	// click first index to close lot
	google.maps.event.addListener(marker, "click",
		function() {
			if (editLot) closeLot();
		});

	lotMarkers[id] = marker;
}
// creates moveable point marker for editing lots
// if first point, left click closes lot
// dragend changes associated editPoints to new marker position
function createPointMarker(position) {
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		title: "Drag me!",
		draggable: true
	});

	google.maps.event.addListener(marker, "dragend",
		function() {
			for (var i = 0, I = editMarkers.length; i < I && editMarkers[i] != marker; ++i);
			editPoints.setAt(i, marker.getPosition());
			if (isClosed) uncloseLot();
		});
		
	google.maps.event.addListener(marker, "rightclick",
		function() {
			for (var i = 0, I = editMarkers.length; i < I && editMarkers[i] != marker; ++i);
			if (editPoints.length > 0) {
				editPoints.removeAt(i);
				if (editMarkers.length > 0) {
					editMarkers[i].setMap(null);
					editMarkers.splice(i, 1);
				}
				if (isClosed) uncloseLot();
			}
		});
	
	editMarkers.push(marker);
}
// if no lot is in edit mode, creates starting point for new lot
// else will add point end of edit lot, IF lot is not already closed (base case)
function addNewPoint(point) {
	if (!editLot) startLot();
	if (!isClosed) {
		// insert marker at new point
		editPoints.insertAt(editPoints.length, point.latLng);
		createPointMarker(point.latLng);
	}
}
// starts a new lot on map
function startLot() {
	isNew = true;
	editPoints = new google.maps.MVCArray();
	editLot = new google.maps.Polyline();
	editLot.setMap(map);
	editLot.setPath(new google.maps.MVCArray([editPoints]));
}
// closes editing lot
function closeLot() {
	if (!isClosed && editLot && editPoints.length > 2) {
		isClosed = true;
		editLot = new google.maps.Polygon();
		editLot.setPaths(new google.maps.MVCArray([editPoints]));
		editLot.setMap(map);
	}
	writeCoords(false);
}
// reopens editing lot
function uncloseLot() {
	if (isClosed) {
		editLot.setMap(null);
		isClosed = false;
		editLot = new google.maps.Polyline();
		editLot.setPath(new google.maps.MVCArray([editPoints]));
		editLot.setMap(map);
	}
	writeCoords(true);
}
// clears current lot
function clearLot() {
	isClosed = false;
	editLot = false;
	editMarkers = [];
	editPoints = [];
	if (editLot) editLot.setMap(null);
}
// save coords in DB format to hidden element on page
function writeCoords(clearAll) {
	var output = "";
	
	if (!clearAll) {	
		for (var i = 0; i < editPoints.getLength(); i++) {
			var point = editPoints.getAt(i);
			output += point.lat().toFixed(6) + "," +
				point.lng().toFixed(6);
			if (i + 1 != editPoints.getLength()) output += ";";
		}
	}
	
	document.getElementById("lot_coords").value = output;
}

// undoes all other lot changes and turns lot editable
// 0 will just clear all other lot data
function SelectLot(id){
	// first, clear all edit lot data
	isClosed = false;
	editLot = false;
	for (var i = 0; i < editMarkers.length; i++) {
			editMarkers[i].setMap(null);
	}
	editPoints = [];
	editMarkers = [];

	if (id > 0) {
		isClosed = true;
		editLot = lotPolygons[id];
		editPoints = editLot.getPath();
		oldEditPoints = editLot.getPath(); // make copy
		// create point markers
		for (var i = 0; i < editPoints.getLength(); i++) {
			var position = editPoints.getAt(i);
			createPointMarker(position);
		}
	}
}
// returns if a current lot is being edited, and if it has more than 2 points
function lotPolyClosed() {
	return true;
}


// creation methods loop through data
// and modify data to send to create methods
function createLotPolygons() {
	$.each(lots, function(i) {
		var lot = lots[i];
		var id = lot.id;
		var scheme = lot.scheme;
		var coords = new Array();
		// convert coords into google LatLng
		$.each(lot.coords, function(i) {
			var latLng = (lot.coords[i].split(","));
			var lat = parseFloat(latLng[0]);
			var lng = parseFloat(latLng[1]);
			coords.push(new google.maps.LatLng(lat, lng));
		});
		
		createPolygon(id, coords, 
			scheme.lineColor, scheme.lineWidth, scheme.lineOpacity,
			scheme.fillColor, scheme.fillOpacity);
	});
}
function createLotPolygonsByPassType(passType) {
	$.each(lots, function(i) {
		var lot = lots[i];
		var id = lot.id;
		var scheme = lot.scheme;
		var coords = new Array();
		// convert coords into google LatLng
		$.each(lot.coords, function(i) {
			var latLng = (lot.coords[i].split(","));
			var lat = parseFloat(latLng[0]);
			var lng = parseFloat(latLng[1]);
			coords.push(new google.maps.LatLng(lat, lng));
		});
		var currentPassTypes = lot.currentPassTypes;
		
		// only create polygons if passType is in current passes
		if (currentPassTypes != null) {
			$.each(currentPassTypes, function(i) {
				if (currentPassTypes[i].id == passType) {
					createPolygon(id, coords, 
						scheme.lineColor, scheme.lineWidth, scheme.lineOpacity,
						scheme.fillColor, scheme.fillOpacity);
				}
			});
		}
	});
}
function createLotInfoMarkers() {
	$.each(lots, function(i) {
		var lot = lots[i];
		var id = lot.id;
		var middle = lot.middle.split(",");
		var lat = parseFloat(middle[0]);
		var lng = parseFloat(middle[1]);
					
		createInfoMarker(id, new google.maps.LatLng(lat, lng),
		lot.name, populateLotHTML(lot.name, lot.description, lot.currentPassTypes));
	});
}
function createLotInfoMarkersByPassType(passType) {
	$.each(lots, function(i) {
		var lot = lots[i];
		var middle = lot.middle.split(",");
		var lat = parseFloat(middle[0]);
		var lng = parseFloat(middle[1]);
		var currentPassTypes = lot.currentPassTypes;		
		
		if (currentPassTypes != null) {
			$.each(currentPassTypes, function(i) {
				if (currentPassTypes[i].id == passType) {
					createInfoMarker(
					new google.maps.LatLng(lat, lng),
					lot.name,
					populateLotHTML(lot.name, lot.description, lot.currentPassTypes));
				}
			});
		}
	});
}
function populateLotHTML(name, desc, passTypes) {
	var html = settings.lotHTML;
	html = html.replace("{lotName}", name);
	html = html.replace("{lotDescription}", desc);

	var text = "";
	if (passTypes != null) {
		$.each(passTypes, function(i) {
			text += passTypes[i].name + "<br>";
		});	
	}
	else text = "Lot is currently closed.";
	html = html.replace("{currentPassTypes}", text);
	
	return html;
}
function createLotEditMarkers() {
	$.each(lots, function(i) {
		var lot = lots[i];
		var id = lot.id;
		var middle = lot.middle.split(",");
		var lat = parseFloat(middle[0]);
		var lng = parseFloat(middle[1]);
		
		createEditMarker(id, new google.maps.LatLng(middle[0], middle[1]), lot.name);
	});
}
function createWDIPMarker(latLng) {
		createInfoMarker(
			latLng,
			"You parked here!",
			"You last parked at " + latLng.toString() + "!");
}

function LoadMap_CIPH() {
	$.getJSON(apiURL + "?function=GetSettingsForUser&id=0",
		function(data) {
			settings = data; // store settings, now create lots
			initialize();
			
			/*// for GetCurrentLot test
			google.maps.event.addListener(map, 'click', function(point) {
				latLng = point.latLng;
				lat = latLng.lat().toFixed(6);
				lng = latLng.lng().toFixed(6);
				$.getJSON(apiURL + "?function=GetCurrentLot&point=" + lat + "," + lng, function(data) {
					var lotId = data;
					if (lotId != null) {
						$.each(lots, function(i) {
							var lot = lots[i];
							if (lot.id == lotId) {
								alert("You're in lot " + lot.name + "!");
								return false;
							}
						});
					}
				});
			}); */
			
			// get lot data
			$.getJSON(apiURL + "?function=GetLots",
				function(data) {
					lots = data; // store lot data, move to settings
					createLotPolygons(); // grabs lot data
					createLotInfoMarkers();
				});
		});
}
function LoadMap_WCIP(passType) {
	$.getJSON(apiURL + "?function=GetSettingsForUser&id=0",
		function(data) {
			settings = data; // store settings, now create lots
			initialize();
			
			// get lot data
			$.getJSON(apiURL + "?function=GetLots",
				function(data) {
					lots = data; // store lot data, move to settings
					createLotPolygonsByPassType(passType);
					createLotInfoMarkersByPassType(passType);
				});
		});
}
function LoadMap_WDIP(user) {
	$.getJSON(apiURL + "?function=GetSettingsForUser&id=0",
		function(data) {
			settings = data; // store settings, now create lots
			initialize();
			
			// get lot data
			$.getJSON(apiURL + "?function=GetLots",
				function(data) {
					lots = data; // store lot data, move to settings
					createLotPolygons(); // grabs lot data
					createLotInfoMarkers();
					
					$.getJSON(apiURL + "?function=WhereDidIPark&id=" + user,
						function(data) {
							var point = data.lastLoc.split(",");
							var lat = parseFloat(point[0]);
							var lng = parseFloat(point[1]);
							lastLoc = new google.maps.LatLng(lat, lng);
							
							createWDIPMarker(lastLoc);
							map.panTo(lastLoc);
						});
				});
		});
}
function LoadMap_Edit() {
	$.getJSON(apiURL + "?function=GetSettingsForUser&id=0",
		function(data) {
			settings = data; // store settings, now create lots
			
			initialize();
			// catch specific events
			google.maps.event.addListener(map, 'click', addNewPoint);
			
			// get lot data
			$.getJSON(apiURL + "?function=GetLots",
				function(data) {
					lots = data; // store lot data, move to settings
					createLotPolygons();
					createLotEditMarkers();
				});
		});
}