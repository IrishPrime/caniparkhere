<?php
# Create/delete recurring parking rules.

require("./auth.php");
require_once("./_logic.php");

switch($_POST["action"]) {
	case "create":
		$startDate = $_POST["create_start_date"];
		$endDate = $_POST["create_end_date"];
		$startTime = $_POST["create_start_hour"] . ":" . $_POST["create_start_minute"] . ":00";
		$endTime = $_POST["create_end_hour"] . ":" . $_POST["create_end_minute"] . ":00";
		$newRuleIds = @CreateRules($_POST["create_lots"], $_POST["create_passes"],
			(string)$startDate, (string)$endDate, (string)$startTime, (string)$endTime,
			implode($_POST["create_days"], ","));

		echo "<div class=\"ui-widget\">\n";
		echo ($newRuleIds != null) ? $ui_info : $ui_alert;
		echo "\t\tRules Created: <strong>".count($newRuleIds)."</strong>\n\t</div>\n</div>\n";
		break;
	case "delete":
		$results = @DeleteRules($_POST["delete_rules"]);

		echo "<div class=\"ui-widget\">\n";
		echo $results ? $ui_info : $ui_alert;
		echo "\t\tRules Deleted: <strong>".count($_POST["delete_rules"])."</strong>\n\t</div>\n</div>\n";
		break;
	default:
		break;
}

$passes = GetPassTypes("name");
$lots = GetLots("name");
$lot_rules = GetRulesByLot(null);
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#create_form").validate({
		rules: {
		},
	});

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

	$("#create_help_dialog").dialog({
		autoOpen: false,
		width: 600,
		show: "drop",
		hide: "drop",
	});
	$("#create_help_opener").hover(function() {
		$(this).toggleClass("ui-state-hover");
		return false;
	});
	$("#create_help_opener").click(function() {
		$("#create_help_dialog").dialog("open");
	});

	$("#delete_help_dialog").dialog({
		autoOpen: false,
		width: 600,
		show: "drop",
		hide: "drop",
	});
	$("#delete_help_opener").hover(function() {
		$(this).toggleClass("ui-state-hover");
		return false;
	});
	$("#delete_help_opener").click(function() {
		$("#delete_help_dialog").dialog("open");
	});
});
</script>

<div id="tabs">
	<ul>
		<li><a href="#create_tab">Create Rule</a></li>
		<li><a href="#delete_tab">Delete Rules</a></li>
	</ul>

	<!-- Create Tab -->
	<div id="create_tab">
		<form id="create_form" name="create" method="POST" action="">
			<!-- Start Date -->
			<label for="create_start_datepicker"><span class="ui-icon ui-icon-calendar" style="float: right; margin-right: .3em;"></span>Start Date</label>
			<input type="text" name="create_start_datepicker" id="create_start_datepicker" class="required"/>
			<input type="hidden" name="create_start_date" id="create_start_date"/>
			<br/>
			<!-- End Date -->
			<label for="create_end_datepicker"><span class="ui-icon ui-icon-calendar" style="float: right; margin-right: .3em;"></span>End Date</label>
			<input type="text" name="create_end_datepicker" id="create_end_datepicker" class="required"/>
			<input type="hidden" name="create_end_date" id="create_end_date"/>
			<br/>

			<!-- Start Time -->
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
			<!-- End Time -->
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

			<!-- Pass Types -->
			<select name="create_passes[]" id="create_passes" multiple="multiple" size="15" class="required">
				<optgroup label="Passes">
				<?php
					if(is_array($passes))
						foreach($passes as $pass)
							echo "<option value=\"".$pass["id"]."\">".$pass["name"]."</option>\n";
				?>
				</optgroup>
			</select>

			<!-- Parking Lots -->
			<select name="create_lots[]" id="create_lots" multiple="multiple" size="15" class="required">
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
		<a href="#" id="create_help_opener" class="ui-state-default ui-corner-all" style="padding: .4em 1em .4em 20px;text-decoration: none;position: relative;"><span class="ui-icon ui-icon-help" style="margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;"></span>Help</a>
		<div id="create_help_dialog" title="Create Rule Help">
			<p>Rules need only be defined for when people <em>are</em> allowed to park.</p>
			<p>The <strong>Start Date</strong> and <strong>End Date</strong> determine the range for when the rule is active. Leave the <strong>End Date</strong> blank if you do not wish to set a limit on the rule.</p>
			<p>The <strong>Start Time</strong> and <strong>End Time</strong> determine the range of times people with the selected <strong>Passes</strong> will be allowed to park in the selected <strong>Parking Lots</strong>.</p>
			<p>Check all <strong>Days</strong> the rule should apply to.</p>
		</div>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<div id="nested_accordion">
			<form name="delete" id="delete" method="POST" action="">
				<?php
				if(is_array($lot_rules)) {
					// If we have rules
					foreach($lot_rules as $lot_rule) {
						if(!empty($lot_rule["rules"])) {
							// If the lot has rules
							echo "\t<h2><a href=\"#\">".$lot_rule["name"]."</a></h2>";
							echo "\t<div>";
							echo "\t\t<div class=\"ui-widget\">\n";
							echo "\t\t\t$ui_info";
							echo "\t\t\t\t<strong>".$lot_rule["description"]."&nbsp;</strong>\n";
							echo "\t\t\t</div>\n";
							echo "\t\t</div>\n";

							foreach($lot_rule["rules"] as $rule) {
								echo "<p class=\"ui-state-default ui-corner-all ui-helper-clearfix\" style=\"padding:0px;\">";
								echo "<span class=\"ui-icon ui-icon-calendar\" style=\"float:left; margin:1.3em 1em;\"></span>";
								echo date("F d, Y", strtotime($rule["startDate"]))." - ".date("F d, Y", strtotime($rule["endDate"]))."<br/>\n";
								echo date("H:i A", strtotime($rule["startTime"]))." - ".date("H:i A", strtotime($rule["endTime"]))."<br/>\n";
								$days = explode(",", $rule["days"]);
								if(is_array($days))
									foreach($days as $day)
										echo $dotw[$day]." ";
								echo "</p>";
								if(is_array($rule["passTypes"])) {
									foreach($rule["passTypes"] as $pass) {
										echo "<input type=\"checkbox\" id=\"delete_pass_".$pass["id"]."_rule_".$pass["ruleId"]."\" name=\"delete_rules[]\" value=\"".$pass["ruleId"]."\"/>\n";
										echo "<label for=\"delete_pass_".$pass["id"]."_rule_".$pass["ruleId"]."\">".$pass["name"]."</label><br/>\n";
									}
								}
							}
							echo "\t</div>";
						}
					}
				}
				else {
					echo "<h2><a href=\"#\">Chaos</a></h2>\n";
					echo "<div>No rules defined. Expect riots and looting any moment now.</div>\n";
				}
				?>
				<input type="hidden" name="action" value="delete"/>
				<br/>
				<input type="submit" value="Delete Rules"/>
			</form>
			<a href="#" id="delete_help_opener" class="ui-state-default ui-corner-all" style="padding: .4em 1em .4em 20px;text-decoration: none;position: relative;"><span class="ui-icon ui-icon-help" style="margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;"></span>Help</a>
			<div id="delete_help_dialog" title="Delete Rule Help">
				<p>Select a <strong>Parking Lot</strong> to expand it, displaying its rules. They are sorted by <strong>End Date</strong> then <strong>Days</strong>.</p>
				<p>Select <strong>Passes</strong> to be removed from the rule set.</p>
				<p>Collapsing a <strong>Parking Lot</strong> will not clear your selections.</p>
			</div>
		</div>
	</div>
</div>
