<?php
session_start();
require_once("./_settings.php");

// Create connection
mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or die("Could not connect: " . mysql_error());
mysql_select_db(MYSQL_DB) or die(mysql_error());

// Successful connection, setup queries
$sql = "SELECT id, password, passType, admin FROM users WHERE email='".addslashes($_POST["email"])."' AND password='".sha1(addslashes($_POST["password"].SALT))."'";
$result = mysql_query($sql);

ob_start();
if($row = mysql_fetch_assoc($result)) {
	session_regenerate_id();
	setcookie("id", $row["id"], time()+SESSION_DURATION);
	setcookie("auth", $row["password"], time()+SESSION_DURATION);
	setcookie("admin", $row["admin"], time()+SESSION_DURATION);
	setcookie("passType", $row["passType"], time()+SESSION_DURATION);
	echo "Login successful.<br/>\n";
}
else {
	echo "Login failed.<br/>\n";
}
// Disconnect
mysql_close();
header("location: ./index.php");
ob_end_flush();
?>
