<?php
# Create/delete temporary parking rule exceptions for limited time events.

require("./auth.php");
require_once("./_logic.php");

switch($_POST["action"]) {
	case "create":
		$start = $_POST["create_start_date"]." ".$_POST["create_start_hour"].":".$_POST["create_start_minute"].":00";
		$end = $_POST["create_end_date"]." ".$_POST["create_end_hour"].":".$_POST["create_end_minute"].":00";
		$newExceptions = @CreateExceptions($_POST["create_lots"], $_POST["create_passes"], $start, $end, $_POST["allowance"]);

		echo "<div class=\"ui-widget\">\n";
		echo count($newExceptions) > 0 ? $ui_info : $ui_alert;
		echo "\t\tExceptions Created: <strong>".count($newExceptions)."</strong>\n\t</div>\n</div>\n";
		break;
	case "delete":
		$results = DeleteExceptions($_POST["delete_exceptions"]);

		echo "<div class=\"ui-widget\">\n";
		echo $results ? $ui_info : $ui_alert;
		echo "\t\tExceptions Deleted: <strong>".count($_POST["delete_exceptions"])."</strong>\n\t</div>\n</div>\n";
		break;
	default:
		break;
}

$passes = GetPassTypes("name");
$lots = GetLots("name");
?>

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#create_form").validate();

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

	$("#allowance").buttonset();
});
</script>

<div id="tabs">
	<ul>
		<li><a href="#create_tab">Create Exception</a></li>
		<li><a href="#delete_tab">Delete Exceptions</a></li>
	</ul>

	<div id="create_tab">
		<form name="create" id="create_form" method="POST" action="">
			<!-- Start Date -->
			<label for="create_start_datepicker" style="width: 120px;"><span class="ui-icon ui-icon-calendar" style="position:relative; float: right; margin-right: .3em;"></span>Start Date/Time</label>
			<input type="text" name="create_start_datepicker" id="create_start_datepicker" class="required"/>
			<input type="hidden" name="create_start_date" id="create_start_date"/>
			<!-- Start Time -->
			<select name="create_start_hour" id="create_start_hour">
				<optgroup label="Hour">
				<?php
					for($i = 0; $i <= 23; $i++) printf("<option value=\"%02d\">%02d</option>\n", $i, $i);
				?>
				</optgroup>
			</select>
			:
			<select name="create_start_minute" id="create_start_minute">
				<optgroup label="Minute">
				<?php
					for($i = 0; $i <= 59; $i++) printf("<option value=\"%02d\">%02d</option>\n", $i, $i);
				?>
				</optgroup>
			</select>
			<br/>

			<!-- End Date -->
			<label for="create_end_datepicker" style="width: 120px;"><span class="ui-icon ui-icon-calendar" style="position:relative; float: right; margin-right: .3em;"></span>End Date/Time</label>
			<input type="text" name="create_end_datepicker" id="create_end_datepicker" class="required"/>
			<input type="hidden" name="create_end_date" id="create_end_date"/>
			<!-- End Time -->
			<select name="create_end_hour" id="create_end_hour">
				<optgroup label="Hour">
				<?php
					for($i = 0; $i <= 23; $i++) printf("<option value=\"%02d\">%02d</option>\n", $i, $i);
				?>
				</optgroup>
			</select>
			:
			<select name="create_end_minute" id="create_end_minute">
				<optgroup label="Minute">
				<?php
					for($i = 0; $i <= 59; $i++) printf("<option value=\"%02d\">%02d</option>\n", $i, $i);
				?>
				</optgroup>
			</select>
			<br/>

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

			<!-- Allownace -->
			<div id="allowance">
				<input type="radio" name="allowance" id="disallow" checked="checked" value="0"/>
				<label for="disallow">Disallow</label>

				<input type="radio" name="allowance" id="allow" value="1"/>
				<label for="allow">Allow</label>
			</div>

			<input type="hidden" name="action" value="create"/>
			<p><input type="submit" value="Create Exception"/></p>
		</form>
		<?php echo $ui_help_create; ?>
		<div id="create_help_dialog" title="Create Exception Help">
			<p>Exceptions need only be defined when normal rules must be temporarily overwritten.</p>
			<p>The <strong>Start Date/Time</strong> determines when the exception becomes active.</p>
			<p>The <strong>End Date/Time</strong> determines when the exception is no longer needed and normal parking rules will resume.</p>
			<p>Select <strong>Passes</strong> and <strong>Parking Lots</strong> the <strong>Exception</strong> will apply to.</p>
			<p>Hold <em>Shift</em> to make continuous selections.</p>
			<p>Hold <em>Ctrl</em> to make discontinuous selections.</p>
			<p>Select <strong style="color: #00FF00;">Allow</strong> or <strong style="color: #FF0000;">Disallow</strong> to set the type of exception to be applied.</p>
		</div>
	</div>

	<div id="delete_tab">
		<div id="nested_accordion">
			<form name="delete" id="delete" method="POST" action="">
				<?php
					$exceptions = GetExceptionsByLot();

					if(!empty($exceptions)) {
						foreach($exceptions as $exception_group) {
							// If the lot has exceptions construct a header
							echo "\t<h2><a href=\"#\">".$exception_group["name"]."</a></h2>\n";
							echo "\t<div>\n$ui_info\t\t\t<strong>".$exception_group["description"]."</strong>\n</div>\n";

							foreach($exception_group["exceptions"] as $exception) {
								// Print each exception
								echo "<p class=\"ui-state-default ui-corner-all ui-helper-clearfix\" style=\"padding:0px;\">";
								echo "<span class=\"ui-icon ui-icon-calendar\" style=\"position:relative; float:left; margin:1.3em 1em;\"></span>";
								echo date("F d, Y H:i", strtotime($exception["start"]))."<br/>\n";
								echo date("F d, Y H:i", strtotime($exception["end"]))."<br/>\n";
								echo ($exception["allowed"] == 0) ? "<span style=\"color: #FF0000;\">Disallow</span>" : "<span style=\"color: #00FF00;\">Allow</span>";
								echo "</p>";
								foreach($exception["passTypes"] as $pass) {
									echo "<input type=\"checkbox\" id=\"delete_exception_".$pass["exceptionId"]."\" name=\"delete_exceptions[]\" value=\"".$pass["exceptionId"]."\"/>\n";
									echo "<label for=\"delete_exception_".$pass["exceptionId"]."\">".$pass["name"]."</label><br/>\n";
								}
							}
							echo "\t</div>\n";
						}
					}
				?>
				<input type="hidden" name="action" value="delete"/>
				<br/>
				<input type="submit" value="Delete Exceptions"/>
			</form>
			<?php echo $ui_help_delete; ?>
			<div id="delete_help_dialog" title="Delete Exceptions Help">
				<p>Select a <strong>Parking Lot</strong> to expand it, displaying its exceptions. Exceptions are sorted by <strong>End Date</strong>.</p>
				<p>Select <strong>Passes</strong> to be removed from the exception set.</p>
				<p>Collapsing a <strong>Parking Lot</strong> will not clear your selections.</p>
			</div>
		</div>
	</div>
</div>
