<?php
session_start();
require_once("./_settings.php");

// Create connection
mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect: " . mysql_error());
mysql_select_db("ciph") or die(mysql_error());

// Successful connection, setup queries
$sql = "SELECT id, admin FROM users where email='".$_POST["email"]."' AND password='".md5($_POST["password"].$password_salt)."'";
$result = mysql_query($sql);

if($row = mysql_fetch_assoc($result)) {
	session_regenerate_id();
	$_SESSION["auth"] = $row["id"];
	$_SESSION["admin"] = $row["admin"];
}
else {
	echo "Login failed.<br>\n";
}
// Disconnect
mysql_close();

header("location: index.php?page=ciph");
?>
