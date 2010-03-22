<?php
# Grant/remove administrator status to other users.
require("./auth.php");
require_once("./_settings.php");

// Create connection
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect: " . mysql_error());
mysql_select_db("ciph") or die(mysql_error());

// Successful connection, setup queries
switch($_POST["action"]) {
	case "promote":
		$sql = "UPDATE users SET admin=1 WHERE email='".$_POST["promote_user"]."'";
		break;
	case "demote":
		$sql = "UPDATE users SET admin=0 WHERE email = '".$_POST["demote_user"]."'";
		break;
	case "delete":
		$sql = "DELETE from users WHERE email = '".$_POST["delete_user"]."'";
		break;
	default:
		$sql = "";
		break;
}

$result = mysql_query($sql);

// Disconnect
mysql_close();
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
