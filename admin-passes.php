<?php
# Create/delete pass types.
require("./auth.php");
require_once("./_logic.php");

$passes = AllPassTypes();
?>

<fieldset>
	<legend>Create Pass Type</legend>
	<form id="create" name="create" method="GET">
		<label for="new_pass">Pass Type</label>
		<input id="new_pass" name="new_pass" type="text"/>
		<br/>
		<input type="submit" value="Create Pass"/>
	</form>
</fieldset>

<fieldset>
	<legend>Edit Pass Type</legend>
	<form id="edit" name="edit" method="GET">
		<label for="edit_select">Select Pass</label>
		<select id="edit_select" name="edit_select">
			<optgroup label="Parking Passes">
			<?php
			if(is_array($passes))
				foreach($passes as $pass) {
					echo "<option value=\"".$pass["id"]."\">".$pass["name"]."</option>\n";
				}
			?>
			</optgroup>
		</select>
		<br/>
		<label for="edit_pass">New Name</label>
		<input id="edit_pass" name="edit_pass" type="text"/>
		<br/>
		<input type="submit" value="Edit Pass"/>
	</form>
</fieldset>

<fieldset>
	<legend>Delete Pass Type</legend>
	<form id="delete" name="delete" method="GET">
		<?php
		if(is_array($passes))
			foreach($passes as $pass) {
				echo "<input type=\"checkbox\" id=\"".$pass["id"]."\" name=\"".$pass["id"]."\"><label style=\"width: auto;\" for=\"".$pass["id"]."\">".$pass["name"]."</label><br/>\n";
			}
		?>
		<input type="button" value="All"/>
		<input type="button" value="None"/><br/>
		<input type="submit" value="Delete Passes"/>
	</form>
</fieldset>
