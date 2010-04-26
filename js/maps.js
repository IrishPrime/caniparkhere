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
var allowedLots = false;
var settings = false;
var passTypes = false;

// editable lot objects
var isClosed = false;
var editLot = false; // reference to polyline/polygon object
var selectedLotId = 0; // start with no lot selected
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
	
	/*
	google.maps.event.addListener(map, 'click', function(point) {
		var lotName = null;
		var inPolygon = false;
		
		for (x in lotPolygons) {
			if (isInPolygon(lotPolygons[x], point.latLng)) {
				//alert("You're in " + lots[x].name + "!");
				inPolygon = true;
				break;
			}
		}
		//if (!inPolygon) alert("You're outside. :-(");
		
		
		// === A method for testing if a point is inside a polygon
		// === Returns true if poly contains point
		// === Algorithm shamelessly stolen from http://alienryderflex.com/polygon/
		
		function isInPolygon(polygon, point) {
			var j = 0;
			var oddNodes = false;
			var x = point.lng();
			var y = point.lat();
			var path = polygon.getPath();
			
			for (var i=0; i < path.getLength(); i++) {
			  j++;
			  if (j == path.getLength()) {j = 0;}
			  
			  var pathPoint = path.getAt(i);
			  var iLat = pathPoint.lat();
			  var iLng = pathPoint.lng();
			  pathPoint = path.getAt(j);
			  var jLat = pathPoint.lat();
			  var jLng = pathPoint.lng();
			  
			  if (((iLat < y) && (jLat >= y))
				|| ((jLat < y) && (iLat >= y))) {
				if (iLng + (y - iLat) /  (jLat - iLat) *  (jLng - iLng) < x ) {
				  oddNodes = !oddNodes
				}
			  }
			}
			return oddNodes;
		}
	}); */
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
	
	if (id == 0) marker.setIcon("http://www.google.com/mapfiles/arrow.png");
	else marker.setIcon("http://labs.google.com/ridefinder/images/mm_20_blue.png");
	
	var infoWindow = new google.maps.InfoWindow({
		content: html
	});
	
	google.maps.event.addListener(marker, "mouseover",
		function() {
			if (id > 0) {
				for (x in lotInfoWindow) lotInfoWindow[x].close();
				infoWindow.open(map, marker);
			}
		});
	google.maps.event.addListener(marker, "click",
		function() { infoWindow.close(); } );

	if (id > 0) {
		lotInfoWindow[id] = infoWindow;
		lotMarkers[id] = marker;
	}
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
	
	marker.setIcon("http://labs.google.com/ridefinder/images/mm_20_blue.png");
	
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

	marker.setIcon("http://labs.google.com/ridefinder/images/mm_20_red.png");
	
	google.maps.event.addListener(marker, "click",
		function() {
			if (!isClosed) closeLot();
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
	// before updating selectedLotId
	// use it to reset any edited path
	// and reclose edited lot
	/*
	var coords = new Array();
	if (editLot && selectedLotId > 0) {
		lot = lots[selectedLotId];
		$.each(lot.coords, function(i) {
			var latLng = (lot.coords[i].split(","));
			var lat = parseFloat(latLng[0]);
			var lng = parseFloat(latLng[1]);
			coords.push(new google.maps.LatLng(lat, lng));
		});
		newPoints = new google.maps.MVCArray(coords);
		lotPolygons[selectedLotId].setPaths(new google.maps.MVCArray([newPoints]));
		lotPolygons[selectedLotId].setMap(map);
		if (!isClosed) closeLot();
	} */
	
	// clear any and all edit lot data
	isClosed = false;
	//if (editLot) editLot.setMap(null);
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
		// create point markers
		for (var i = 0; i < editPoints.getLength(); i++) {
			var position = editPoints.getAt(i);
			createPointMarker(position);
		}
		writeCoords(false);
	}
	
	// copy new id
	selectedLotId = id;
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
function createLotPolygonsByPassType() {
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
		//var currentPassTypes = lot.currentPassTypes;
		
		// only display lots if they're within allowedLots
		$.each(allowedLots, function(i) {
			if (i == id) {
				createPolygon(id, coords, 
					scheme.lineColor, scheme.lineWidth, scheme.lineOpacity,
					scheme.fillColor, scheme.fillOpacity);
			}
		});
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
function createLotInfoMarkersByPassType() {
	$.each(lots, function(i) {
		var lot = lots[i];
		var id = lot.id;
		var middle = lot.middle.split(",");
		var lat = parseFloat(middle[0]);
		var lng = parseFloat(middle[1]);
		var currentPassTypes = lot.currentPassTypes;		
		
		// only display lots if they're within allowedLots
		$.each(allowedLots, function(i) {
			if (i == id) {
				createInfoMarker(id, new google.maps.LatLng(lat, lng),
					lot.name, populateLotHTML(lot.name, lot.description, lot.currentPassTypes));
			}
		});
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
	createInfoMarker(0, latLng, "You parked @ " +  latLng.toString(), "");
}

function LoadMap_CIPH(passType) {
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
					
					if (passType != "") {
						google.maps.event.addListener(map, 'click',
							function(point) {
								var latLng = point.latLng;
								var lat = latLng.lat();
								var lng = latLng.lng();
								
								// call CIPH
								$.getJSON(apiURL + "?function=CanIParkHere&latLng=" + lat + "," + lng + "&pass=" + passType,
									function(response) {
										if (response.lotName != null)
											alert("Can I park in " + response.lotName + "?\n"
												+ (response.ciph ? "Yes\n" : "No\n")
												+ "You're at (" + lat + ", " + lng + ").");
									});
							});
					}
				});
		});
}
function LoadMap_WCIP(passType) {
	if (passType != null) {
		$.getJSON(apiURL + "?function=GetSettingsForUser&id=0",
		function(data) {
			settings = data; // store settings, now create lots
			initialize();
			
			// get lot data
			$.getJSON(apiURL + "?function=GetLots",
				function(data) {
					lots = data; // store lot data, move to settings
					
					$.getJSON(apiURL + "?function=WhereCanIPark&pass=" + passType,
						function(data) {
							allowedLots = data;
							createLotPolygonsByPassType(); // grabs lot data
							createLotInfoMarkersByPassType();
						});
				});
		});
	}
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