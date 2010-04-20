<?php
require_once("./_logic.php");
$passes = GetPassTypes("name");

if(!empty($_POST)) {
	// Check and escape input
	foreach($_POST as $k => $v) {
		if(!empty($_POST[$k])) {
			$_POST[$k] = addslashes($v);
		}
		else {
			$status = 2;
			break;
		}
	}

	// Password mismatch?
	if(strcmp($_POST["pass1"], $_POST["pass2"]) != 0) {
		$status = 3;
	}

	// Create connection
	mysql_connect($mysql_server, $mysql_user, $mysql_password) or $status = 4;
	mysql_select_db($mysql_db_name) or $status = 4;

	// Look for user
	$sql = "SELECT CONCAT_WS(' ', firstName, lastName) AS fullName FROM users WHERE email='".$_POST["email"]."'";
	$result = mysql_query($sql);

	// Unique e-mail?
	if(mysql_num_rows($result) == 0) {
		// Create user
		$sql  = "INSERT INTO users (firstName, lastName, email, password, passType) VALUES('";
		$sql .= $_POST["fname"] . "', '";
		$sql .= $_POST["lname"] . "', '";
		$sql .= $_POST["email"] . "', '";
		$sql .= sha1($_POST["pass1"].$password_salt) . "', '";
		$sql .= $_POST["passtype"] . "')";
		(mysql_query($sql) and $status = 1) or $status = 4;
	}
	else {
		// Don't create user
		$row = mysql_fetch_assoc($result);
		$status = 5;
	}
}

// Feedback
echo "<div class=\"ui-widget\">\n\t";
switch($status) {
	case 1:
		// Created user
		echo $ui_info;
		echo "Successfully registered: ".$_POST["email"];
		break;
	case 2:
		// Empty field
		echo $ui_alert;
		echo "All fields required.";
		break;
	case 3:
		// Password mismatch
		echo $ui_alert;
		echo "Password mismatch.";
		break;
	case 4:
		// MySQL Error
		echo $ui_alert;
		echo "Could not connect: ".mysql_error();
	case 5:
		// E-Mail already registered
		echo $ui_alert;
		echo stripslashes($_POST["email"])." is already registered by ".stripslashes($row["fullName"]).".";
		break;
	default:
		echo $ui_info;
		echo "All fields required.";
		break;
	}
echo "\n\t\t</div>\n\t</div>\n";
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#register").validate();
	});
</script>

<div id="tabs">
	<ul>
		<li><a href="#register_tab">New User</a></li>
	</ul>
	<div id="register_tab">
		<form name="register" id="register" method="POST" action="" onsubmit="return $('#register').validate().form()">
			<label for="fname">First Name</label>
			<input id="fname" name="fname" type="text" class="required" minlength="2"/><br/>

			<label for="lname">Last Name</label>
			<input id="lname" name="lname" type="text" class="required" minlength="2"/><br/>

			<label for="email">E-Mail</label>
			<input id="email" name="email" type="text" class="required email"/><br/>

			<label for="pass1">Password</label>
			<input id="pass1" name="pass1" type="password" class="required" minlength="8"/><br/>

			<label for="pass2">Confirm</label>
			<input id="pass2" name="pass2" type="password" class="required" minlength="8"/><br/>

			<label for="passtype">Pass Type</label>
			<select id="passtype" name="passtype">
				<optgroup label="Pass Type">
					<?php
					if(is_array($passes))
						foreach($passes as $pass) {
							echo "<option value=\"".$pass["id"]."\">".$pass["name"]."</option>\n";
						}
					?>
				</optgroup>
			</select>
			<p><input type="submit" value="Register"/></p>
		</form>
	</div>
</div>
