<?php
# Edit the current user's profile.
require_once("./_settings.php");
require_once("./_logic.php");
$passes = GetPassTypes("name");

// Successful connection, setup queries
switch($_POST["action"]) {
	case "user_edit":
		$sql = "UPDATE users SET firstName='".addslashes($_POST["edit_fname"])."', lastName='".addslashes($_POST["edit_lname"])."', email='".addslashes($_POST["edit_email"])."', password='".md5($_POST["edit_pass1"].$password_salt)."', passType='".addslashes($_POST["edit_passtype"])."' WHERE id='".addslashes($_COOKIE["auth"])."'";
		mysql_query($sql);
		break;
	case "user_delete":
		$sql = "DELETE FROM users WHERE id='".addslashes($_COOKIE["id"])."'";
		mysql_query($sql);
		break;
	default:
		$sql = "SELECT * FROM users WHERE id='".addslashes($_COOKIE["id"])."'";
		break;
}

$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
?>

<script>
function check_form(form) {
	if(form.edit_fname.value == '') {
		alert('First name cannot be blank.');
		return false;
	}
	else if(form.edit_lname.value == '') {
		alert('Last name cannot be blank.');
		return false;
	}
	else if(form.edit_email.value == '') {
		alert('E-mail cannot be blank.');
		return false;
	}
	else if(form.edit_pass1.value == '') {
		alert('Password cannot be blank.');
		return false;
	}
	else if(form.edit_pass2.value != form.edit_pass1.value) {
		alert('Passwords do not match.');
		return false;
	}
	return true;
}
</script>

<div id="tabs">
	<ul>
		<li><a href="#edit_tab">Edit Profile</a></li>
		<li><a href="#delete_tab">Delete Account</a></li>
	</ul>

	<!-- Edit Tab -->
	<div id="edit_tab">
		<form name="user_edit" id="user_edit" method="POST" action="?page=user-profile" onSubmit="return check_form(this)">
			<label for="edit_fname">First Name</label>
			<input id="edit_fname" name="edit_fname" type="text" value="<?php echo stripslashes($row["firstName"]); ?>"/><br/>

			<label for="edit_lname">Last Name</label>
			<input id="edit_lname" name="edit_lname" type="text" value="<?php echo stripslashes($row["lastName"]); ?>"/><br/>

			<label for="edit_email">E-Mail</label>
			<input id="edit_email" name="edit_email" type="text" value="<?php echo stripslashes($row["email"]); ?>"/><br/>

			<label for="edit_pass1">Password</label>
			<input id="edit_pass1" name="edit_pass1" type="password" value=""/><br/>

			<label for="edit_pass2">Confirm</label>
			<input id="edit_pass2" name="edit_pass2" type="password" value=""/><br/>

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
		<form name="user_delete" id="user_edit" method="POST" action="?page=user-profile">
			<label for="delete_password">Password</label>
			<input type="password" name="delete_password" id="delete_password" value=""/>
			<input type="hidden" name="action" value="user_delete"/>
			<p><input type="submit" value="Delete Account"/></p>
		</form>
	</div>
</div>
