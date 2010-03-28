<?php

require_once("./_settings.php");

class cdl {
	private $cdl = null;
	private $delimiter = ",";
	
	function __construct($cdl, $delimiter) {
		$this->cdl = array();
		if ($delimiter != null) $this->delimiter = $delimiter;
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
	public function cdl($overrideDelimiter) {
		if ($overrideDelimiter != null) return implode($overrideDelimiter, $this->cdl);
		else return implode($this->delimiter, $this->cdl);
	}
	public function cdlArray() {
		return $this->cdl;
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
	
class data {

	private $conn;
	private $db_name = "ciph";
	private $sql;

	function __construct() {
		require("./_settings.php");
		// constructor (connects to mysql server and changes to ciph db)
		
		// create connection - mysql_connect("banshee:3306", "mysql", "cpsc123")
		$conn = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Can't connect to MySQL server: " . mysql_error());
		
		// open database
		mysql_select_db($this->db_name, $conn) or die("Can't open database: " . mysql_error());
	}
	
	private function create_lots($result) {
		$lots = null;
		if (mysql_num_rows($result) != 0) {
			$lots = array();
			while ($row = mysql_fetch_assoc($result)) {
				$coords = new cdl($row["lotCoords"], ";"); // parse coords from string
				$currentPasses = $this->whatPassTypesCanParkHere($row["id"]);
				$rules = $this->get_rulesForLots($row["id"]);
				$scheme = $this->get_scheme($row["lotScheme"]);
			
				$lots[$row["id"]] = array(
						"id" => $row["id"],
						"name" => $row["lotName"],
						"description" => $row["lotDescription"],
						"currentPassTypes" => $currentPasses, // array
						"rules" => $rules, // array
						"coords" => $coords->cdlArray(), // array
						"picture" => $row["lotPicture"],
						"scheme" => $scheme); // array
			}
		}
		return $lots;
	}
	private function create_passTypes($result) {
		$passTypes = null;
		if (mysql_num_rows($result) != 0) {
			$passTypes = array();
			while ($row = mysql_fetch_assoc($result)) {
				$passTypes[$row["id"]] = array (
					"id" => $row["id"],
					"name" => $row["passName"],
					"rules" => $this->get_rulesForPassTypes($row["id"])); // array;
			}
		}
		return $passTypes;
	}
	private function create_rules($result) {
		$rules = null;
		if (mysql_num_rows($result) != 0) {
			$rules = array();
			while ($row = mysql_fetch_assoc($result)) {
				$rules[$row["id"]] = array(
					"id" => $row["id"],
					"lotId" => $row["lot"],
					"passTypeId" => $row["passType"],
					"startDate" => $row["startDate"],
					"endDate" => $row["endDate"],
					"startTime" => $row["startTime"],
					"endTime" => $row["endTime"],
					"days" => $row["days"]);
			}
		}
		return $rules;
	}
	private function create_settings($result) {
		$settings = null;
		if (mysql_num_rows($result) != 0) {
			$settings = array();
			while ($row = mysql_fetch_assoc($result)) {
				$settings[$row["settingName"]] = $row["settingValue"];
			}
		}
		return $settings;
	}
	private function create_scheme($result) {
		$scheme = null;
		if (mysql_num_rows($result) != 0) {
				$row = mysql_fetch_assoc($result);
				$scheme = array(
					"id" => $row["schemeId"],
					"name" => $row["schemeName"],
					"lineColor" => $row["schemeLineColor"],
					"lineWidth" => $row["schemeLineWidth"],
					"lineOpacity" => $row["schemeLineOpacity"],
					"fillColor" => $row["schemeFillColor"],
					"fillOpacity" => $row["schemeFillOpacity"]);
		}
		return $scheme;
	}
	
	public function get_lots($ids) {
		$sql = "select * from lots";
		if ($ids != null) $sql .= " where id in (" . $ids . ")";
		$sql .= " order by id asc";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_lots($ids)");
		else return $this->create_lots($result);
	}
	public function get_passTypes($ids) {
		$sql = "select * from passTypes";
		if ($ids != null) $sql .= " where id in (" . $ids . ")";
		$sql .= " order by passName asc";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_passTypes($ids)");
		else return $this->create_passTypes($result);
	}
	public function get_rulesForLots($ids) {
		$sql = "select * from rules";
		if ($ids != null) $sql .= " where lot in (" . $ids . ")";
		$sql .= " order by startDate, endDate";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_rulesForLots($ids)");
		else return $this->create_rules($result);
	}
	public function get_rulesForPassTypes($ids) {
		$sql = "select * from rules";
		if ($ids != null) $sql .= " where passType in (" . $ids . ")";
		$sql .= " order by startDate, endDate";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_rulesForPassTypes($ids)");
		else return $this->create_rules($result);
	}
	public function get_settingsById($ids) {
		$sql = "select * from settings";
		if ($ids != null) $sql .= " where settingId in (" . $ids . ")";
		$sql .= " order by settingId";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_settingsById($ids)");
		else return $this->create_settings($result);
	}
	public function get_settingsByUser($ids) {
		$sql = "select * from settings";
		if ($ids != null) $sql .= " where userId in (" . $ids . ")";
		$sql .= " order by settingId";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_settingsByUser($ids)");
		else return $this->create_settings($result);
	}
	public function get_scheme($id) {
		$sql = "select * from schemes";
		if ($id != null) $sql .= " where schemeId in (" . $id . ")";
		$sql .= " order by schemeId";
		$result = mysql_query($sql);
		if (!result) die ("MySQL error: get_schemes($ids)");
		else return $this->create_scheme($result);
	}
	
	private function addSingleQuotes($string) {
		return "'" . $string . "'";
	}
	public function insert_lot($name, $desc, $coords) {
		$sql = "INSERT INTO lots "
			. "(lotCoords, lotName, lotDescription) "
			. "values (" 
			. $this->addSingleQuotes($coords) . ", "
			. $this->addSingleQuotes($name) . ", "
			. $this->addSingleQuotes($desc) . ")";
		$result = mysql_query($sql);
		
		if ($result) return mysql_insert_id();
		else return null;
	}
	public function insert_passType($name) {
		$sql = "INSERT INTO passTypes "
			.	"(passName) "
			. "values ("
			. $this->addSingleQuotes($name) . ")";
		$result = mysql_query($sql);
		
		if ($result) return mysql_insert_id();
		else return null;
	}
	public function insert_rule($lotId, $passTypeId, $startDate, $endDate, $startTime, $endTime, $days) {
		$sql = "INSERT INTO rules "
			.	"(lot, passType, startDate, endDate, startTime, endTime, days) "
			. "values ("
			. $lotId . ", "
			. $passTypeId . ", "
			. $this->addSingleQuotes($startDate) . ", "
			. $this->addSingleQuotes($endDate) . ", "
			. $this->addSingleQuotes($startTime) . ", "
			. $this->addSingleQuotes($endTime) . ", "
			. $this->addSingleQuotes($days) . ")";

		$result = mysql_query($sql);
		
		if ($result) return mysql_insert_id();
		else return null;
	}
	
	public function delete_lot($id) {
		$sql = "DELETE FROM lots WHERE id=($id)";
		return mysql_query($sql);
	}
	public function delete_passType($id) {
		$sql = "DELETE FROM passTypes WHERE id=($id)";
		return mysql_query($sql);
	}
	public function delete_rule($id) {
		$sql = "DELETE FROM rules WHERE id=($id)";
		return mysql_query($sql);
	}
	
	public function whatPassTypesCanParkHere($lotId) {
	/* function WhatPassTypesCanParkHere($lotId)
		$lotId = single id of requested lot
		returns passType data, null on no passTypes

		Returns an array of passtypes that can currently
		park at the requested lot.
	*/
		// grab all rules for this lot
		$rules = $this->get_rulesForLots($lotId);
		// set requested timestamp
		$requestedTime = new DateTime("now");
		
		$noIds = true;
		$ids = new cdl(null, null);
		
		// search for rules that apply to this passType
		if ($rules != null) {
			foreach ($rules as $rule) {
				if ($this->doesRuleApply($rule, $requestedTime))
					$ids->add($rule["passTypeId"]);
			}
		}
		
		// if there were ids, grab data
		if ($ids->hasValues()) {
			$passTypes = $this->get_passTypes($ids->cdl(null));
			return $passTypes;
		}
		else {
			return null;
		}
	}
	public function doesRuleApply($rule, $parkTimestamp) {

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
		$years = new cdl(null, null);
		$months = new cdl(null, null);
		$startMonthDays = new cdl(null, null);
		$endMonthDays = new cdl(null, null);
		$hours = new cdl(null, null);
		$startHourMinutes = new cdl(null, null);
		$endHourMinutes = new cdl(null, null);
		$weekDays = new cdl($rule["days"], null);
		
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
	
	public function close_me() {
	// disconnect mysql connection
		mysql_close();
	}
	
	function __destruct() {
		$this->close_me();
	}

}

$data = new data();

function GetLots() {
/* function AllLots()
	returns all lots in database
*/
	global $data;
	$lots = $data->get_lots(null);
	return $lots;
}
function GetPassTypes() {
/* function AllPassTypes()
	returns all pass types in database
*/
	global $data;
	$passTypes = $data->get_passTypes(null);
	return $passTypes;
}
function GetSettings($id) {
	global $data;
	$settings = $data->get_settingsById($id);
	return $settings;
}
function GetSettingsForUser($id) {
	global $data;
	$settings = $data->get_settingsByUser($id);
	return $settings;
}

function CreateLot($name, $desc, $coords) {
	global $data;
	return $data->insert_lot($name, $desc, $coords);
}
function CreatePassType($name) {
	global $data;
	return $data->insert_passType($name);
}
function CreateRule($lotId, $passTypeId, $startDate, $endDate, $startTime, $endTime, $days) {
	global $data;
	$ruleIds = array(0);
	for ($i = 0; $i < count($lotId); $i++) {
		for ($j = 0; $j < count($passTypeId); $j++) {
			$ruleIds[] = $data->insert_rule($lotId[$i], $passTypeId[$j], $startDate, $endDate, $startTime, $endTime, $days);
		}
	}
	if (count($ruleIds > 0)) return $ruleIds;
	else return null; // no ids to return
}

function DeleteLot($id) {
	global $data;
	return $data->delete_lot($id);
}
function DeletePassType($id) {
	global $data;
	return $data->delete_passType($id);
}
function DeleteRule($id) {
	global $data;
	return $data->delete_rule($id);
}

function CanIParkHereNow($lotId, $passTypeId) {
/*  function CanIParkHereNow($lotId, $passId)
	$lotId = single id of requested lot
	$passId = single id of requested pass type
	returns results for that lot

	Returns if you can or can not park in the requested
	lot(s) based on current time of day and the passtype sent.		

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
				if ($data->doesRuleApply($rule, $requestedTime)) {
					$results["ciph"] = true;
					break;
				}
			}
		}
	}
	
	return $results;
*/
}

?>
