<?php
// Database info
$mysql_server = "localhost:3306";
$mysql_user = "mysql";
$mysql_password = "cpsc123";
$mysql_db_name = "ciph";

// WARNING: Changing the password salt on a live system will cause all accounts to quit working.
$password_salt = "C!p|-|4Mg501337";

// Default location
$default_location = "34.668717,-82.837134";

$session_duration = 60*60*24*365;

$dotw = array(
	0 => "Sunday",
	1 => "Monday",
	2 => "Tuesday",
	3 => "Wednesday",
	4 => "Thursday",
	5 => "Friday",
	6 => "Saturday",
);

$title = array(
	"404" => "Page Not Found",
	"FAQ" => "Frequently Asked Questions",
	"admin" => "Administration Tools",
	"admin-exceptions" => "Administration Tools",
	"admin-lots" => "Administration Tools",
	"admin-passes" => "Administration Tools",
	"admin-rules" => "Administration Tools",
	"admin-users" => "Administration Tools",
	"ciph" => "Home",
	"news" => "News & Updates",
	"register" => "Register a User Account",
	"rules" => "Rules & Regulations",
	"user-profile" => "Edit User Profile",
	"wcip" => "Where Can I Park?",
	"wdip" => "Where Did I Park?",
);
?>
