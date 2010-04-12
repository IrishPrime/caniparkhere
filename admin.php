<?php
require_once("./_settings.php");
require("./auth.php");
?>
<ul>
	<li><a href="?page=admin-rules" title="<?php echo $title["admin-rules"]; ?>">Rules</a></li>
	<li><a href="?page=admin-exceptions" title="<?php echo $title["admin-exceptions"]; ?>">Exceptions</a></li>
	<li><a href="?page=admin-lots" title="<?php echo $title["admin-lots"]; ?>">Parking Lots</a></li>
	<li><a href="?page=admin-passes" title="<?php echo $title["admin-passes"]; ?>">Pass Types</a></li>
	<li><a href="?page=admin-schemes" title="<?php echo $title["admin-schemes"]; ?>">Color Schemes</a></li>
	<li><a href="?page=admin-users" title="<?php echo $title["admin-users"]; ?>">Users</a></li>
</ul>
