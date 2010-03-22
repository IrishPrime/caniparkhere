<?php
# Create/delete recurring parking rules.
require("./auth.php");
require_once("./_logic.php");

$passes = AllPassTypes();
$lots = AllLots();
?>

<script type="text/javascript">
</script>

<fieldset>
	<legend>Create Rule</legend>
	<form name="create" id="create" method="POST" action="">
		<!-- Start Date/Time -->
		<label for="create_start_month">Start Date</label>
		<select name="create_start_month" id="create_start_month">
			<optgroup label="Month">
			</optgroup>
		</select>
		-
		<select name="create_start_date" id="create_start_date">
			<optgroup label="Date">
			</optgroup>
		</select>
		-
		<select name="create_start_year" id="create_start_year">
			<optgroup label="Year">
			</optgroup>
		</select>
		<label for="create_start_hour">Start Time</label>
		<select name="create_start_hour" id="create_start_hour">
			<optgroup label="Hour">
			</optgroup>
		</select>
		:
		<select name="create_start_minute" id="create_start_minute">
			<optgroup label="Minute">
			</optgroup>
		</select>
		<select name="create_start_meridiem" id="create_start_meridiem">
			<option>AM</option>
			<option>PM</option>
		</select>
		<br/>

		<!-- End Date/Time -->
		<label for="create_end_month">End Date</label>
		<select name="create_end_month" id="create_end_month">
			<optgroup label="Month">
			</optgroup>
		</select>
		-
		<select name="create_end_date" id="create_end_date">
			<optgroup label="Date">
			</optgroup>
		</select>
		-
		<select name="create_end_year" id="create_end_year">
			<optgroup label="Year">
			</optgroup>
		</select>
		<label for="create_end_hour">End Time</label>
		<select name="create_end_hour" id="create_end_hour">
			<optgroup label="Hour">
			</optgroup>
		</select>
		:
		<select name="create_end_minute" id="create_end_minute">
			<optgroup label="Minute">
			</optgroup>
		</select>
		<select name="create_end_meridiem" id="create_end_meridiem">
			<option>AM</option>
			<option>PM</option>
		</select>
		<br/>

		<!-- Days -->
		<label for="create_days">Days</label>
		<div name="create_days" id="create_days">
			<input type="checkbox" name="create_sunday" id="create_sunday"/>
			<label for="create_sunday">Sunday</label>
			<input type="checkbox" name="create_monday" id="create_monday"/>
			<label for="create_monday">Monday</label>
			<input type="checkbox" name="create_tuesday" id="create_tuesday"/>
			<label for="create_tuesday">Tuesday</label>
			<input type="checkbox" name="create_wednesday" id="create_wednesday"/>
			<label for="create_wednesday">Wednesday</label>
			<input type="checkbox" name="create_thursday" id="create_thursday"/>
			<label for="create_thursday">Thursday</label>
			<input type="checkbox" name="create_friday" id="create_friday"/>
			<label for="create_friday">Friday</label>
			<input type="checkbox" name="create_saturday" id="create_saturday"/>
			<label for="create_saturday">Saturday</label>
		</div>
		<input type="button" value="Weekdays" onclick=""/>
		<input type="button" value="Weekends" onclick=""/>
		<input type="button" value="All" onclick=""/>
		<br/>

		<!-- Permit Types -->
		<label for="create_passes">Permits</label>
		<select name="create_passes" id="create_passes" multiple="multiple">
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
		<label for="create_lots">Parking Lots</label>
		<select name="create_lots" id="create_lots" multiple="multiple">
			<optgroup label="Parking Lots">
			<?php
				if(is_array($lots))
					foreach($lots as $lot)
						echo "<option value=\"".$lot["id"]."\">".$lot["name"]."</option>\n";
			?>
			</optgroup>
		</select>
		<br/>
		<input type="submit" value="Create Rule"/>
	</form>
</fieldset>

<fieldset>
	<legend>Delete Rules</legend>
	<form name="delete" id="delete" method="POST" action="">
		<input type="submit" value="Delete Rules"/>
	</form>
</fieldset>
