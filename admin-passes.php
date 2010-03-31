<?php
# Create/delete pass types.

require("./auth.php");
require_once("./_logic.php");

switch($_POST["action"]) {
	case "create":
		CreatePassType($_POST["create_pass"]);
		break;
	case "edit":
		RenamePassType($_POST["edit_select"], $_POST["edit_pass"]);
		break;
	case "delete":
		DeletePassTypes($_POST["delete_passes"]);
		break;
	default:
		break;
}

$passes = GetPassTypes("name");
?>

<div id="accordion">
	<h1><a href="#">Create Pass Type</a></h1>
	<div>
		<form id="create" name="create" method="POST" action="">
			<label for="create_pass">Pass Type</label>
			<input id="create_pass" name="create_pass" type="text"/>
			<input type="hidden" name="action" value="create"/>
			<br/>
			<input type="submit" value="Create Pass"/>
		</form>
	</div>

	<h1><a href="#">Edit Pass Type</a></h1>
	<div>
		<form id="edit" name="edit" method="POST" action="">
			<label for="edit_select">Select Pass</label>
			<select id="edit_select" name="edit_select">
				<optgroup label="Passes">
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
			<p><input type="submit" value="Edit Pass"/></p>
		</form>
	</div>

	<h1><a href="#">Delete Pass Type</a></h1>
	<div>
		<form id="delete" name="delete" method="POST" action="">
			<select id="delete_passes" name="delete_passes[]" multiple="multiple" size="15">
				<optgroup label="Passes">
				<?php
				if(is_array($passes))
					foreach($passes as $pass) {
						echo "<option value=\"".$pass["id"]."\">".$pass["name"]."</option>\n";
					}
				?>
				</optgroup>
			</select>
			<input type="hidden" name="action" value="delete"/>
			<p><input type="submit" value="Delete Passes"/></p>
		</form>
	</div>
</div>
