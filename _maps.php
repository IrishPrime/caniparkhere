<script type="text/javascript">
	
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
var lotPolygons = new Array(<?php echo count($lots) ?>);
var lotMarkers = new Array(<?php echo count($lots) ?>);
var lotInfoWindow = new Array(<?php echo count($lots) ?>);

function initialize() {
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
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
	lotPolygons[index] = lot;
	return lot;
}

function createMarker(index, position, name, html) {
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		shadow: shadow,
		icon: image,
		shape: shape,
		title: name,
		zIndex: index
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

	marker.setMap(map);
	lotInfoWindow[index] = infoWindow;
	lotMarkers[index] = marker;
	return marker;
}

function displayAllLots() {
<?php

	function populateHTML($html, $tagValues) {
		$output = $html;
		foreach ($tagValues as $tagValue) {
			$output = str_replace($tagValue["tag"], $tagValue["value"], $output);
		}
		// make HTML more readable
		//$output = str_replace("<br>", "<br>\n ", $output);
		return $output;
	};
	function implodeObjectProperty($objects, $property, $glue, $default) {
		$output = "";
		if ($objects == null) $output = $default;
		else {
			if (count($objects) != 0) {
				foreach ($objects as $object)
					$output .= $object[$property] . $glue;
				$output = rtrim($output, $glue);
			}
		}
		return $output;
	}
	function implodeArray($array, $glue) {
		$output = '';
		for ($x = 0; $x < count($array); $x++)
			$output .= $array[$x] . $glue;
		$output = rtrim($output, $glue);
		return $output;
	}
	function wrapText($array, $head, $tail) {
		for ($x = 0; $x < count($array); $x++) {
			$array[$x] = $head . $array[$x] . $tail;
		}
		return $array;
	}
	function addQuotes($string) {
		return '"' . $string . '"';
	}
	
	$i = 0;
	foreach ($lots as $lot) {
		//print_r($lot);
		$coords = wrapText($lot["coords"], " new google.maps.LatLng(", ")");
		$currentPasses = $lot["currentPassTypes"];
		$colorScheme = $lot["scheme"];
		
		// populate HTML tag values then fill in HTML
		$tagValues = array(
			array("tag" => "{lotPicture}",
				"value" => $lot["picture"]),
			array("tag" => "{lotName}",
				"value" => $lot["name"]),
			array("tag" => "{lotDescription}",
				"value" => $lot["description"]),
			array("tag" => "{currentPassTypes}", 
				"value" => implodeObjectProperty($currentPasses, "name", "<br>", "Lot closed to all pass types.")));
		$html = populateHTML($globalSettings["lotHTML"], $tagValues);
		
		// write comment
		echo "\n// Loading lot " . $lot["name"] . " [ID = " . $lot["id"] . "]\n";
		
		// create polygon
		echo "createPolygon("
			. $i . ", "
			. "[\n" . implodeArray($coords, ",\n") . "],\n "
			. addQuotes($colorScheme["lineColor"]) . ", " 
			. $colorScheme["lineWidth"] . ", "
			. $colorScheme["lineOpacity"] . ", "
			. addQuotes($colorScheme["fillColor"]) . ", "
			. $colorScheme["fillOpacity"] . ");\n";
		
		// create marker
		echo "createMarker("
			. $i++ . ",\n "
			. "new google.maps.LatLng(" . $lot["middle"] . ")" . ",\n "
			. addQuotes($lot["name"]) . ",\n "
			. addQuotes(addslashes($html)) . ");\n";
	}
?>
}
</script>
