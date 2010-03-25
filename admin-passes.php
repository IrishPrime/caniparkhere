<?php
# Create/delete pass types.
# TODO: Check for correct info and remove comment markers.

require("./auth.php");
require_once("./_logic.php");

$passes = GetPassTypes();

switch($_POST["action"]) {
	case "create":
		// CreatePassType($_POST["create_pass"]);
		break;
	case "edit":
		// RenamePassType($_POST["edit_select"], $_POST["edit_pass"]);
		break;
	case "delete":
		// DeletePassType($_POST["passes"]);
		break;
	default:
		break;
}
?>

<fieldset>
	<legend>Create Pass Type</legend>
	<form id="create" name="create" method="POST" action="">
		<label for="create_pass">Pass Type</label>
		<input id="create_pass" name="create_pass" type="text"/>
		<input type="hidden" name="action" value="create"/>
		<br/>
		<input type="submit" value="Create Pass"/>
	</form>
</fieldset>

<fieldset>
	<legend>Edit Pass Type</legend>
	<form id="edit" name="edit" method="POST" action="">
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
		<input type="hidden" name="action" value="edit"/>
		<br/>
		<input type="submit" value="Edit Pass"/>
	</form>
</fieldset>

<fieldset>
	<legend>Delete Pass Type</legend>
	<form id="delete" name="delete" method="POST" action="">
		<?php
		if(is_array($passes))
			foreach($passes as $pass) {
				echo "<input type=\"checkbox\" id=\"pass_".$pass["id"]."\" name=\"passes\" value=\"".$pass["id"]."\"><label style=\"width: auto;\" for=\"pass_".$pass["id"]."\">".$pass["name"]."</label><br/>\n";
			}
		?>
		<input type="hidden" name="action" value="delete"/>
		<input type="button" value="All"/>
		<input type="button" value="None"/><br/>
		<input type="submit" value="Delete Passes"/>
	</form>
</fieldset>
