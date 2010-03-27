<?php
require_once("./_settings.php");

if(isset($_COOKIE["admin"]) && $_COOKIE["admin"] == "1") {
	// Create connection
	mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect: " . mysql_error());
	mysql_select_db($mysql_db_name) or die(mysql_error());

	// Successful connection, setup queries
	$sql = "SELECT password, admin FROM users WHERE id=".addslashes($_COOKIE["id"]);
	$result = mysql_query($sql);

	if($row = mysql_fetch_assoc($result)) {
		if($row["password"] != $_COOKIE["auth"] || $row["admin"] != 1) die("Access denied.\n");
	}
	else die("Access denied.\n");

	// Disconnect
	mysql_close();
}
else die("Access denied.\n");
?>
