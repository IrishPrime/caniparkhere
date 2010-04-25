<?php
# List all rules, sorted by lot or permit type.
require_once("./_logic.php");

$lots = GetRulesByLot($_GET["lot"]);
?>

<div id="accordion">
<?php
if(is_array($lots)) {
	foreach($lots as $lot) {
		echo "\t<h1><a href=\"#\">".$lot["name"]."</a></h1>";
		echo "\t<div>";
		ui_info("<strong>".$lot["description"]."</strong>");

		foreach($lot["dateRange"] as $date_range) {
			echo "<p class=\"ui-state-default ui-corner-all ui-helper-clearfix\" style=\"padding:0px;\">";
			echo "<span class=\"ui-icon ui-icon-calendar\" style=\"position:relative; float:left; margin:.1em .5em;\">&nbsp;</span>";
			echo date("F d, Y", strtotime($date_range["startDate"]))." - ".date("F d, Y", strtotime($date_range["endDate"]))."</p>\n";
			foreach($date_range["timeRange"] as $time_range) {
				echo date("H:i", strtotime($time_range["startTime"]))." - ".date("H:i", strtotime($time_range["endTime"]))."<br/>\n";
				foreach($time_range["dow"] as $dow) {
					$days = explode(",", $dow["days"]);
					if(is_array($days)) foreach($days as $day) echo $dotw[$day]." ";
					echo "<br/>";
					if(is_array($dow["passTypes"])) {
						echo "<ul>\n";
						foreach($dow["passTypes"] as $pass) echo "<li>".$pass["name"]."</li>\n";
						echo "</ul>\n";
					}
				}
			}
		}
		echo "\t</div>";
	}
} else {
	echo "<h1><a href=\"#\">Chaos</a></h1>\n";
	echo "<div>No rules defined. Expect riots and looting any moment now.</div>\n";
}
?>
</div>
