<?php
	session_start();
	require("./_settings.php");
	isset($_GET["page"]) ? $page = $_GET["page"] : $page = "ciph";
	if(!is_file("$page.php")) $page="404";
	
	require_once("./_logic.php");
	$globalSettings = GetSettingsForUser(0);
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Can I Park Here? - <?php echo $title[$page]; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=8"/>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<style type="text/css">@import url("./css/ciph.css");</style>
	<style type="text/css">@import url("./css/jquery-ui-1.8.custom.css");</style>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
	<script type="text/javascript" src="./js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="./js/jquery-ui-1.8.custom.min.js"></script>
	<script type="text/javascript" src="./js/ciph.js"></script>
</head>

<body>
<!-- Header -->
<div id="header"></div>

<!-- Menu -->
<div id="menu">
	<ul>
	<li><a href="?page=ciph" title="<?php echo $title["ciph"]; ?>">Can I Park Here?</a></li>
		<li><a href="?page=wcip" title="<?php echo $title["wcip"]; ?>">Where Can I Park?</a></li>
		<li><a href="?page=wdip" title="<?php echo $title["wdip"]; ?>">Where Did I Park?</a></li>
		<li><a href="?page=rules" title="<?php echo $title["rules"]; ?>">Rules</a></li>
		<li><a href="?page=news" title="<?php echo $title["news"]; ?>">News</a></li>
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
	<img src="./images/php-power-micro2.png" alt="PHP" border="0"/>
	<img src="./images/MySQL.gif" alt="MySQL" border="0"/>
	<img src="./images/Google-Maps.gif" alt="Google Maps" border="0"/>
	<img src="./images/ui-jquery.png" alt="jQuery UI" border="0"/>
</div>
</body>
</html>
