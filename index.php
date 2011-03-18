<?php
	session_start();
	error_reporting(E_ALL ^ E_NOTICE);
	require_once("./_logic.php");

	isset($_GET["page"]) ? $page = $_GET["page"] : $page = "ciph";
	if(!is_file("$page.php")) $page="404";
	
	$globalSettings = GetSettingsForUser(0);

	# UI Elements
	function ui_alert($message) {
		echo "<div class=\"ui-widget\">\n";
		echo "\t<div class=\"ui-state-error ui-corner-all\" style=\"margin-top: 0px; padding: 0 .7em;\">\n";
		echo "\t\t<span class=\"ui-icon ui-icon-alert\" style=\"position:relative; float:left; margin:.1em .3em 0 0;\"></span>\n";
		echo "\t\t$message\n";
		echo "\t</div>\n";
		echo "</div>\n";
	}
	function ui_info($message) {
		echo "<div class=\"ui-widget\">\n";
		echo "\t<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 0px; padding: 0 .7em;\">\n";
		echo "\t\t<span class=\"ui-icon ui-icon-info\" style=\"position:relative; float:left; margin:.1em .3em 0 0;\"></span>\n";
		echo "\t\t$message\n";
		echo "\t</div>\n";
		echo "</div>\n";
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="SHORTCUT ICON" href="./favicon.ico"/>
	<meta http-equiv="X-UA-Compatible" content="IE=8"/>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<style type="text/css">@import url("./css/ciph.css");</style>
	<style type="text/css">@import url("./css/jquery-ui-1.8.10.custom.css");</style>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
	<script type="text/javascript" src="./js/jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="./js/jquery-ui-1.8.10.custom.min.js"></script>
	<script type="text/javascript" src="./js/ciph.js"></script>
	<title>Can I Park Here? - <?php echo $title[$page]; ?></title>
</head>

<body>
<!-- Header -->
<div id="header"></div>

<!-- Menu -->
<div id="menu">
	<ul>
		<li><a href="?page=ciph" title="<?php echo $title["ciph"]; ?>">Can I Park Here?</a></li>
		<?php
		if(isset($_COOKIE["passType"])) printf("<li><a href=\"?page=wcip\" title=\"%s\">Where Can I Park?</a></li>", $title["wcip"]);
		if(isset($_COOKIE["id"])) printf("<li><a href=\"?page=wdip\" title=\"%s\">Where Did I Park?</a></li>", $title["wdip"]);
		?>
		<li><a href="?page=rules" title="<?php echo $title["rules"]; ?>">Rules</a></li>
		<li><a href="?page=exceptions" title="<?php echo $title["exceptions"]; ?>">Exceptions</a></li>
		<li><a href="?page=FAQ" title="<?php echo $title["FAQ"]; ?>"><acronym title="Frequently Asked Questions">FAQ</acronym></a></li>
		<?php include("./login.php"); ?>
	</ul>
</div>

<!-- Content -->
<div id="content">
	<?php include("$page.php"); ?>
</div>

<!-- Footer -->
<div id="footer">
	<small>&copy; Michael O'Neill &amp; Matthew Burkhard 2010</small><br/>
	<a href="http://www.php.net/"><img src="./images/php-power-micro2.png" alt="PHP" border="0"/></a>
	<a href="http://www.mysql.com/"><img src="./images/MySQL.gif" alt="MySQL" border="0"/></a>
	<a href="http://maps.google.com/"><img src="./images/Google-Maps.gif" alt="Google Maps" border="0"/></a>
	<a href="http://jqueryui.com/"><img src="./images/ui-jquery.png" alt="jQuery UI" border="0"/></a>
</div>
</body>
</html>
