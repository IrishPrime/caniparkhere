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
	case "modify":
		$result = @RenamePassType($_POST["modify_select"], $_POST["modify_pass"]);

		echo "<div class=\"ui-widget\">\n";
		if($result) {
			printf("%sRenamed Pass: <strong>%s</strong> to <strong>%s</strong>\n\t</div>\n</div>\n", $ui_info, $_POST["modify_select"], $_POST["modify_pass"]);
		} else {
			printf("%sFailed to rename pass: <strong>%s</strong> to <strong>%s</strong>\n\t</div>\n</div>\n", $ui_alert, $_POST["modify_select"], $_POST["modify_pass"]);
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
		$("#modify_form").validate();
		$("#delete_form").validate();
	});
</script>

<div id="tabs">
	<ul>
		<li><a href="#create_tab">Create Pass Type</a></li>
		<li><a href="#modify_tab">Modify Pass Type</a></li>
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
		<?php echo $ui_help_create; ?>
		<div id="create_help_dialog" title="Create Pass Help">
			<p>Enter the name of the <strong>Pass Type</strong> to create.</p>
		</div>
	</div>

	<div id="modify_tab">
		<form id="modify_form" name="modify" method="POST" action="">
			<label for="modify_select">Current Pass</label>
			<select id="modify_select" name="modify_select">
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
			<label for="modify_pass">New Name</label>
			<input id="modify_pass" name="modify_pass" type="text" class="required" minlength="1"/>
			<input type="hidden" name="action" value="modify"/>
			<p><input type="submit" value="Modify Pass"/></p>
		</form>
		<?php echo $ui_help_modify; ?>
		<div id="modify_help_dialog" title="Modify Pass Help">
			<p>Select the <strong>Current Pass</strong> and enter a <strong>New Name</strong>.</p>
		</div>
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
		<?php echo $ui_help_delete; ?>
		<div id="delete_help_dialog" title="Delete Pass Help">
			<p>Select all <strong>Passes</strong> to delete.</p>
		</div>
	</div>
</div>
