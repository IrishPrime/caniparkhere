<?php
# List all exceptions, sorted by date.
require_once("./_logic.php");

$passes = GetPassTypes();
$lots = GetLots("name");
?>

<div id="accordion">
<?php
if(is_array($lots)) {
	foreach($lots as $lot) {
		$lot["exceptions"] = GetExceptionsByLot($lot["id"]);

		if(!empty($lot["exceptions"])) {
			// If the lot has exceptions construct a header
			echo "\t<h1><a href=\"#\">".$lot["name"]."</a></h1>";
			echo "\t<div>";
			echo "\t\t<div class=\"ui-widget\">\n$ui_info";
			echo "\t\t\t\t<strong>".$lot["description"]."&nbsp;</strong>\n";
			echo "\t\t\t</div>\n";
			echo "\t\t</div>\n";

			foreach($lot["exceptions"] as $exception) {
				// Print each exception
				echo "<p class=\"ui-state-default ui-corner-all ui-helper-clearfix\" style=\"padding:0px;\">";
				echo "<span class=\"ui-icon ui-icon-calendar\" style=\"position:relative; float:left; margin:1.3em 1em;\"></span>";
				echo date("F d, Y H:i", strtotime($exception["start"]))."<br/>\n";
				echo date("F d, Y H:i", strtotime($exception["end"]))."<br/>\n";
				echo ($exception["allowed"] == 0) ? "<span style=\"color: #FF0000;\">Disallow</span>" : "<span style=\"color: #00FF00;\">Allow</span>";
				echo "</p>";
				echo "<p>".$passes[$exception["passType"]]["name"]."</p>\n";
			}
			echo "\t</div>\n";
		}
	}
} else {
	echo "<h1><a href=\"#\">Chaos</a></h1>\n";
	echo "<div>No rules defined. Expect riots and looting any moment now.</div>\n";
}
?>
</div>
