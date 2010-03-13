<?php
# Grant/remove administrator status to other users.
require_once("./_settings.php");
require("./auth.php");
?>

<fieldset>
	<legend>Promote to Administrator</legend>
	<form id="promote" name="promote" method="GET">
		<label for="promote_user">E-Mail</label>
		<input type="text" id="promote_user" name="promote_user"/>
		<br/>
		<input type="submit" value="Promote"/>
	</form>
</fieldset>

<fieldset>
	<legend>Demote from Administrator</legend>
	<form id="demote" name="demote" method="GET">
		<label for="demote_user">E-Mail</label>
		<input type="text" id="demote_user" name="demote_user"/>
		<br/>
		<input type="submit" value="Demote"/>
	</form>
</fieldset>

<fieldset>
	<legend>Delete User</legend>
	<form id="delete" name="delete" method="GET">
		<label for="delete_user">E-Mail</label>
		<input type="text" id="delete_user" name="delete_user"/>
		<br/>
		<input type="submit" value="Delete"/>
	</form>
</fieldset>
