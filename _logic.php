<?php

require_once("./_settings.php");
require_once("./_data.php");

$data = new data();

// my own comma delimited list helper class
class cdl {
	private $cdl = null;
	private $delimiter = ",";
	
	//function __construct() { }
	
	function __construct($cdl) {
		$this->cdl = array();
		if ($cdl != null) $this->cdl = explode($this->delimiter, $cdl);
	}
	public function add($item) {
		$this->cdl[] = (int)$item;
	}
	public function addSeq($startNum, $endNum) {
		for ($i = (int)$startNum; $i <= (int)$endNum; $i++) {
			$this->cdl[] = (int)$i;
		}
	}
	public function addCircularSeq($startNum, $endNum, $floor, $ceiling) {
		$i = $startNum;
		while (true) {
			$this->cdl[] = (int)$i;
			$i++;
			// if you move above the ceiling, drop to the floor
			if ($i > $ceiling) $i = $floor;
			// if you reach the end number, stop
			if ($i == $endNum) {
				$this->cdl[] = (int)$i;
				break;
			}
		}
	}
	public function cdl() {
		return implode($this->delimiter, $this->cdl);
	}
	public function hasValues() {
		$count = count($this->cdl);
		if ($count > 0) return true;
		else return false;
	}
	public function contains($string) {
		if (in_array($string, $this->cdl)) return true;
		else return false;
	}
	public function isStartNum($num) {
		$key = array_search($num, $this->cdl);
		$minIndex = 0;
		if ($key !== false && $key == $minIndex) return true;
		else return false;
	}
	public function isEndNum($num) {
		$key = array_search($num, $this->cdl);
		// get count of objects in cdl,
		// subtract one to get max index of array
		$maxIndex = (count($this->cdl) - 1);
		if ($key !== false && $key == maxIndex) return true;
		else return false;
	}
}

/* function AllLots()
	returns all lots in database
*/
function AllLots() {
	global $data;
	$lots = $data->get_lots(null);
	return $lots;
}

/* function AllPassTypes()
	returns all pass types in database
*/
function AllPassTypes() {
	global $data;
	$passTypes = $data->get_passTypes(null);
	return $passTypes;
}

/* function AllRulesByLot($id)
	$id = single id of requested lot
	returns all rules for a specific lot
*/
function AllRulesByLot($id) {
	global $data;
	$rules = $data->get_rulesForLots($id);
	return $rules;
}

/* function AllRulesByPassType($id)
	$id = single id of requested pass type
	returns all rules for a specific pass type
*/
function AllRulesByPassType($id) {
	global $data;
	$rules = $data->get_rulesForPassTypes($id);
	return $rules;
}

/* function CanIParkHereNow($lotId, $passId)
	$lotId = single id of requested lot
	$passId = single id of requested pass type
	returns results for that lot

	Returns if you can or can not park in the requested
	lot(s) based on current time of day and the passtype sent.
*/		
function CanIParkHereNow($lotId, $passTypeId) {
	global $data;
	// grab all rules for this lot
	$rules = $data->get_rulesForLots($lotIds);
	// set requested timestamp
	$requestedTime = new DateTime("now");
	
	// set default results
	$results = array(
		"ciph" => false);
	
	// search for rules that apply to this passType
	if ($rules != null) {
		foreach ($rules as $rule) {
			if ($rule("passTypeId") == $passTypeId) {
				if ($this->doesRuleApply($rule, $requestedTime)) {
					$results["ciph"] = true;
					break;
				}
			}
		}
	}
	
	return $results;
}
/* function WhatPassTypesCanParkHere($lotId)
	$lotId = single id of requested lot
	returns passType data, null on no passTypes

	Returns an array of passtypes that can currently
	park at the requested lot.
*/
function WhatPassTypesCanParkHere($lotId) {

	global $data;
	// grab all rules for this lot
	$rules = $data->get_rulesForLots($lotId);
	// set requested timestamp
	$requestedTime = new DateTime("now");
	
	$noIds = true;
	$ids = new cdl(null);
	
	// search for rules that apply to this passType
	if ($rules != null) {
		foreach ($rules as $rule) {
			if (doesRuleApply($rule, $requestedTime))
				$ids->add($rule["passTypeId"]);
		}
	}
	
	// if there were ids, grab data
	if ($ids->hasValues()) {
		//echo "Rules that apply for this lot: " . $ids . "<br>\n";
		$passTypes = $data->get_passTypes($ids->cdl());
		return $passTypes;
	}
	else {
		return null;
	}
}

function doesRuleApply($rule, $parkTimestamp) {

	$inDateRange = false;
	$inTimeRange = false;
	$inDayOfWeek = false;

	// parse times and dates
	$park = getdate($parkTimestamp->format('U')); //->format('Y-m-d G:i')
	$startDate = date_parse((string)$rule["startDate"]);
	$startTime = date_parse((string)$rule["startTime"]);
	$endDate = date_parse((string)$rule["endDate"]);
	$endTime = date_parse((string)$rule["endTime"]);
	
	// create cdls
	$years = new cdl(null);
	$months = new cdl(null);
	$startMonthDays = new cdl(null);
	$endMonthDays = new cdl(null);
	$hours = new cdl(null);
	$startHourMinutes = new cdl(null);
	$endHourMinutes = new cdl(null);
	$weekDays = new cdl($rule["days"]);
	
	// populate cdls
	$years->addSeq($startDate["year"], $endDate["year"]);
	//echo "Years " . $years->cdl() . "<br>";
	
	$months->addCircularSeq($startDate["month"], $endDate["month"], 1, 12);
	//echo "Months " . $months->cdl() . "<br>";
	
	if ($startDate["month"] == $endDate["month"]) {
		$startMonthDays->addSeq($startDate["day"], $endDate["day"]);
		//echo "Start Month Days " . $startMonthDays->cdl() . "<br>";
		$endMonthDays->addSeq($startDate["day"], $endDate["day"]);
		//echo "End Month Days " . $endMonthDays->cdl() . "<br>";
	}
	else {
		$startMonthDays->addSeq($startDate["day"], 31);
		//echo "Start Month Days " . $startMonthDays->cdl() . "<br>";
		$endMonthDays->addSeq(1, $endDate["day"]);
		//echo "End Month Days " . $endMonthDays->cdl() . "<br>";
	}
	
	$hours->addCircularSeq($startTime["hour"], $endTime["hour"], 0, 24);
	//echo "Hours " . $hours->cdl() . "<br>";
	if ($startTime["minute"] == $endTime["minute"]) {
		$startHourMinutes->addSeq($startTime["minute"], $endTime["minute"]);
		//echo "Start Hour Minutes " . $startHourMinutes->cdl() . "<br>";
		$endHourMinutes->addSeq($startTime["minute"], $endTime["minute"]);
		//echo "End Hour Minutes " . $endHourMinutes->cdl() . "<br>";
	}
	else {
		$startHourMinutes->addSeq($startTime["minute"], 60);
		//echo "Start Hour Minutes " . $startHourMinutes->cdl() . "<br>";
		$endHourMinutes->addSeq(0, $endTime["minute"]);
		//echo "End Hour Minutes " . $endHourMinutes->cdl() . "<br>";
	}
	
	// print data
	//echo "Years " . $years->cdl() . "<br>";
	//echo "Months " . $months->cdl() . "<br>";
	//echo "Start Month Days " . $startMonthDays->cdl() . "<br>";
	//echo "End Month Days " . $endMonthDays->cdl() . "<br>";
	//echo "Hours " . $hours->cdl() . "<br>";
	//echo "Start Hour Minutes " . $startHourMinutes->cdl() . "<br>";
	//echo "End Hour Minutes " . $endHourMinutes->cdl() . "<br>";
	
	//echo "Start rule logic.<br>";
	
	// rule logic
	//echo "Is the year " . $park["year"] . " in the range (" . $years->cdl() . ")? ";
	if ($years->contains($park["year"])) {
		//echo "Yes<br>Is the month " . $park["mon"] . " in the range (" . $months->cdl() . ")? ";
		if ($months->contains($park["mon"])) {
			if ($years->isStartNum($park["year"]) && $months->isStartNum($park["mon"])) {
				//echo "Yes<br>In start month/year; is the day " . $park["mday"] . " in the range (" . $startMonthDays->cdl() . ")? ";
				if ($startMonthDays->contains($park["mday"])) {
					//echo "Yes<br><b>Date in range.</b>";
					$inDateRange = true;
				}
				//else
					//echo "<b>No.</b>";
			}
			elseif ($years->isEndNum($park["year"]) && $months->isEndNum($park["mon"])){
				//echo "Yes<br>In end month/year; is the day " . $park["mday"] . " in the range (" . $endMonthDays->cdl() . ")? ";
				if ($endMonthDays->contains($park["mday"])) {
					//echo "Yes<br><b>Date in range.</b>";
					$inDateRange = true;
				}
				//else
					//echo "<b>No.</b>";
			}
			else {
				//echo "Yes<br><b>Date in range.</b>";
				$inDateRange = true;
			}
		}
		//else
			//echo "<b>No.</b>";
	}
	//else
		//echo "<b>No.</b>";
	//echo "<br>";
	
	//echo "Is the hour " . $park["hours"] . " in the range (" . $hours->cdl() . ")?";
	if ($hours->contains($park["hours"])) {
		if ($hours->isStartNum($park["hours"])) {
			//echo "Yes<br>In first defined hour; is the minute " . $park["minutes"] . " in the range (" . $startHourMinutes->cdl() . ")? ";
			if ($startHourMinutes->contains($park["minutes"])) {	
				//echo "Yes<br><b>Time in range.</b>";
				$inTimeRange = true;
			}
			//else
				//echo "<b>No.</b>";
		}
		elseif ($hours->isEndNum($park["hours"])) {
			//echo "Yes<br>In last defined hour; is the minute " . $park["minutes"] . " in the range (" . $endHourMinutes->cdl() . ")? ";
			if ($endHourMinutes->contains($park["minutes"])) {
				//echo "Yes<br><b>Time in range.</b>";
				$inTimeRange = true;
			}
			//else
				//echo "<b>No.</b>";
		}
		else {
			//echo "Yes<br><b>Time in range.</b>";
			$inTimeRange = true;
		}
	}
	//else
		//echo "<b>No.</b>";
	//echo "<br>";
	
	//echo "Is the day of the week " . $park["wday"] . " in the range (" . $weekDays->cdl() . ")? ";
	if ($weekDays->contains($park["wday"])) {
		//echo "Yes<br><b>Day of week in range.</b>";
		$inDayOfWeek = true;
	}
	//else
		//echo "<b>No.</b>";
	
	//echo "<br>";
	
	if ($inDateRange && $inTimeRange && $inDayOfWeek) return true;
	else return false;
}

?>