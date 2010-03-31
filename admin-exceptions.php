<?php
# Create/delete temporary parking rule exceptions for limited time events.
require("./auth.php");
require_once("./_logic.php");

switch($_POST["action"]) {
	case "create":
		$startDate = addslashes($_POST["create_start_date"]);
		$endDate = addslashes($_POST["create_end_date"]);
		$startTime = $_POST["create_start_hour"] . ":" . $_POST["create_start_minute"] . ":00";
		$endTime = $_POST["create_end_hour"] . ":" . $_POST["create_end_minute"] . ":00";
		$newRuleIds = CreateRule($_POST["create_lots"], $_POST["create_passes"],
			(string)$startDate, (string)$endDate, (string)$startTime, (string)$endTime,
			implode($_POST["create_days"], ","));
		if ($newRuleIds != null) print_r($newRuleIds);
		else echo "Insert failed.";
		break;
	case "delete":
		foreach($_POST["delete_rules"] as $id)
			DeleteRule(addslashes($id));
		break;
	default:
		break;
}

$passes = GetPassTypes("name");
$lots = GetLots("name");
?>

<script type="text/javascript">
$(document).ready(function() {
	$("#create_start_datepicker").datepicker({
		dateFormat: "mm-dd-yy",
		altField: "#create_start_date",
		altFormat: "yy-mm-dd",
		minDate: "0d",
		showAnim: "drop",
  	});

	$("#create_end_datepicker").datepicker({
		dateFormat: "mm-dd-yy",
		altField: "#create_end_date",
		altFormat: "yy-mm-dd",
		minDate: "0d",
		showAnim: "drop",
  	});

	$("#create_days_checks").buttonset();
});
</script>

<div id="accordion">
	<div>
		<h1><a href="#">Create Rule</a></h1>
		<div>
			<form name="create" id="create" method="POST" action="">
				<!-- Start Date/Time -->
				<label for="create_start_datepicker">Start Date</label>
				<input type="text" name="create_start_datepicker" id="create_start_datepicker"/>
				<input type="hidden" name="create_start_date" id="create_start_date"/>
				<label for="create_start_hour">Start Time</label>
				<select name="create_start_hour" id="create_start_hour">
					<optgroup label="Hour">
					<?php
						for($i = 0; $i <= 23; $i++) {
							echo "<option value=\"$i\">";
							printf("%02d", $i);
							echo "</option>";
						}
					?>
					</optgroup>
				</select>
				:
				<select name="create_start_minute" id="create_start_minute">
					<optgroup label="Minute">
					<?php
						for($i = 0; $i <= 59; $i++) {
							echo "<option value=\"$i\">";
							printf("%02d", $i);
							echo "</option>";
						}
					?>
					</optgroup>
				</select>
				<br/>

				<!-- End Date/Time -->
				<label for="create_end_datepicker">End Date</label>
				<input type="text" name="create_end_datepicker" id="create_end_datepicker"/>
				<input type="hidden" name="create_end_date" id="create_end_date"/>
				<label for="create_end_hour">End Time</label>
				<select name="create_end_hour" id="create_end_hour">
					<optgroup label="Hour">
					<?php
						for($i = 0; $i <= 23; $i++) {
							echo "<option value=\"$i\">";
							printf("%02d", $i);
							echo "</option>";
						}
					?>
					</optgroup>
				</select>
				:
				<select name="create_end_minute" id="create_end_minute">
					<optgroup label="Minute">
					<?php
						for($i = 0; $i <= 59; $i++) {
							echo "<option value=\"$i\">";
							printf("%02d", $i);
							echo "</option>";
						}
					?>
					</optgroup>
				</select>
				<br/>

				<!-- Days -->
				<label for="create_days_checks">Days</label>
				<div name="create_days_checks" id="create_days_checks">
					<input type="checkbox" name="create_days[]" id="create_sunday" value="0"/>
					<label for="create_sunday">Sunday</label>

					<input type="checkbox" name="create_days[]" id="create_monday" value="1"/>
					<label for="create_monday">Monday</label>

					<input type="checkbox" name="create_days[]" id="create_tuesday" value="2"/>
					<label for="create_tuesday">Tuesday</label>

					<input type="checkbox" name="create_days[]" id="create_wednesday" value="3"/>
					<label for="create_wednesday">Wednesday</label>

					<input type="checkbox" name="create_days[]" id="create_thursday" value="4"/>
					<label for="create_thursday">Thursday</label>

					<input type="checkbox" name="create_days[]" id="create_friday" value="5"/>
					<label for="create_friday">Friday</label>

					<input type="checkbox" name="create_days[]" id="create_saturday" value="6"/>
					<label for="create_saturday">Saturday</label>
				</div>
				<br/>

				<!-- Pass Types -->
				<select name="create_passes[]" id="create_passes" multiple="multiple" size="15">
					<optgroup label="Passes">
					<?php
						if(is_array($passes))
							foreach($passes as $pass)
								echo "<option value=\"".$pass["id"]."\">".$pass["name"]."</option>\n";
					?>
					</optgroup>
				</select>

				<!-- Parking Lots -->
				<select name="create_lots[]" id="create_lots" multiple="multiple" size="15">
					<optgroup label="Parking Lots" id="test">
					<?php
						if(is_array($lots))
							foreach($lots as $lot)
								echo "<option value=\"".$lot["id"]."\">".$lot["name"]."</option>\n";
					?>
					</optgroup>
				</select>
				<input type="hidden" name="action" value="create"/>
				<p><input type="submit" value="Create Rule"/></p>
			</form>
		</div>
	</div>

	<div id="nested_accordion">
		<h1><a href="#">Delete Rules</a></h1>
		<div>
			<form name="delete" id="delete" method="POST" action="">
				<?php
					if(is_array($lots))
						foreach($lots as $lot) {
							echo "<h2 style=\"margin-top:0\"><a href=\"#\">".$lot["name"]."</a></h2>";
							$rules = $lot["rules"];
							echo "<div>";
							if(is_array($rules))
								foreach($rules as $rule) {
									echo "<input type=\"checkbox\" name=\"delete_rules[]\" id=\"rule_".$rule["id"]."\" value=\"".$rule["id"]."\"/>";
									echo "<label for=\"rule_".$rule["id"]."\">".date("F d, Y", strtotime($rule["startDate"]))." - ".date("F d, Y", strtotime($rule["endDate"]))."</label><br/>";
									$days = explode(",", $rule["days"]);
									if(is_array($days))
										foreach($days as $day)
											echo $dotw[$day]." ";
									echo "<br/>";
									echo "From ".$rule["startTime"]." until ".$rule["endTime"]."<br/>";
									echo $passes[$rule["passTypeId"]]["name"];
									echo "<br/><br/>";
								}
							else echo "No rules.\n";
							echo "</div>\n";
						}
				?>
				<input type="hidden" name="action" value="delete"/>
				<br/>
				<input type="submit" value="Delete Rules"/>
			</form>
		</div>
	</div>
</div>
