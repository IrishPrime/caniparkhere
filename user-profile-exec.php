<?php
require_once("./_settings.php");
if(!get_magic_quotes_gpc()) @array_map(addslashes, $_POST);

// Create connection
mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or die("Could not connect: " . mysql_error());
mysql_select_db(MYSQL_DB) or die(mysql_error());

switch($_POST["action"]) {
	case "user_edit":
		$sql = "SELECT id, password, admin FROM users WHERE email='".stripslashes($_POST["edit_email"])."'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		// Ensure unique e-mail address and user authenticity
		if(mysql_num_rows($result) == 0 || ((mysql_num_rows($result) == 1) && ($_COOKIE["auth"] == $row["password"] && $_COOKIE["id"] == $row["id"]))) {
			$new_password = sha1($_POST["edit_password_1"].SALT);
			$sql = "UPDATE users SET firstName='".$_POST["edit_fname"]."', lastName='".$_POST["edit_lname"]
				. "', email='".$_POST["edit_email"]."', password='$new_password', passType='".$_POST["edit_passtype"]
				. "' WHERE id='".addslashes($_COOKIE["id"])."' AND password='".$_COOKIE["auth"]."'";
		} else {
			unset($sql);
		}
		break;
	case "user_delete":
		$sql = "DELETE FROM users WHERE id='".addslashes($_COOKIE["id"])."' AND password='".sha1($_POST["delete_password"].SALT)."'";
		break;
	default:
		unset($sql);
		break;
}

if(isset($sql)) {
	// Update database values
	if($result = mysql_query($sql)) {
		// Start output Buffering
		ob_start();
		session_regenerate_id();
		// Update cookie values
		setcookie("id", $row["id"], time()+SESSION_DURATION);
		setcookie("auth", $new_password, time()+SESSION_DURATION);
		setcookie("admin", $row["admin"], time()+SESSION_DURATION);
		setcookie("passType", $_POST["edit_passtype"], time()+SESSION_DURATION);
	}
}

// Redirect
header("location: index.php?page=user-profile");
// Flush output buffer
ob_end_flush();
?>
