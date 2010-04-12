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
	"admin-exceptions" => "Administration Tools | Exceptions &amp; Events",
	"admin-lots" => "Administration Tools | Parking Lots",
	"admin-passes" => "Administration Tools | Permit Types",
	"admin-rules" => "Administration Tools | Rules &amp; Regulations",
	"admin-schemes" => "Administration Tools | Color Schemes",
	"admin-users" => "Administration Tools | User Permissions",
	"ciph" => "Home",
	"news" => "News &amp; Updates",
	"register" => "Register a User Account",
	"rules" => "Rules &amp; Regulations",
	"user-profile" => "Edit User Profile",
	"wcip" => "Where Can I Park?",
	"wdip" => "Where Did I Park?",
);

// UI Elements
$ui_info = "\t<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 0px; padding: 0 .7em;\">\n\t\t<span class=\"ui-icon ui-icon-info\" style=\"float: left; margin: .1em .3em 0 0;\"></span>\n";
$ui_alert = "\t<div class=\"ui-state-error ui-corner-all\" style=\"margin-top: 0px; padding: 0 .7em;\">\n\t\t<span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin: .1em .3em 0 0;\"></span>\n";
?>
