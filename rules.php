<?php
# List all rules, sorted by lot or permit type.
require_once("./_logic.php");

$lots = GetRulesByLot(null);

switch($_POST["action"]) {
	case "sort_by_lot":
		break;
	case "sort_by_permit":
		break;
	default:
		break;
}
?>

<div id="accordion">
<?php
if(is_array($lots)) {
	foreach($lots as $lot) {
		if(!empty($lot["rules"])) {
			echo "\t<h1><a href=\"#\">".$lot["name"]."</a></h1>";
			echo "\t<div>";
			echo "\t\t<div class=\"ui-widget\">\n";
			echo "\t\t\t<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 0px; padding: 0 .7em;\">\n";
			echo "\t\t\t\t<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>\n";
			echo "\t\t\t\t<strong>".$lot["description"]."&nbsp;</strong></p>\n";
			echo "\t\t\t</div>\n";
			echo "\t\t</div>\n";

			foreach($lot["rules"] as $rule) {
				echo "<p class=\"ui-state-default ui-corner-all ui-helper-clearfix\" style=\"padding:0px;\">";
				echo "<span class=\"ui-icon ui-icon-calendar\" style=\"float:left; margin:1.3em 1em;\"></span>";
				echo date("F d, Y", strtotime($rule["startDate"]))." - ".date("F d, Y", strtotime($rule["endDate"]))."<br/>\n";
				echo date("H:i A", strtotime($rule["startTime"]))." - ".date("H:i A", strtotime($rule["endTime"]))."<br/>\n";
				$days = explode(",", $rule["days"]);
				if(is_array($days))
					foreach($days as $day)
						echo $dotw[$day]." ";
				echo "</p>";
				if(is_array($rule["passTypes"]))
					foreach($rule["passTypes"] as $pass)
						echo $pass["name"]."<br/>";
			}
			echo "\t</div>";
		}
	}
}
else {
	echo "<h1><a href=\"#\">Chaos</a></h1>\n";
	echo "<div>No rules defined. Expect riots and looting any moment now.</div>\n";
}
?>
</div>
