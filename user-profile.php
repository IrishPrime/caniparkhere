<?php
# Edit the current user's profile. Form must submit to a different file to set new cookies.
require_once("./_logic.php");
$passes = GetPassTypes("name");

$sql = "SELECT * FROM users WHERE id='".addslashes($_COOKIE["id"])."' AND password='".$_COOKIE["auth"]."'";
$result = @mysql_query($sql);
$row = @mysql_fetch_assoc($result);
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#user_edit").validate();
	$("#user_delete").validate();
});
</script>

<div id="tabs">
	<ul>
		<li><a href="#edit_tab">Edit Profile</a></li>
		<li><a href="#delete_tab">Delete Account</a></li>
	</ul>

	<!-- Edit Tab -->
	<div id="edit_tab">
		<form name="user_edit" id="user_edit" method="POST" action="./user-profile-exec.php">
			<label for="edit_fname">First Name</label>
			<input id="edit_fname" name="edit_fname" type="text" value="<?php echo stripslashes($row["firstName"]); ?>" class="required" minlength="2"/><br/>

			<label for="edit_lname">Last Name</label>
			<input id="edit_lname" name="edit_lname" type="text" value="<?php echo stripslashes($row["lastName"]); ?>" class="required" minlength="2"/><br/>

			<label for="edit_email">E-Mail</label>
			<input id="edit_email" name="edit_email" type="text" value="<?php echo stripslashes($row["email"]); ?>" class="required email" minlength="2"/><br/>

			<label for="edit_password_1">Password</label>
			<input id="edit_password_1" name="edit_password_1" type="password" value="" class="required" minlength="8"/><br/>

			<label for="edit_password_2">Confirm</label>
			<input id="edit_password_2" name="edit_password_2" type="password" value="" class="required" equalTo="#edit_password_1"/><br/>

			<label for="edit_passtype">Pass</label>
			<select id="edit_passtype" name="edit_passtype">
				<optgroup label="Passes">
					<?php
					if(is_array($passes))
						foreach($passes as $pass) {
							if($pass["id"] == $row["passType"]) echo "<option value=\"".$pass["id"]."\" selected=\"selected\">".$pass["name"]."</option>\n";
							else echo "<option value=\"".$pass["id"]."\">".$pass["name"]."</option>\n";
						}
					?>
				</optgroup>
			</select>
			<input type="hidden" name="action" value="user_edit"/>
			<p><input type="submit" value="Edit Profile" /></p>
		</form>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<form name="user_delete" id="user_delete" method="POST" action="./user-profile-exec.php">
			<label for="delete_password">Password</label>
			<input type="password" name="delete_password" id="delete_password" value="" class="required"/>
			<input type="hidden" name="action" value="user_delete"/>
			<p><input type="submit" value="Delete Account"/></p>
		</form>
	</div>
</div>
