<?php
	session_start();
	require("./_settings.php");
	isset($_GET["page"]) ? $page = $_GET["page"] : $page = "ciph";
	if(!is_file("$page.php")) $page="404";
?>

<html>
<head>
	<title>Can I Park Here? - <?php echo $title[$page]; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=8" />
	<style type="text/css">@import url("ciph.css");</style>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
	<script type="text/javascript" src="./maps.js"></script>
	<script type="text/javascript" src="./ciph.js"></script>
</head>

<body>
<!-- Header -->
<div id="header">
</div>

<!-- Menu -->
<div id="menu">
	<ul>
	<li><a href="?page=ciph" title="<?php echo $title["ciph"]; ?>">Can I Park Here?</a></li>
		<li><a href="?page=wcip" title="<?php echo $title["wcip"]; ?>">Where Can I Park?</a></li>
		<li><a href="?page=wdip" title="<?php echo $title["wdip"]; ?>">Where Did I Park?</a></li>
		<li><a href="?page=rules" title="<?php echo $title["rules"]; ?>">Rules</a></li>
		<li><a href="?page=news" title="<?php echo $title["news"]; ?>">News</a></li>
		<li><a href="?page=FAQ" title="<?php echo $title["FAQ"]; ?>"><acronymn title="Frequently Asked Questions">FAQ</acronymn></a></li>
		<div><?php include("login.php"); ?></div>
	</ul>
</div>

<!-- Content -->
<div id="content">
	<?php include("$page.php"); ?>
</div>

<!-- Footer -->
<div id="footer">
	<small>&copy; Michael O'Neill &amp; Matthew Burkhard 2010</small><br />
	<img src="./images/php-power-micro2.png" alt="PHP" border="0" />
	<img src="./images/MySQL.gif" alt="MySQL" border="0" />
	<img src="./images/Google-Maps.gif" alt="Google Maps" border="0" />
</div>
</body>
</html>
