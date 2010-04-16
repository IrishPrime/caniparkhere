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
			printf("%sFailed to Promote User: <strong>%s</strong> to <strong>%s</strong>\n\t</div>\n", $ui_alert, $_POST["promote_user"]);
		}
		break;
	case "demote":
		$sql = "UPDATE users SET admin=0 WHERE email = '".$_POST["demote_user"]."'";
		echo "<div class=\"ui-widget\">\n";
		if(mysql_query($sql)) {
			printf("%sDemoted User: <strong>%s</strong>\n\t</div>\n", $ui_info, $_POST["demote_user"]);
		} else {
			printf("%sFailed to Demote User: <strong>%s</strong> to <strong>%s</strong>\n\t</div>\n", $ui_alert, $_POST["demote_user"]);
		}
		break;
	case "delete":
		$sql = "DELETE from users WHERE email = '".$_POST["delete_user"]."'";
		if(mysql_query($sql)) {
			printf("%sDeleted User: <strong>%s</strong>\n\t</div>\n", $ui_info, $_POST["demote_user"]);
		} else {
			printf("%sFailed to Delete User: <strong>%s</strong> to <strong>%s</strong>\n\t</div>\n", $ui_alert, $_POST["demote_user"]);
		}
		break;
	default:
		break;
}
echo "</div>\n";

$admin_query = "SELECT id, email, firstName, lastName FROM users WHERE admin = '1'";
$admin_results = mysql_query($admin_query);
while($row = mysql_fetch_assoc($admin_results))
	$admins[$row["id"]] = stripslashes($row["email"]);
?>

<script type="text/javascript">
	$(document).ready(function() {
		var admins = ["<?php echo implode("\", \"", $admins); ?>"];
		$("#demote_user").autocomplete({source: admins});
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
		<form id="promote" name="promote" method="POST" action="?page=admin-users">
			<label for="promote_user">E-Mail</label>
			<input type="text" id="promote_user" name="promote_user"/>
			<input type="hidden" name="action" value="promote">
			<br/>
			<input type="submit" value="Promote"/>
		</form>
	</div>

	<!-- Demote Tab -->
	<div id="demote_tab">
		<form id="demote" name="demote" method="POST" action="?page=admin-users">
			<label for="demote_user">E-Mail</label>
			<input type="text" id="demote_user" name="demote_user"/>
			<input type="hidden" name="action" value="demote">
			<br/>
			<input type="submit" value="Demote"/>
		</form>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<form id="delete" name="delete" method="POST" action="?page=admin-users">
			<label for="delete_user">E-Mail</label>
			<input type="text" id="delete_user" name="delete_user"/>
			<input type="hidden" name="action" value="delete">
			<br/>
			<input type="submit" value="Delete"/>
		</form>
	</div>
</div>
