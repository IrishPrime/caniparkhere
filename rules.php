<?php
# List all rules, sorted by lot or permit type.
require_once("./_logic.php");

$passes = GetPassTypes();
$lots = GetLots();

switch($_POST["action"]) {
	case "sort_by_lot":
		break;
	case "sort_by_permit":
		break;
	default:
		break;
}
?>

<script type="text/javascript">
</script>

<div id="accordion">
<?php
if(is_array($lots))
	foreach($lots as $lot) {
		echo "\t<h1><a href=\"#\">".$lot["name"]."</a></h1>";
		$rules = $lot["rules"];
		echo "\t<div>";
		if(is_array($rules))
			foreach($rules as $rule) {
				echo "<p style=\"margin-top:0;\"><b>".date("F d, Y", strtotime($rule["startDate"]))." - ".date("F d, Y", strtotime($rule["endDate"]))."</b><br/>";
				$days = explode(",", $rule["days"]);
				if(is_array($days))
					foreach($days as $day)
						echo $dotw[$day]." ";
				echo "<br/>";
				echo "From ".$rule["startTime"]." until ".$rule["endTime"]."<br/>";
				echo $passes[$rule["passTypeId"]]["name"]."</p>";
			}
		else echo "\t<p style=\"margin-top:0;\">No rules.</p>\n";
		echo "\t</div>";
	}
?>
</div>
