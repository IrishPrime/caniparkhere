<?php
	if(!isset($_COOKIE["auth"]) || (trim($_COOKIE["auth"])=='')) {
		include("./login-form.php");
	}
	else {
		if(isset($_COOKIE["admin"]) && $_COOKIE["admin"] == "1") {
			echo "<li><a href=\"?page=admin\" title=\"".$title["admin"]."\">Admin Tools</a>";
			include("admin.php");
			echo "</li>";
		}
		echo "<span id=\"login\"><li><a href=\"?page=user-profile\" title=\"".$title["user-profile"]."\">Edit Profile</a></li>";
		echo "<li><a href=\"logout.php\" title=\"".$title["logout"]."\">Logout</a></li></span>";
	}
?>
