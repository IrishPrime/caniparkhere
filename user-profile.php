<?php
# Edit the current user's profile.
require_once("./_settings.php");
require("./data.php");
?>

<script>
</script>

<fieldset><legend>Edit Profile</legend>
<form name="user_edit" id="user_edit" method="POST" action="">
	<label for="edit_fname">First Name</label>
	<input id="edit_fname" name="edit_fname" type="text"/><br/>

	<label for="edit_lname">Last Name</label>
	<input id="edit_lname" name="edit_lname" type="text"/><br/>

	<label for="edit_email">E-Mail</label>
	<input id="edit_email" name="edit_email" type="text"/><br/>

	<label for="edit_pass1">Password</label>
	<input id="edit_pass1" name="edit_pass1" type="password"/><br/>

	<label for="edit_pass2">Confirm</label>
	<input id="edit_pass2" name="edit_pass2" type="password"/><br/>

	<label for="edit_passtype">Pass Type</label>
	<select id="edit_passtype" name="edit_passtype">
		<optgroup label="Pass Type">
			<?php
			if(is_array($passes))
				foreach($passes as $k => $v) {
					echo "<option value=\"$k\">$v</option>\n";
				}
			?>
		</optgroup>
	</select><br/>
	<input type="submit" value="Edit Profile" /><br/>
</form>
</fieldset>

<fieldset><legend>Delete Account</legend>
<form name="user_delete" id="user_edit" method="POST" action="">
	<label for="delete_password">Password</label>
	<input type="password" name="delete_password" id="delete_password"/>
	<br/>
	<input type="submit" value="Delete Account"/>
</form>
</fieldset>
