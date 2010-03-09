var initialLocation = new google.maps.LatLng(34.668717, -82.837134);
var browserSupportFlag =  new Boolean();

function initialize() {
	var myOptions = {
		zoom: 15,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	// Try W3C Geolocation (Preferred)
	if(navigator.geolocation) {
		browserSupportFlag = true;
		navigator.geolocation.getCurrentPosition(function(position) {
			initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			map.setCenter(initialLocation);
		}, function() {
				handleNoGeolocation(browserSupportFlag);
			});
		// Try Google Gears Geolocation
	}
	else if (google.gears) {
		browserSupportFlag = true;
		var geo = google.gears.factory.create('beta.geolocation');
		geo.getCurrentPosition(function(position) {
			initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
			map.setCenter(initialLocation);
		}, function() {
				handleNoGeoLocation(browserSupportFlag);
			});
		// Browser doesn't support Geolocation
	}
	else {
		browserSupportFlag = false;
		handleNoGeolocation(browserSupportFlag);
	}

	function handleNoGeolocation(errorFlag) {
		if(errorFlag == true) {
			alert("Geolocation service failed.");
		}
		else {
			alert("Your browser doesn't support geolocation.");
		}
		map.setCenter(initialLocation);
	}
}
