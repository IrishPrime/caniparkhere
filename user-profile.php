<?php
# Edit the current user's profile.
require_once("./_settings.php");
require_once("./_logic.php");
$passes = GetPassTypes("name");

// Successful connection, setup queries
@array_map(addslashes, $_POST);

switch($_POST["action"]) {
	case "user_edit":
		$sql = "UPDATE users SET firstName='".$_POST["edit_fname"]."', lastName='".$_POST["edit_lname"]."', email='".$_POST["edit_email"]."', password='".sha1($_POST["edit_pass1"].$password_salt)."', passType='".$_POST["edit_passtype"]."' WHERE id='".addslashes($_COOKIE["id"])."' AND password='".$_COOKIE["auth"]."'";
		mysql_query($sql);
		break;
	case "user_delete":
		$sql = "DELETE FROM users WHERE id='".addslashes($_COOKIE["id"])."' AND password='".sha1($_POST["delete_password"].$password_salt)."'";
		mysql_query($sql);
		break;
	default:
		$sql = "SELECT * FROM users WHERE id='".addslashes($_COOKIE["id"])."'";
		break;
}

$result = mysql_query($sql);
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
		<form name="user_edit" id="user_edit" method="POST" action="?page=user-profile">
			<label for="edit_fname">First Name</label>
			<input id="edit_fname" name="edit_fname" type="text" value="<?php echo stripslashes($row["firstName"]); ?>" class="required" minlength="2"/><br/>

			<label for="edit_lname">Last Name</label>
			<input id="edit_lname" name="edit_lname" type="text" value="<?php echo stripslashes($row["lastName"]); ?>" class="required" minlength="2"/><br/>

			<label for="edit_email">E-Mail</label>
			<input id="edit_email" name="edit_email" type="text" value="<?php echo stripslashes($row["email"]); ?>" class="required email" minlength="2"/><br/>

			<label for="edit_pass1">Password</label>
			<input id="edit_pass1" name="edit_pass1" type="password" value="" class="required" minlength="8"/><br/>

			<label for="edit_pass2">Confirm</label>
			<input id="edit_pass2" name="edit_pass2" type="password" value="" class="required" minlength="8"/><br/>

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
		<form name="user_delete" id="user_delete" method="POST" action="?page=user-profile">
			<label for="delete_password">Password</label>
			<input type="password" name="delete_password" id="delete_password" value="" class="required"/>
			<input type="hidden" name="action" value="user_delete"/>
			<p><input type="submit" value="Delete Account"/></p>
		</form>
	</div>
</div>
