<?php
# Create/delete parking lots/areas.
require("./auth.php");
require_once("./_logic.php");

switch($_POST["action"]) {
	case "update":
		$result = @UpdateLot($_POST["lot_list"], $_POST["lot_name"], $_POST["lot_description"], $_POST["lot_coords"], $_POST["lot_scheme"]);

		if($result > 0) ui_info("Updated Lot: <strong>".$_POST["lot_name"]."</strong>");
		else ui_alert("Failed to Update Lot: <strong>".$_POST["lot_name"]."</strong>");
		break;
	case "delete":
		$result = @DeleteLots($_POST["delete_lots"]);

		if($result) ui_info("Deleted Lots: <strong>".count($_POST["delete_lots"])."</strong>");
		else ui_alert("Failed to Delete Lots: <strong>".count($_POST["delete_lots"])."</strong>");
		break;
	default:
		break;
}

$lots = GetLots("name");
$schemes = GetSchemes();
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript" src="./js/maps.js"></script>
<script type="text/javascript">
	var json_lots = jQuery.parseJSON('<?php echo json_encode($lots); ?>');
	$(document).ready(function() {
		$("#update_form").validate();

		$("#lot_list").bind("change keypress", function() {
			if($("#lot_list option:selected").val() != 0) {
				$("#lot_name").val($("#lot_list option:selected").html());
				$("#lot_description").val(json_lots[$("#lot_list option:selected").val()].description);
				$("#lot_scheme").val(json_lots[$("#lot_list option:selected").val()].scheme.id);
				$("#update_form :submit").val("Update Parking Lot");
			} else {
				$("#lot_name").val("");
				$("#lot_coords").val("");
				$("#lot_description").val("");
				$("#lot_scheme").val($("#lot_scheme option").val());
				$("#update_form :submit").val("Create Parking Lot");
			}
			SelectLot($("#lot_list option:selected").val());
		});
	});
</script>

<div id="tabs">
	<ul>
		<li><a href="#update_tab">Update Edit Parking Lots</a></li>
		<li><a href="#delete_tab">Delete Parking Lots</a></li>
	</ul>

	<!-- Create/Edit Tab -->
	<div id="update_tab">
		<form id="update_form" name="update" method="POST" action="">
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
			<!-- Map -->
			<div id="map_canvas" style="width: 100%; height: 65%;">
				<script type="text/javascript">LoadMap_Edit();</script>
			</div>
			<label for="lot_coords">Coordinates</label>
			<input type="text" id="lot_coords" name="lot_coords" readonly="readonly" class="required"/><br/>
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
			<br/>
			<!-- Timed Options
			<label for="update_timed">Timed Parking</label>
			<input type="text" id="update_timed" name="update_timed" value="0" class="required number" min="-1"/>
			-->
			<input type="hidden" name="action" value="update"/>
			<p><input type="submit" value="Create Parking Lot"/></p>
		</form>
		<?php echo $ui_help_create; ?>
		<div id="create_help_dialog" title="Update Lot Help">
			<h3>New Lot</h3>
			<ol>
				<li>Enter a <strong>Lot Name</strong>.</li>
				<li>Left click to place markers to enclose the parking area.</li>
				<li>Enter a <strong>Description</strong> for the lot, such as nearby landmarks.</li>
				<li>Select a <strong>Color Scheme</strong> for the lot.</li>
			</ol>

			<h3>Existing Lots</h3>
			<ol>
				<li>Select an <strong>Existing Lot</strong> to edit.</li>
				<li><strong>Lot Name</strong>, <strong>Description</strong>, and <strong>Color Scheme</strong> may all be edited.</li>
				<li>The <strong>Parking Lot</strong> itself may be reshaped.</li>
			</ol>

			<h3>General</h3>
			<ol>
				<li>Red markers may be dragged to reshape the lot.</li>
				<li>Right click on a draggable (red) marker to remove it.</li>
				<li>Lots cannot be saved until the polygon is closed. Left click a marker to close the lot polygon.</li>
				<li>Only one lot can be created (or edited) at a time.</li>
			</ol>
			<!-- Timed Help
			<h3>Timed Parking</h3>
			<ul>
				<li>-1: Metered</li>
				<li>0: Not timed</li>
				<li>n: Minutes</li>
			</ul>
			-->
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
			<p>Hold <em>Shift</em> to make continuous selections.</p>
			<p>Hold <em>Ctrl</em> to make discontinuous selections.</p>
		</div>
	</div>
</div>
