<?php
# Create/delete recurring parking rules.
# TODO: Ensure proper data is being passed to Create/Delete.
# TODO: Remove comment markers and print_r's.

require("./auth.php");
require_once("./_logic.php");

$passes = GetPassTypes();
$lots = GetLots();

switch($_POST["action"]) {
	case "create":
		$startDate = $_POST["create_start_year"] . "-" . $_POST["create_start_month"] . "-" . $_POST["create_start_date"];
		$endDate = $_POST["create_end_year"] . "-" . $_POST["create_end_month"] . "-" . $_POST["create_end_date"];
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
?>

<script type="text/javascript">
var wd = false;
var we = false;
$(document).ready(function() {
	$("#weekdays").click(function () {
		var weekdays = "#create_monday, #create_tuesday, #create_wednesday, #create_thursday, #create_friday";
		$(weekdays).attr("checked", wd = !wd);
	});

	$("#weekends").click(function () {
		var weekends = "#create_sunday, #create_saturday";
		$(weekends).attr("checked", we = !we);
	});
	$("#datepicker").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yyyy-mm-dd", minDate: "0d" });
});
</script>
<div type="text" id="datepicker"></div>
<fieldset>
	<legend>Create Rule</legend>
	<form name="create" id="create" method="POST" action="">
		<!-- Start Date/Time -->
		<label for="create_start_month">Start Date</label>
		<select name="create_start_month" id="create_start_month">
			<optgroup label="Month">
			<?php
				for($i = 1; $i <= 12; $i++) {
					echo "<option value=\"$i\">";
					echo date("F", mktime(0, 0, 0, $i));
					echo "</option>";
				}
			?>
			</optgroup>
		</select>
		-
		<select name="create_start_date" id="create_start_date">
			<optgroup label="Date">
			<?php
				for($i = 1; $i <= 31; $i++) {
					echo "<option value=\"$i\">";
					echo $i;
					echo "</option>";
				}
			?>
			</optgroup>
		</select>
		-
		<select name="create_start_year" id="create_start_year">
			<optgroup label="Year">
			<?php
				for($i = 0; $i <= 5; $i++) {
					printf("<option value=\"%d\">", date("Y") + $i);
					echo date("Y") + $i;
					echo "</option>";
				}
			?>
			</optgroup>
		</select>
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
		<label for="create_end_month">End Date</label>
		<select name="create_end_month" id="create_end_month">
			<optgroup label="Month">
			<?php
				for($i = 1; $i <= 12; $i++) {
					echo "<option value=\"$i\">";
					echo date("F", mktime(0, 0, 0, $i));
					echo "</option>";
				}
			?>
			</optgroup>
		</select>
		-
		<select name="create_end_date" id="create_end_date">
			<optgroup label="Date">
			<?php
				for($i = 1; $i <= 31; $i++) {
					echo "<option value=\"$i\">";
					echo $i;
					echo "</option>";
				}
			?>
			</optgroup>
		</select>
		-
		<select name="create_end_year" id="create_end_year">
			<optgroup label="Year">
			<?php
				for($i = 0; $i <= 5; $i++) {
					printf("<option value=\"%d\">", date("Y") + $i);
					echo date("Y") + $i;
					echo "</option>";
				}
			?>
			</optgroup>
		</select>
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
		<input type="button" value="Weekdays" id="weekdays"/>
		<input type="button" value="Weekends" id="weekends"/>
		<br/>

		<!-- Permit Types -->
		<label for="create_passes[]">Permits</label>
		<select name="create_passes[]" id="create_passes" multiple="multiple">
			<optgroup label="Permits">
			<?php
				if(is_array($passes))
					foreach($passes as $pass)
						echo "<option value=\"".$pass["id"]."\">".$pass["name"]."</option>\n";
			?>
			</optgroup>
		</select>
		<br/>

		<!-- Parking Lots -->
		<label for="create_lots[]">Parking Lots</label>
		<select name="create_lots[]" id="create_lots" multiple="multiple">
			<optgroup label="Parking Lots">
			<?php
				if(is_array($lots))
					foreach($lots as $lot)
						echo "<option value=\"".$lot["id"]."\">".$lot["name"]."</option>\n";
			?>
			</optgroup>
		</select>
		<input type="hidden" name="action" value="create"/>
		<br/>
		<input type="submit" value="Create Rule"/>
	</form>
</fieldset>

<fieldset>
	<legend>Delete Rules</legend>
	<form name="delete" id="delete" method="POST" action="">
		<?php
			if(is_array($lots))
				foreach($lots as $lot) {
					echo "<h2 style=\"margin-top:0\">".$lot["name"]."</h2>";
					$rules = $lot["rules"];
					if(is_array($rules))
						foreach($rules as $rule) {
							echo "<p><input type=\"checkbox\" name=\"delete_rules[]\" id=\"rule_".$rule["id"]."\" value=\"".$rule["id"]."\"/>";
							echo "<b>".date("F d, Y", strtotime($rule["startDate"]))." - ".date("F d, Y", strtotime($rule["endDate"]))."</b><br/>";
							$days = explode(",", $rule["days"]);
							if(is_array($days))
								foreach($days as $day)
									echo $dotw[$day]." ";
							echo "<br/>";
							echo "From ".$rule["startTime"]." until ".$rule["endTime"]."<br/>";
							echo $passes[$rule["passTypeId"]]["name"];
							echo "</p>";
						}
					else echo "No rules.\n";
					echo "<hr/>\n";
				}
		?>
		<input type="hidden" name="action" value="delete"/>
		<br/>
		<input type="submit" value="Delete Rules"/>
	</form>
</fieldset>
