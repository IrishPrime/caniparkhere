<?php
# List all exceptions, sorted by date.
require_once("./_logic.php");

$exceptions = GetExceptionsByLot();
?>

<div id="accordion">
<?php
if(!empty($exceptions)) {
	foreach($exceptions as $exception_group) {
		// If the lot has exceptions construct a header
		echo "\t<h1><a href=\"#\">".$exception_group["name"]."</a></h1>\n";
		echo "\t<div>\n";
		ui_info("<strong>".$exception_group["description"]."</strong>");

		foreach($exception_group["exceptions"] as $exception) {
			// Print each exception
			echo "<p class=\"ui-state-default ui-corner-all ui-helper-clearfix\" style=\"padding:0px;\">";
			echo "<span class=\"ui-icon ui-icon-calendar\" style=\"position:relative; float:left; margin:1.3em 1em;\"></span>";
			echo date("F d, Y H:i", strtotime($exception["start"]))."<br/>\n";
			echo date("F d, Y H:i", strtotime($exception["end"]))."<br/>\n";
			echo ($exception["allowed"] == 0) ? "<span style=\"color: #FF0000;\">Disallow</span>" : "<span style=\"color: #00FF00;\">Allow</span>";
			echo "</p>";
			foreach($exception["passTypes"] as $pass) {
				echo $pass["name"]."<br/>\n";
			}
		}
		echo "\t</div>\n";
	}
}
?>
</div>
