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
		$sql = "";
		break;
}

mysql_query($sql);
?>

<fieldset>
	<legend>Promote to Administrator</legend>
	<form id="promote" name="promote" method="POST" action="?page=admin-users">
		<label for="promote_user">E-Mail</label>
		<input type="text" id="promote_user" name="promote_user"/>
		<input type="hidden" name="action" value="promote">
		<br/>
		<input type="submit" value="Promote"/>
	</form>
</fieldset>

<fieldset>
	<legend>Demote from Administrator</legend>
	<form id="demote" name="demote" method="POST" action="?page=admin-users">
		<label for="demote_user">E-Mail</label>
		<input type="text" id="demote_user" name="demote_user"/>
		<input type="hidden" name="action" value="demote">
		<br/>
		<input type="submit" value="Demote"/>
	</form>
</fieldset>

<fieldset>
	<legend>Delete User</legend>
	<form id="delete" name="delete" method="POST" action="?page=admin-users">
		<label for="delete_user">E-Mail</label>
		<input type="text" id="delete_user" name="delete_user"/>
		<input type="hidden" name="action" value="delete">
		<br/>
		<input type="submit" value="Delete"/>
	</form>
</fieldset>
