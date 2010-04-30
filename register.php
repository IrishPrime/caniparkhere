<?php
require_once("./_logic.php");
$passes = GetPassTypes("name");
$status = 0;

if(!empty($_POST)) {
	// Check and escape input
	foreach($_POST as $k => $v) {
		if(!empty($_POST[$k])) {
			$_POST[$k] = addslashes($v);
		} else {
			$status = 2;
			break;
		}
	}

	// Password mismatch?
	if(strcmp($_POST["password_1"], $_POST["password_2"]) != 0) {
		$status = 3;
	}

	// Create connection
	mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or $status = 4;
	mysql_select_db(MYSQL_DB) or $status = 4;

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
		$sql .= sha1($_POST["password_1"].SALT) . "', '";
		$sql .= $_POST["passtype"] . "')";
		(mysql_query($sql) and $status = 1) or $status = 4;
	} else {
		// Don't create user
		$row = mysql_fetch_assoc($result);
		$status = 5;
	}
}

// Feedback
switch($status) {
	case 1:
		// Created user
		ui_info("Successfully registered: ".$_POST["email"]);
		break;
	case 2:
		// Empty field
		ui_alert("All fields required.");
		break;
	case 3:
		// Password mismatch
		ui_alert("Password mismatch.");
		break;
	case 4:
		// MySQL Error
		ui_alert("Could not connect: ".mysql_error());
	case 5:
		// E-Mail already registered
		ui_alert(stripslashes($_POST["email"])." is already registered by ".stripslashes($row["fullName"]).".");
		break;
	default:
		ui_info("All fields required.");
		break;
	}
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

			<label for="password_1">Password</label>
			<input id="password_1" name="password_1" type="password" class="required" minlength="8"/><br/>

			<label for="password_2">Confirm</label>
			<input id="password_2" name="password_2" type="password" class="required" equalTo="#password_1"/><br/>

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
