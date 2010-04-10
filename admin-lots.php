<?php
# Create/delete parking lots/areas.
require("./auth.php");
require_once("./_logic.php");
// include("./adminMaps.php");

switch($_POST["action"]) {
	case "create":
		break;
	case "edit":
		break;
	case "delete":
		break;
	default:
		break;
}

$lots = GetLots("name");
?>

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

var createMap;
var editMap;

function initialize() {
	createMap = new google.maps.Map(document.getElementById("create_map_canvas"), myOptions);
	editMap = new google.maps.Map(document.getElementById("edit_map_canvas"), myOptions);
}
</script>

<div id="tabs">
	<ul>
		<li><a href="#create_edit_tab">Create/Edit Parking Lot</a></li>
		<li><a href="#delete_tab">Delete Parking Lots</a></li>
	</ul>

	<!-- Create/Edit Tab -->
	<div id="create_edit_tab">
		<form id="create" name="create" method="GET">
			<label for="lot_name">Lot Name</label>
			<input id="lot_name" name="lot_name" type="text"/>
			<select id="lot_list" name="lot_list">
				<optgroup label="New Lot">
					<option value="">Create New Lot</a>
				</optgroup>
				<optgroup label="Existing Lots">
				<?php
				if(is_array($lots))
					foreach($lots as $lot)
						echo "\t\t\t\t<option value=\"".$lot["id"]."\">".$lot["name"]."</option>\n";
				?>
				</optgroup>
			</select>
			<div id="create_map_canvas" style="width: 100%; height: 65%;"></div>
			<label for="lot_description">Description</label>
			<textarea id="lot_description" name="lot_description" cols="40"></textarea>
			<br/>
			<label for="lot_scheme">Color Scheme</label>
			<select id="lot_scheme" name="lot_scheme">
				<optgroup label="Color Schemes">
				<?php
					foreach($schemes as $scheme) {
						echo "<option value=\"".$scheme["id"]."\">".$scheme["name"]."</option>\n";
					}
				?>
				</optgroup>
			</select>
			<input type="hidden" name="action" value="create"/>
			<p><input type="submit" value="Create Parking Lot"/></p>
		</form>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<form id="delete" name="delete" method="GET">
			<?php
			if(is_array($lots))
				foreach($lots as $lot) {
					echo "<p><label style=\"width: auto;\" for=\"".$lot["id"]."\">".$lot["name"]."</label><input type=\"checkbox\" id=\"".$lot["id"]."\" name=\"".$lot["id"]."\"><br/>".$lot["description"]."</p>\n";
				}
			?>
			<input type="hidden" name="action" value="create"/>
			<p><input type="submit" value="Delete Lots"/></p>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(initialize());
</script>
