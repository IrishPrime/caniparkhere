<?php
session_start();
require_once("./_settings.php");

// Create connection
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect: " . mysql_error());
mysql_select_db($mysql_db_name) or die(mysql_error());

// Successful connection, setup queries
$sql = "SELECT id, password, passType, admin FROM users WHERE email='".addslashes($_POST["email"])."' AND password='".sha1(addslashes($_POST["password"].$password_salt))."'";
$result = mysql_query($sql);

ob_start();
if($row = mysql_fetch_assoc($result)) {
	session_regenerate_id();
	setcookie("id", $row["id"], time()+$session_duration);
	setcookie("auth", $row["password"], time()+$session_duration);
	setcookie("admin", $row["admin"], time()+$session_duration);
	setcookie("passType", $row["passType"], time()+$session_duration);
	echo "Login successful.<br/>\n";
}
else {
	echo "Login failed.<br/>\n";
}
// Disconnect
mysql_close();
header("location: index.php");
ob_end_flush();
?>
