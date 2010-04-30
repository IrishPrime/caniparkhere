<?php
# Install Can I Park Here? on a new system.

# Defined for interal use only. Do not modify.
@define("SETTINGS_FILE", "./_settings.php");
if(is_file(SETTINGS_FILE)) die("Can I Park Here? appears to be installed.");

if(!empty($_POST)) {
	if(!get_magic_quotes_gpc()) array_map("addslashes", $_POST);
	foreach($_POST as $k => $v) {
		# jQuery should prevent the form from being submitted without all required fields, but just in case...
		if(empty($_POST["$k"])) die("Incomplete form.");
		else $_POST["$k"] = addslashes($v);
	}

	if($fp = @fopen(SETTINGS_FILE, "w")) {
		@fwrite($fp, "<?php\n");
		@fwrite($fp, "# Database info\n");
		@fwrite($fp, "@define(\"MYSQL_SERVER\", \"".$_POST["install_mysql_server"].":".$_POST["install_mysql_port"]."\");\n");
		@fwrite($fp, "@define(\"MYSQL_USER\", \"".$_POST["install_mysql_user"]."\");\n");
		@fwrite($fp, "@define(\"MYSQL_PASSWORD\", \"".$_POST["install_mysql_password"]."\");\n");
		@fwrite($fp, "@define(\"MYSQL_DB\", \"".$_POST["install_mysql_database"]."\");\n\n");

		@fwrite($fp, "// WARNING: Changing the password salt on a live system will cause all accounts to quit working.\n");
		@fwrite($fp, "// If the password salt is changed it will also need to be changed in the Android application.\n");
		@fwrite($fp, "// This prevents the need to send unencrypted passwords while maintaining the password salt.\n");
		@fwrite($fp, "@define(\"SALT\", \"C!p|-|4Mg501337\");\n");
		@fwrite($fp, "@define(\"SESSION_DURATION\", 60*60*24*365);\n");
		@fwrite($fp, "@define(\"MAINTAINER\", \"".$_POST["install_admin_email"]."\");\n\n");

		@fwrite($fp, "\$dotw = array(\n");
			@fwrite($fp, "\t0 => \"Sunday\",\n");
			@fwrite($fp, "\t1 => \"Monday\",\n");
			@fwrite($fp, "\t2 => \"Tuesday\",\n");
			@fwrite($fp, "\t3 => \"Wednesday\",\n");
			@fwrite($fp, "\t4 => \"Thursday\",\n");
			@fwrite($fp, "\t5 => \"Friday\",\n");
			@fwrite($fp, "\t6 => \"Saturday\",\n");
		@fwrite($fp, ");\n\n");

		@fwrite($fp, "\$title = array(\n");
			@fwrite($fp, "\t\"404\" => \"Page Not Found\",\n");
			@fwrite($fp, "\t\"FAQ\" => \"Frequently Asked Questions\",\n");
			@fwrite($fp, "\t\"api\" => \"Web Services\",\n");
			@fwrite($fp, "\t\"admin\" => \"Administration Tools\",\n");
			@fwrite($fp, "\t\"admin-exceptions\" => \"Administration Tools | Exceptions &amp; Events\",\n");
			@fwrite($fp, "\t\"admin-lots\" => \"Administration Tools | Parking Lots\",\n");
			@fwrite($fp, "\t\"admin-passes\" => \"Administration Tools | Permit Types\",\n");
			@fwrite($fp, "\t\"admin-rules\" => \"Administration Tools | Rules &amp; Regulations\",\n");
			@fwrite($fp, "\t\"admin-schemes\" => \"Administration Tools | Color Schemes\",\n");
			@fwrite($fp, "\t\"admin-users\" => \"Administration Tools | User Permissions\",\n");
			@fwrite($fp, "\t\"ciph\" => \"Home\",\n");
			@fwrite($fp, "\t\"exceptions\" => \"Exceptions\",\n");
			@fwrite($fp, "\t\"register\" => \"Register a User Account\",\n");
			@fwrite($fp, "\t\"rules\" => \"Rules &amp; Regulations\",\n");
			@fwrite($fp, "\t\"user-profile\" => \"Edit User Profile\",\n");
			@fwrite($fp, "\t\"wcip\" => \"Where Can I Park?\",\n");
			@fwrite($fp, "\t\"wdip\" => \"Where Did I Park?\",\n");
		@fwrite($fp, ");\n\n");

		@fwrite($fp, "# Dialogs\n");
		@fwrite($fp, "// TODO: Should consider replacing with functions to generate UI elements to reduce duplicate code. Also move to index.php.\n");
		@fwrite($fp, "\$ui_help_create = \"<a href=\\\"#\\\" id=\\\"create_help_opener\\\" class=\\\"ui-state-default ui-corner-all\\\" style=\\\"padding: .2em .8em .2em 1.4em;text-decoration: none;position: relative;\\\"><span class=\\\"ui-icon ui-icon-help\\\" style=\\\"margin:0 .2em 0 0; position:absolute; left:.2em; top:50%; margin-top:-8px;\\\"></span>Help</a>\";\n");
		@fwrite($fp, "\$ui_help_delete = \"<a href=\\\"#\\\" id=\\\"delete_help_opener\\\" class=\\\"ui-state-default ui-corner-all\\\" style=\\\"padding:.2em .8em .2em 1.4em; text-decoration:none; position:relative;\\\"><span class=\\\"ui-icon ui-icon-help\\\" style=\\\"margin:0 .2em 0 0; position:absolute; left:.2em; top:50%; margin-top:-8px;\\\"></span>Help</a>\";\n");
		@fwrite($fp, "\$ui_help_modify = \"<a href=\\\"#\\\" id=\\\"modify_help_opener\\\" class=\\\"ui-state-default ui-corner-all\\\" style=\\\"padding:.2em .8em .2em 1.4em; text-decoration:none; position:relative;\\\"><span class=\\\"ui-icon ui-icon-help\\\" style=\\\"margin: 0 .2em 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;\\\"></span>Help</a>\";\n");
		@fwrite($fp, "?>");

		@fclose($fp);

		include(SETTINGS_FILE);
		mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or die(mysql_error());
		mysql_select_db(MYSQL_DB) or die(mysql_error());
		$schema = explode(";", file_get_contents("./sql/ciph_schema.sql"));

		foreach($schema as $query) {
			mysql_query($query);
		}
		mysql_query("INSERT INTO `users` VALUES ('1', '".$_POST["install_admin_fname"]."', '".$_POST["install_admin_lname"]."', '".$_POST["install_admin_email"]."', '".sha1($_POST["install_admin_password_1"].SALT)."', '', '', '1')");
		$extract = preg_replace("/\((.+)\)/", "$1", $_POST["install_map_coords"]);
		mysql_query("UPDATE `settings` SET value='$extract' WHERE id='1'");
		mysql_query("UPDATE `settings` SET value='".$_POST["install_map_zoom"]."' WHERE id='6'");
		mysql_close();

		header("location: ./index.php");
	} else {
		die("Failed to open ".SETTINGS_FILE." for writing.");
	}
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="SHORTCUT ICON" href="./favicon.ico"/>
		<meta http-equiv="X-UA-Compatible" content="IE=8"/>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
		<style type="text/css">@import url("./css/ciph.css");</style>
		<style type="text/css">@import url("./css/jquery-ui-1.8.custom.css");</style>
		<title>Can I Park Here? - Install</title>
		<script type="text/javascript" src="./js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="./js/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="./js/ciph.js"></script>
		<script type="text/javascript" src="http://code.google.com/apis/gears/gears_init.js"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
		<script type="text/javascript">
		var map;
		function initialize() {

			var myOptions = {
				center: new google.maps.LatLng(0, 0),
				disableDoubleClickZoom: true,
				mapTypeControl: true,
				mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				navigationControl: true,
				navigationControlOptions: { style: google.maps.NavigationControlStyle.SMALL },
				scaleControl: false,
				scrollwheel: true,
				zoom: 1,
			}

			// create map on page
			map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

			google.maps.event.addListener(map, 'center_changed',
				function() {
					writeCenter();
				});

			google.maps.event.addListener(map, 'zoom_changed',
				function() {
					writeZoom();
				});

			//write first time
			writeCenter();
			writeZoom();

			function writeCenter() {
				document.getElementById("install_map_coords").value = map.getCenter().toString();
			}
			function writeZoom() {
				document.getElementById("install_map_zoom").value = map.getZoom().toString();
			}
		}
		$(document).ready(function() {
			initialize();
			$("#install_form").validate({
				rules: {
					install_mysql_server: {
						required: true,
					},
					install_mysql_port: {
						required: true,
						digits: true,
					},
					install_mysql_user: {
						required: true,
					},
					install_mysql_password: {
						required: true,
					},
					install_mysql_database: {
						required: true,
					},
					install_admin_fname: {
						required: true,
					},
					install_admin_lname: {
						required: true,
					},
					install_admin_email: {
						required: true,
						email: true,
					},
					install_admin_password_1: {
						required: true,
					},
					install_admin_password_2: {
						equalTo: "#install_admin_password_1",
					},
					install_map_coords: {
						required: true,
					},
					install_map_zoom: {
						required: true,
						range: [1, 20],
					},
				},
			});
		});
		</script>
	</head>

	<body>
		<!-- Header -->
		<div id="header"></div>

		<!-- Content -->
		<div id="content">
			<div id="tabs">
				<ul>
					<li><a href="#install_tab">Install</a></li>
				</ul>

				<!-- Install -->
				<div id="install_tab">

					<form id="install_form" method="POST" action="">
						<label for="install_mysql_server">MySQL Server</label>
						<input id="install_mysql_server" name="install_mysql_server" type="text"/>
						<br/>

						<label for="install_mysql_port">MySQL Port</label>
						<input id="install_mysql_port" name="install_mysql_port" type="text" value="3306"/>
						<br/>

						<label for="install_mysql_user">MySQL User</label>
						<input id="install_mysql_user" name="install_mysql_user" type="text"/>
						<br/>

						<label for="install_mysql_password">MySQL Password</label>
						<input id="install_mysql_password" name="install_mysql_password" type="text"/>
						<br/>

						<label for="install_mysql_database">MySQL Database</label>
						<input id="install_mysql_database" name="install_mysql_database" type="text"/>
						<hr/>

						<label for="install_admin_fname">Admin. First Name</label>
						<input id="install_admin_fname" name="install_admin_fname" type="text"/>
						<br/>

						<label for="install_admin_lname">Admin. Last Name</label>
						<input id="install_admin_lname" name="install_admin_lname" type="text"/>
						<br/>

						<label for="install_admin_email">Admin. E-Mail</label>
						<input id="install_admin_email" name="install_admin_email" type="text"/>
						<br/>

						<label for="install_admin_password_1">Admin. Password</label>
						<input id="install_admin_password_1" name="install_admin_password_1" type="password"/>
						<br/>

						<label for="install_admin_password_2">Confirm Password</label>
						<input id="install_admin_password_2" name="install_admin_password_2" type="password"/>
						<hr/>

						<label for="install_map_coords">Map Center (Lat, Lng)</label>
						<input id="install_map_coords" name="install_map_coords" type="text" value="34.6766, -82.8343"/>
						<br/>

						<label for="install_map_zoom">Map Zoom</label>
						<input id="install_map_zoom" name="install_map_zoom" type="text" value="16"/>
						<br/>

						<p><input type="submit" value="Install CIPH"/></p>
					</form>

					<!-- Help -->
					<a href="#" id="create_help_opener" class="ui-state-default ui-corner-all" style="padding: .2em .8em .2em 1.4em;text-decoration: none;position: relative;"><span class="ui-icon ui-icon-help" style="margin:0 .2em 0 0; position:absolute; left:.2em; top:50%; margin-top:-8px;"></span>Help</a>

					<div id="create_help_dialog" title="Install Help">
						<h2>MySQL</h2>
						<p>Your network administrator should provide you with the following information.</p>
						<p><strong>MySQL Server</strong>: Hostname or IP address of MySQL server on which to install Can I Park Here? localhost or 127.0.0.1 if the MySQL server will always be on the same system as the website.</p>
						<p><strong>MySQL Port</strong>: Port on which MySQL accepts connections. 3306 is the default, contact your network administrator to confirm.</p>
						<p><strong>MySQL User</strong>: MySQL user name with <em>DROP</em>, <em>CREATE</em>, <em>INSERT</em> and <em>DELETE</em> permissions. Using root is strongly discouraged.</p>
						<p><strong>MySQL Password</strong>: Password for the associated <strong>MySQL User</strong>.</p>
						<p><strong>MySQL Database</strong>: The name of the database in which <em>Can I Park Here?</em> will create tables. The database should be created by your network or systems administrator and permissions granted to the <strong>MySQL User</strong> you wish to use. Existing tables will be dropped.</p>

						<h2>Administrator</h2>
						<p>The account created here will be used to log into the <em>Can I Park Here?</em> website. This account will have permission to create and edit <em>Parking Lots</em>, <em>Pass Types</em>, <em>Rules</em>, <em>Exceptions</em>, and <em>Color Schemes</em>. This account will also be able to promote additional users to an administrator level with the same permissions.</p>
						<p><strong>Admin. First Name</strong>: Your first name.</p>
						<p><strong>Admin. Last Name</strong>: Your last name.</p>
						<p><strong>Admin. E-Mail</strong>: The e-mail address you wish to login with.</p>
						<p><strong>Admin. Password</strong>: The password for your account, not the <strong>MySQL User</strong>.</p>
						<p><strong>Confirm Password</strong>: Your password will be salted and hashed with SHA-1 encryption. This means you will not be able to retrieve your password in clear text or have it sent to you.</p>

						<h2>Map</h2>
						<p>The default settings for the map displayed on the <em>Can I Park Here?</em> and <em>Where Can I Park?</em> pages. <em>Where Did I Park?</em> may start at this location, but will center on the user's last known location.</p>
						<p><strong>Map Center (Lat, Lng)</strong>: <em>Latitude</em> and <em>Longitude</em> on which the map should center. Use the map at the bottom of the page to select the default view.</p>
						<p><strong>Map Zoom</strong>: The default zoom level of all maps displayed on the site. The previous tool should be helpful for this, as well.</p>
					</div>
				</div>
					<!-- Map -->
					<div id="map_canvas" style="height:600px; width:100%;"></div>

			</div>
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
