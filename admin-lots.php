<?php
# Create/delete parking lots/areas.
require("./auth.php");
require_once("./_logic.php");

$lots = GetLots();

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
?>

<div id="accordion">
	<h1><a href="#">Create Parking Lot</a></h1>
		<form id="create" name="create" method="GET">
			<label for="lot_name">Lot Name</label>
			<input id="lot_name" name="lot_name" type="text"/>
			<div>
				<h3>[Google Map Here]</h3>
			</div>
			<label for="lot_description">Description</label>
			<textarea id="lot_description" name="lot_description" cols="40"></textarea>
			<br/>
			<input type="file" name="create_photo" size="55">
			<input type="hidden" name="action" value="create"/>
			<p><input type="submit" value="Create Parking Lot"/></p>
		</form>

	<h1><a href="#">Edit Parking Lot</a></h1>
	<div>
		<form id="edit" name="edit" method="GET">
			<label for="lot_list">Lot Name</label>
			<select id="lot_list" name="lot_list">
				<optgroup label="Parking Lots">
				<?php
				if(is_array($lots))
					foreach($lots as $lot)
						echo "\t\t\t\t<option value=\"".$lot["id"]."\">".$lot["name"]."</option>\n";
				?>
				</optgroup>
			</select>
			<div>
				<h3>[Google Map Here]</h3>
			</div>
			<label for="lot_description">Description</label>
			<textarea id="lot_description" name="lot_description" cols="40"></textarea>
			<br/>
			<input type="file" name="edit_photo" size="55">
			<input type="hidden" name="action" value="edit"/>
			<p><input type="submit" value="Edit Parking Lot"/></p>
		</form>
	</div>

	<h1><a href="#">Delete Parking Lots</a></h1>
	<div>
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
