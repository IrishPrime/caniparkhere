<?php
# Create/delete parking lots/areas.
require_once("_settings.php");
require("./auth.php");
include("./data.php");
$data = new data();
$lots = $data->get_lots();
?>

<fieldset>
	<legend>Create Parking Lot</legend>
	<form id="create" name="create" method="GET">
		<label for="lot_name">Lot Name</label>
		<input id="lot_name" name="lot_name" type="text"/>
		<div>
			<h1>[Google Map Here]</h1>
		</div>
		<label for="lot_description">Description</label>
		<textarea id="lot_description" name="lot_description" cols="40"></textarea>
		<br/>
		<input type="submit" value="Create Parking Lot"/>
	</form>
</fieldset>

<fieldset>
	<legend>Edit Parking Lot</legend>
	<form id="edit" name="edit" method="GET">
		<label for="lot_list">Lot Name</label>
		<select id="lot_list" name="lot_list">
			<optgroup label="Parking Lots">
			<?php
			if(is_array($lots))
				foreach($lots as $lot)
					echo "\t\t\t\t<option>".$lot["name"]."</option>\n";
			?>
			</optgroup>
		</select>
		<div>
			<h1>[Google Map Here]</h1>
		</div>
		<label for="lot_description">Description</label>
		<textarea id="lot_description" name="lot_description" cols="40"></textarea>
		<br/>
		<input type="submit" value="Edit Parking Lot"/>
	</form>
</fieldset>
<fieldset>
	<legend>Delete Parking Lots</legend>
	<form id="delete" name="delete" method="GET">
		<?php
		if(is_array($lots))
			foreach($lots as $lot) {
				echo "<input type=\"checkbox\" id=\"".$lot["id"]."\" name=\"".$lot["id"]."\"><label style=\"width: auto;\" for=\"".$lot["id"]."\">".$lot["name"]."</label> - ".$lot["description"]."<br/>\n";
			}
		?>
		<input type="button" value="All"/>
		<input type="button" value="None"/><br/>
		<input type="submit" value="Delete Lots"/>
	</form>
</fieldset>
