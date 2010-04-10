<?php
# Grant/remove administrator status to other users.
require("./auth.php");
require_once("./_settings.php");

// Successful connection, setup queries
switch($_POST["action"]) {
	case "promote":
		$sql = "UPDATE users SET admin=1 WHERE email='".$_POST["promote_user"]."'";
		echo $_POST["promote_user"]." promoted to administrator.";
		break;
	case "demote":
		$sql = "UPDATE users SET admin=0 WHERE email = '".$_POST["demote_user"]."'";
		echo $_POST["demote_user"]." demoted from administrator.";
		break;
	case "delete":
		$sql = "DELETE from users WHERE email = '".$_POST["delete_user"]."'";
		echo $_POST["delete_user"]." deleted.";
		break;
	default:
		break;
}

mysql_query($sql);

$admin_query = "SELECT id, email, firstName, lastName FROM users WHERE admin = '1'";
$admin_results = mysql_query($admin_query);
$admins = array();
while($row = mysql_fetch_assoc($admin_results))
	// $admins[$row["id"]] = stripslashes($row["lastName"].", ".$row["firstName"]." (".$row["email"].")");
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
