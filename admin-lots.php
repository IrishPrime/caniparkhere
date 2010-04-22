<?php
# Create/delete parking lots/areas.
require("./auth.php");
require_once("./_logic.php");
// include("./adminMaps.php");

echo "<div class=\"ui-widget\">\n";
switch($_POST["action"]) {
	case "create":
		$result = @CreateLot($_POST["lot_name"], $_POST["lot_description"], $_POST["lot_coords"], $_POST["lot_scheme"]);

		if($result > 0) {
			printf("%sCreated Lot: <strong>%s</strong>\n\t</div>\n", $ui_info, $_POST["lot_name"]);
		} else {
			printf("%sFailed to Create Lot: <strong>%s</strong>\n\t</div>\n", $ui_alert, $_POST["lot_name"]);
		}
		break;
	case "edit":
		break;
	case "delete":
		$result = @DeleteLots($_POST["delete_lots"]);

		if($result) {
			printf("%sDeleted Lots: <strong>%d</strong>\n\t</div>\n", $ui_info, count($_POST["delete_lots"]));
		} else {
			printf("%sFailed to Delete Lots: <strong>%d</strong>\n\t</div>\n", $ui_alert, count($_POST["delete_lots"]));
		}
		break;
	default:
		break;
}
echo "</div>\n";

$lots = GetLots("name");
$schemes = GetSchemes();
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript" src="./js/maps.js"></script>
<script type="text/javascript">
	var json_lots = jQuery.parseJSON('<?php echo json_encode($lots); ?>');
	$(document).ready(function() {
		$("#create_form").validate();

		$("#lot_list").bind("change keypress", function() {
			if($("#lot_list option:selected").val() != 0) {
				$("#lot_name").val($("#lot_list option:selected").html());
				$("#lot_coords").val(json_lots[$("#lot_list option:selected").val()].coords);
				$("#lot_description").val(json_lots[$("#lot_list option:selected").val()].description);
				$("#lot_scheme").val(json_lots[$("#lot_list option:selected").val()].scheme.id);
			} else {
				$("#lot_name").val("");
				$("#lot_coords").val("");
				$("#lot_description").val("");
				$("#lot_scheme").val($("#lot_scheme option").val());
			}
			editLot($("#lot_list option:selected").val());
		});
	});
</script>

<div id="tabs">
	<ul>
		<li><a href="#create_edit_tab">Create/Edit Parking Lot</a></li>
		<li><a href="#delete_tab">Delete Parking Lots</a></li>
	</ul>

	<!-- Create/Edit Tab -->
	<div id="create_edit_tab">
		<form id="create_form" name="create" method="POST" action="">
			<label for="lot_name">Lot Name</label>
			<input id="lot_name" name="lot_name" type="text" class="required" minlength="1"/>
			<select id="lot_list" name="lot_list">
				<optgroup label="New Lot">
					<option value="0">Create New Lot</option>
				</optgroup>
				<optgroup label="Existing Lots">
				<?php
				if(is_array($lots))
					foreach($lots as $lot)
						echo "\t\t\t\t<option value=\"".$lot["id"]."\">".$lot["name"]."</option>\n";
				?>
				</optgroup>
			</select>
			<div id="map_canvas" style="width: 100%; height: 65%;">
				<script type="text/javascript">
					LoadMap_Edit();
				</script>
			</div>
			<label for="lot_coords">Coordinates</label>
			<input type="text" id="lot_coords" name="lot_coords" class="required"/><br/>
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
			<!-- Timed Options -->
			<input type="hidden" name="action" value="create"/>
			<p><input type="submit" value="Save Parking Lot"/></p>
		</form>
		<?php echo $ui_help_create; ?>
		<div id="create_help_dialog" title="Create Lot Help">
			<p>Enter a <strong>Lot Name</strong> or select an <strong>Existing Lot</strong> to edit.</p>
			<h3>Create New Lot</h3>
			<ol>
				<li>Left click on the map to place the <strong>Starting Marker</strong> for a lot.</li>
				<li>Left click to continue placing vertices to enclose the parking area.</li>
				<li>Left click the <strong>Starting Marker</strong> to close the lot.</li>
				<li>Right click on the map at any time to undo the last action.</li>
				<li>Vertices may be dragged to reshape the lot.</li>
			</ol>
			<h3>Edit Existing Lot</h3>
			<ol>
				<li>Vertices may be dragged to reshape the lot.</li>
			</ol>
		</div>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<form id="delete" name="delete" method="POST" action="">
			<select id="delete_lots" name="delete_lots[]" multiple="multiple" size="15">
				<optgroup label="Parking Lots">
					<?php
					if(is_array($lots))
						foreach($lots as $lot) {
							echo "<option value=\"".$lot["id"]."\" title=\"".$lot["description"]."\">".$lot["name"]."</option>\n";
						}
					?>
				</optgroup>
			</select>
			<input type="hidden" name="action" value="delete"/>
			<p><input type="submit" value="Delete Lots"/></p>
		</form>
		<?php echo $ui_help_delete; ?>
		<div id="delete_help_dialog" title="Delete Lot Help">
			<p><strong>Parking Lots</strong> are sorted by <em>name</em>.</p>
			<p>Hover over a <strong>Parking Lot</strong> to view its <em>description</em>.</p>
			<p>Select <strong>Parking Lots</strong> to remove.</p>
		</div>
	</div>
</div>
