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

<?php
	if(is_array($lots))
		foreach($lots as $lot) {
			echo "<fieldset><legend>".$lot["name"]."</legend>";
			$rules = $lot["rules"];
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
			else echo "<p style=\"margin-top:0;\">No rules.</p>\n";
			echo "</fieldset>\n";
		}
?>
