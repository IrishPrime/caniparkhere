<?php
	if(!isset($_SESSION["auth"]) || (trim($_SESSION["auth"])=='')) {
		include("login-form.php");
	}
	else {
		if(isset($_SESSION["admin"]) && $_SESSION["admin"] == "1")
			echo "<li><a href=\"?page=admin\" title=\"".$title["admin"]."\">Admin Tools</a></li>";
		echo "<li><a href=\"logout.php\" title=\"".$title["logout"]."\">Logout</a></li>";
	}
?>
