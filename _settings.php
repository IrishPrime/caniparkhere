<?php
# Database info
define(MYSQL_SERVER, "localhost:3306");
define(MYSQL_USER, "mysql");
define(MYSQL_PASSWORD, "cpsc123");
define(MYSQL_DB, "ciph");

// WARNING: Changing the password salt on a live system will cause all accounts to quit working.
// If the password salt is changed it will also need to be changed in the Android application.
// This prevents the need to send unencrypted passwords while maintaining the password salt.
define(SALT, "C!p|-|4Mg501337");
define(SESSION_DURATION, 60*60*24*365);
define(MAINTAINER, "");

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
	"api" => "Web Services",
	"admin" => "Administration Tools",
	"admin-exceptions" => "Administration Tools | Exceptions &amp; Events",
	"admin-lots" => "Administration Tools | Parking Lots",
	"admin-passes" => "Administration Tools | Permit Types",
	"admin-rules" => "Administration Tools | Rules &amp; Regulations",
	"admin-schemes" => "Administration Tools | Color Schemes",
	"admin-users" => "Administration Tools | User Permissions",
	"ciph" => "Home",
	"exceptions" => "Exceptions",
	"register" => "Register a User Account",
	"rules" => "Rules &amp; Regulations",
	"user-profile" => "Edit User Profile",
	"wcip" => "Where Can I Park?",
	"wdip" => "Where Did I Park?",
);

# Dialogs
// TODO: Should consider replacing with functions to generate UI elements to reduce duplicate. Also move to index.php.
$ui_help_create = "<a href=\"#\" id=\"create_help_opener\" class=\"ui-state-default ui-corner-all\" style=\"padding: .2em .8em .2em 1.4em;text-decoration: none;position: relative;\"><span class=\"ui-icon ui-icon-help\" style=\"margin:0 .2em 0 0; position:absolute; left:.2em; top:50%; margin-top:-8px;\"></span>Help</a>";
$ui_help_delete = "<a href=\"#\" id=\"delete_help_opener\" class=\"ui-state-default ui-corner-all\" style=\"padding:.2em .8em .2em 1.4em; text-decoration:none; position:relative;\"><span class=\"ui-icon ui-icon-help\" style=\"margin:0 .2em 0 0; position:absolute; left:.2em; top:50%; margin-top:-8px;\"></span>Help</a>";
$ui_help_modify = "<a href=\"#\" id=\"modify_help_opener\" class=\"ui-state-default ui-corner-all\" style=\"padding:.2em .8em .2em 1.4em; text-decoration:none; position:relative;\"><span class=\"ui-icon ui-icon-help\" style=\"margin: 0 .2em 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;\"></span>Help</a>";
?>
