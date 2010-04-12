<?php
# Create/delete pass types.

require("./auth.php");
require_once("./_logic.php");

switch($_POST["action"]) {
	case "create":
		$passID = @CreatePassType($_POST["create_pass"]);

		echo "<div class=\"ui-widget\">\n";
		if($passID > 0) {
			printf("%sCreated Pass: <strong>%s</strong>\n\t</div>\n</div>\n", $ui_info, $_POST["create_pass"]);
		} else {
			printf("%sFailed to create pass: <strong>%s</strong>\n\t</div>\n</div>\n", $ui_alert, $_POST["create_pass"]);
		}
		break;
	case "edit":
		$result = @RenamePassType($_POST["edit_select"], $_POST["edit_pass"]);

		echo "<div class=\"ui-widget\">\n";
		if($result) {
			printf("%sRenamed Pass: <strong>%s</strong> to <strong>%s</strong>\n\t</div>\n</div>\n", $ui_info, $_POST["edit_select"], $_POST["edit_pass"]);
		} else {
			printf("%sFailed to rename pass: <strong>%s</strong> to <strong>%s</strong>\n\t</div>\n</div>\n", $ui_alert, $_POST["edit_select"], $_POST["edit_pass"]);
		}
		break;
	case "delete":
		$result = @DeletePassTypes($_POST["delete_passes"]);

		echo "<div class=\"ui-widget\">\n";
		if($result) {
			printf("%sDeleted Passes: <strong>%d</strong>\n\t</div>\n</div>\n", $ui_info, count($_POST["delete_passes"]));
		} else {
			printf("%sFailed to delete passes: <strong>%d</strong>\n\t</div>\n</div>\n", $ui_alert, count($_POST["delete_passes"]));
		}
		break;
	default:
		break;
}

$passes = GetPassTypes("name");
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#create_form").validate();
		$("#edit_form").validate();
		$("#delete_form").validate();
	});
</script>

<div id="tabs">
	<ul>
		<li><a href="#create_tab">Create Pass Type</a></li>
		<li><a href="#edit_tab">Edit Pass Type</a></li>
		<li><a href="#delete_tab">Delete Pass Type</a></li>
	</ul>

	<div id="create_tab">
		<form id="create_form" name="create" method="POST" action="">
			<label for="create_pass">Pass Type</label>
			<input id="create_pass" name="create_pass" type="text" class="required"/>
			<input type="hidden" name="action" value="create"/>
			<br/>
			<input type="submit" value="Create Pass"/>
		</form>
	</div>

	<div id="edit_tab">
		<form id="edit_form" name="edit" method="POST" action="">
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
			<input id="edit_pass" name="edit_pass" type="text" class="required" minlength="1"/>
			<input type="hidden" name="action" value="edit"/>
			<p><input type="submit" value="Edit Pass"/></p>
		</form>
	</div>

	<div id="delete_tab">
		<form id="delete_form" name="delete" method="POST" action="">
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
