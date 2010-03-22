<?php
require_once("./_settings.php");

// Check for $_POST
if(!isset($_POST)) {
	die("Empty form.\n");
}

// Check input
foreach($_POST as $k => $v) {
	if(empty($_POST[$k]))
		echo("$k is empty.<br>\n");
	else
		$_POST[$k] = addslashes($v);
}

// Password mismatch?
if(strcmp($_POST["pass1"], $_POST["pass2"]) != 0)
	die("Password mismatch.<br>\n");

// Create connection
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect: " . mysql_error());
mysql_select_db("ciph") or die(mysql_error());

// Successful connection, setup queries
$sql = "SELECT CONCAT_WS(' ', firstName, lastName) AS fullName FROM users WHERE email='".$_POST["email"]."'";
$result = mysql_query($sql);

// Unique e-mail?
if(mysql_num_rows($result) == 0) {
	// Create user
	echo "Creating user.<br>\n";
	$sql  = "INSERT INTO users (firstName, lastName, email, password, passType) VALUES('";
	$sql .= $_POST["fname"] . "', '";
	$sql .= $_POST["lname"] . "', '";
	$sql .= $_POST["email"] . "', '";
	$sql .= md5($_POST["pass1"].$password_salt) . "', '";
	$sql .= $_POST["passtype"] . "')";
	mysql_query($sql);
}
else {
	// Don't create user
	$row = mysql_fetch_assoc($result);
	echo $_POST["email"]." is already registered by ".stripslashes($row["fullName"]).".<br>\n";
}

// Disconnect
mysql_close();
?>
