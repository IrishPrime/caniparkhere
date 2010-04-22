<?php
# Grant/remove administrator status to other users.
require("./auth.php");
require_once("./_settings.php");

// Successful connection, setup queries
echo "<div class=\"ui-widget\">\n";
switch($_POST["action"]) {
	case "promote":
		$sql = "UPDATE users SET admin=1 WHERE email='".$_POST["promote_user"]."'";
		echo "<div class=\"ui-widget\">\n";
		if(mysql_query($sql)) {
			printf("%sPromoted User: <strong>%s</strong>\n\t</div>\n", $ui_info, $_POST["promote_user"]);
		} else {
			printf("%sFailed to Promote User: <strong>%s</strong>\n\t</div>\n", $ui_alert, $_POST["promote_user"]);
		}
		break;
	case "demote":
		$sql = "UPDATE users SET admin=0 WHERE id IN (".implode(", ", $_POST["demote_user"]).")";
		echo "<div class=\"ui-widget\">\n";
		if(mysql_query($sql)) {
			printf("%sDemoted User: <strong>%s</strong>\n\t</div>\n", $ui_info, count($_POST["demote_user"]));
		} else {
			printf("%sFailed to Demote User: <strong>%s</strong>\n\t</div>\n", $ui_alert, count($_POST["demote_user"]));
		}
		break;
	case "delete":
		$sql = "DELETE from users WHERE email='".$_POST["delete_user"]."'";
		if(mysql_query($sql)) {
			printf("%sDeleted User: <strong>%s</strong>\n\t</div>\n", $ui_info, $_POST["delete_user"]);
		} else {
			printf("%sFailed to Delete User: <strong>%s</strong>\n\t</div>\n", $ui_alert, $_POST["delete_user"]);
		}
		break;
	default:
		break;
}
echo "</div>\n";

$admins = GetAdmins();
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#promote_form").validate();
	$("#demote_form").validate();
	$("#delete_form").validate();
});
</script>

<div id="tabs">
	<ul>
		<li><a href="#promote_tab">Promote to Administrator</a></li>
		<li><a href="#demote_tab">Demote from Administrator</a></li>
		<li><a href="#delete_tab">Delete User</a></li>
	</ul>

	<!-- Promote Tab -->
	<div id="promote_tab">
		<form id="promote_form" method="POST" action="">
			<label for="promote_user">E-Mail</label>
			<input type="text" id="promote_user" name="promote_user" class="required email"/>
			<input type="hidden" name="action" value="promote">
			<br/>
			<input type="submit" value="Promote"/>
		</form>
		<!-- Help -->
		<?php echo $ui_help_create; ?>
		<div id="create_help_dialog" title="Promote to Administrator">
			<p>Enter the <strong>E-Mail</strong> address of the user to promote to administrator status.</p>
			<p>Administrators can create/edit/delete rules, exceptions, parking lots, pass types, color schemes, and other administrators.</p>
			<p>There is no "super admin". All administrators have the same abilities.</p>
			<p>There is no limit on the number of administrators.</p>
			<p>After a user is promoted he or she must logout of the system and back in to be reauthenticated.</p>
		</div>
	</div>

	<!-- Demote Tab -->
	<div id="demote_tab">
		<form id="demote_form" method="POST" action="">
			<select id="demote_user" name="demote_user[]" multiple="multiple" class="required"/>
				<optgroup label="Administrators">
				<?php
				foreach($admins as $admin) {
					printf("<option value=\"%d\">%s, %s (%s)</option>\n", $admin["id"], $admin["lastName"], $admin["firstName"], $admin["email"]);
				}
				?>
				</optgroup>
			</select>
			<input type="hidden" name="action" value="demote">
			<br/>
			<input type="submit" value="Demote"/>
		</form>
		<!-- Help -->
		<?php echo $ui_help_modify; ?>
		<div id="modify_help_dialog" title="Demote from Administrator">
			<p>Select <strong>Administrators</strong> to demote.</p>
			<p>Hold <em>Shift</em> to (de)select a range or <em>Ctrl</em> to (de)select discontinuous items.</p>
			<p>Take care not to demote yourself accidentally.</p>
		</div>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<form id="delete_form" method="POST" action="">
			<label for="delete_user">E-Mail</label>
			<input type="text" id="delete_user" name="delete_user" class="required email"/>
			<input type="hidden" name="action" value="delete">
			<br/>
			<input type="submit" value="Delete"/>
		</form>
		<!-- Help -->
		<?php echo $ui_help_delete; ?>
		<div id="delete_help_dialog" title="Delete User">
			<p>Enter the <strong>E-Mail</strong> address of a user to remove their account completely.</p>
		</div>
	</div>
</div>
