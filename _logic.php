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
	public function split($axis) {
		// split array items up by $axis
		// left side is this cdl
		// right side is returned
		$left = array();
		$right = array();
		foreach ($this->cdl as $i) {
			$split = explode($axis, $i);
			$left[] = $split[0];
			$right[] = $split[1];
		}
		$this->cdl = $left;

		return $right;
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
				$id = $row["id"];
				$name = $row["name"];
				$desc = $row["descrip"];
				$pic = $row["pic"];
				$scheme = $row["scheme"];

				// change data
				$coords = new cdl($row["coords"], ";"); // parse coords from string
				$scheme = $this->get_scheme($scheme);
				$currentPasses = $this->whatPassTypesCanParkHere($id);
				$middle = $this->findLatLngAverage($row["coords"]);

				$lots[$id] = array(
					"id" => $id,
					"name" => $name,
					"description" => $desc,
					"picture" => $pic,
					"middle" => $middle,
					"coords" => $coords->cdlArray(), // array
					"scheme" => $scheme, // array
					"currentPassTypes" => $currentPasses); // array
			}
		}
		//debug($lots);
		return $lots;
	}
	private function findLatLngAverage($coords) {
		// write middle point for each polygon
		$both = new cdl($coords, ";");
		$lngs = $both->split(",");
		$lats = $both->cdlArray();
		$latAvg = 0;
		$lngAvg = 0;

		foreach ($lats as $lat)
			$latAvg += floatval($lat);
		foreach ($lngs as $lng)
			$lngAvg += floatval($lng);

		$latAvg /= count($lats);
		$lngAvg /= count($lngs);
		//echo "Lat Avg = $latAvg<br>";
		//echo "Lng Avg = $lngAvg<br>";
		return "$latAvg, $lngAvg";
	}
	private function create_passTypes($result) {
		$passTypes = null;
		if (mysql_num_rows($result) != 0) {
			$passTypes = array();
			while ($row = mysql_fetch_assoc($result)) {
				$passTypes[$row["id"]] = array (
					"id" => $row["id"],
					"name" => $row["name"]);
			}
		}
		return $passTypes;
	}
	private function create_rulesByLots($lots, $times, $result) {
		/*$rules = null;

		$lots = null;
		$rules = null;
		$passTypes = null;

		if (mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_assoc($lots)) {
				if ($lots[$row["lotId"]] == null)
					$lots[$row["lotId"] = array(
					"name" => $row["lotName"],
					"description" => $row["descrip"],
					"rules" => array());

				if ($lots[$row["lotId"]]["rules"][datekey] == null) {
				}
					"startDate" => $row["startDate"],
					"endDate" => $row["endDate"],
					"timeRange" = array()

				)

				if ($lots[$row["lotId"]]["rules"][datekey]["timeRange"][timeKey] == null) {
				}
					"startTime" => $row["startTime"],
					"endTime" => $row["endTime"],
					"days" = array()

				)

				if ($lots[$row["lotId"]]["rules"][datekey]["timeRange"][timeKey]["days"][dayKey] == null) {
					"days" => $row["days"],
					"passTypes" = array()
				}

				if ($lots[$row["lotId"]]["rules"][datekey]["timeRange"][timeKey]["days"][dayKey]["passTypes"][$row["passTypeId"]] == null) {
					"ruleId" => $row["ruleId"],
					"id" => $row["passTypeId"],
					"name" => $row["name"]);
			}
		} */


		$rules = null;
		if (mysql_num_rows($lots) != 0) {
			$rules = array();
			// create lot array to hold rules
			while ($row = mysql_fetch_assoc($lots)) {
				$rules[$row["id"]] = array(
					"name" => $row["name"],
					"description" => $row["descrip"],
					"rules" => array());
			}
			// create unique rule combination entries
			if (mysql_num_rows($times) != 0) {
				while ($row = mysql_fetch_assoc($times)) {
					$lot = $row["lot"];
					$key = $this->key($row);
					$rules[$lot]["rules"][$key] = array(
						"startDate" => $row["startDate"],
						"startTime" => $row["startTime"],
						"endDate" => $row["endDate"],
						"endTime" => $row["endTime"],
						"days" => $row["days"],
						"passTypes" => array());
				}

				// create modified passType object for each rule
				if (mysql_num_rows($result) != 0) {
					while ($row = mysql_fetch_assoc($result)) {
						$lot = $row["lot"];
						$key = $this->key($row);
						$passTypeId = $row["passTypeId"];
						$rules[$lot]["rules"][$key]["passTypes"][$passTypeId] = array(
							"ruleId" => $row["ruleId"],
							"id" => $row["passTypeId"],
							"name" => $row["name"]);
					}
				}
			}
		}
		//debug($rules);
		return $rules;
	}
	private function key($row) {
		$startDate = $row["startDate"];
		$startTime = $row["startTime"];
		$endDate = $row["endDate"];
		$endTime = $row["endTime"];
		$days = $row["days"];
		return ("[" . $startDate . "][" . $startTime . "][". $endDate . "][" . $endTime . "][" . $days . "]");
	}
	private function create_exceptions($result) {
		$exceptions = null;
		if (mysql_num_rows($result) != 0) {
			$exceptions = array();
			while ($row = mysql_fetch_assoc($result)) {
				$exceptions[$row["id"]] = array(
					"id" => $row["id"],
					"lot" => $row["lot"],
					"passType" => $row["passType"],
					"start" => $row["start"],
					"end" => $row["end"]);
			}
		}
		return $exceptions;
	}
	private function create_settings($result) {
		$settings = null;
		if (mysql_num_rows($result) != 0) {
			$settings = array();
			while ($row = mysql_fetch_assoc($result)) {
				$settings[$row["name"]] = $row["value"];
			}
		}
		return $settings;
	}
	private function create_scheme($result) {
		$scheme = null;
		if (mysql_num_rows($result) != 0) {
			$row = mysql_fetch_assoc($result);
			$scheme = array(
				"id" => $row["id"],
				"name" => $row["name"],
				"lineColor" => $row["lineColor"],
				"lineWidth" => $row["lineWidth"],
				"lineOpacity" => $row["lineOpacity"],
				"fillColor" => $row["fillColor"],
				"fillOpacity" => $row["fillOpacity"]);
		}
		return $scheme;
	}

	public function get_lots($ids, $sortColumn) {
		$sql = "SELECT * FROM lots";
		if ($ids != null) $sql .= " WHERE id in (" . $ids . ")";
		if ($sortColumn == null) $sortColumn = "name";
		$sql .= " ORDER BY " . $sortColumn . " ASC";

		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_lots($ids)");
		else return $this->create_lots($result);
	}
	public function get_passTypes($ids, $sortColumn) {
		$sql = "select * from passTypes";
		if ($ids != null) $sql .= " where id in (" . $ids . ")";
		if ($sortColumn == null) $sortColumn = "name"; // was id
		$sql .= " order by " . $sortColumn . " asc";

		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_passTypes($ids)");
		else return $this->create_passTypes($result);
	}
	private function get_passTypesByTime($startDate, $endDate, $startTime, $endTime, $days) {
		$sql = "select passType from rules"
			. " inner join passTypes on passTypes.id = rules.passType where"
			. " startDate = " . $this->addSingleQuotes($startDate)
			. " and endDate = " . $this->addSingleQuotes($endDate)
			. " and startTime = " . $this->addSingleQuotes($startTime)
			. " and endTime = " . $this->addSingleQuotes($endTime)
			. " and days = " . $this->addSingleQuotes($days)
			. " order by name asc";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_passTypesByTime($ids)");
		else {
			$ruleIds = array();
			while ($row = mysql_fetch_assoc($result))
				$ruleIds[] = $row["passType"];
			return $this->get_passTypes(implode(',', $ruleIds), "name");
		}
	}
	public function get_rulesByLot($id) {
		/*$sql = "select"
			. " lots.id as lotId, lots.name as lotName, lots.descrip,"
			. " rules.id as ruleId, startDate, startTime, endDate, endTime, days,"
			. " passTypes.id as passTypeId, passTypes.name as passTypeName"
			. " from rules"
			. " inner join lots on lots.id = rules.lot"
			. " inner join passTypes on passTypes.id = rules.passType"
			. " order by lots.name asc, endDate desc, endTime desc, days asc, passTypes.name asc";
		//echo $sql . "<br>";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_rulesByLots($ids)");
		else return $this->create_rulesByLots($result);*/


		$sql = "select id, name, descrip from lots";
		if ($id != null) $sql .= " where id in (" . $id . ")";
		$sql .= " order by name asc";
		$lots = mysql_query($sql);
		//echo $sql . "<br>";

		$sql = "select lot, startDate, startTime, endDate, endTime, days from rules"
			. " group by lot, startDate, startTime, endDate, endTime, days";
		if ($id != null) $sql .= " having lot in (" . $id . ")";
		$sql .= " order by endDate desc, endTime desc, days asc";
		$times = mysql_query($sql);
		//echo $sql . "<br>";

		$sql = "select lot, startDate, startTime, endDate, endTime, days,"
			. " rules.id as ruleId, passTypes.id as passTypeId, name"
			. " from rules inner join passTypes on passTypes.id = rules.passType";
		if ($id != null) $sql .= " where lot in (" . $id . ")";
		$sql .= " order by name asc";
		$result = mysql_query($sql);
		//echo $sql . "<br>";

		if (!lots || !times || !$result) die("MySQL error: get_rulesByLots($ids)");
		else return $this->create_rulesByLots($lots, $times, $result); 
	}
	public function get_exceptionsByLots($ids) {
		$sql = "SELECT * FROM exceptions";
		if ($ids != null) $sql .= " WHERE lot IN (" . $ids . ")";
		$sql .= " ORDER BY end DESC";

		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_exceptionsByLots($ids)");
		else return $this->create_exceptions($result);
	}
	public function get_settingsByUser($ids) {
		$sql = "select * from settings";
		if ($ids != null) $sql .= " where user in (" . $ids . ")";
		$sql .= " order by id";
		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_settingsByUser($ids)");
		else return $this->create_settings($result);
	}
	public function get_scheme($id) {
		$sql = "SELECT * FROM schemes";
		if ($id != null) $sql .= " where id in (" . $id . ")";
		$sql .= " ORDER BY id";
		$result = mysql_query($sql);
		if (!$result) die ("MySQL error: get_schemes($id)");
		else return $this->create_scheme($result);
	}

	private function addSingleQuotes($string) {
		return "'" . $string . "'";
	}
	public function insert_lot($name, $desc, $pic, $coords, $scheme) {
		$sql = "insert into lots "
			. "(name, desc, pic, coords, scheme) "
			. "values (" 
			. $this->addSingleQuotes($name) . ", "
			. $this->addSingleQuotes($desc) . ", "
			. $this->addSingleQuotes($pic) . ", "
			. $this->addSingleQuotes($coords) . ", "
			. $scheme . ")";
		$result = mysql_query($sql);

		if ($result) return mysql_insert_id();
		else return false;
	}
	public function insert_passType($name) {
		$sql = "INSERT INTO passTypes "
			.	"(name) "
			. "VALUES ("
			. $this->addSingleQuotes($name) . ")";
		$result = mysql_query($sql);

		if ($result) return mysql_insert_id();
		else return false;
	}
	public function insert_rule($lot, $passType, $startDate, $endDate, $startTime, $endTime, $days) {
		$sql = "insert into rules "
			.	"(lot, passType, startDate, endDate, startTime, endTime, days) "
			. "values ("
			. $lot . ", "
			. $passType . ", "
			. $this->addSingleQuotes($startDate) . ", "
			. $this->addSingleQuotes($endDate) . ", "
			. $this->addSingleQuotes($startTime) . ", "
			. $this->addSingleQuotes($endTime) . ", "
			. $this->addSingleQuotes($days) . ")";

		$result = mysql_query($sql);

		if ($result) return mysql_insert_id();
		else return false;
	}
	public function insert_exception($lot, $passType, $start, $end, $allow) {
		$sql = "INSERT INTO exceptions (lot, passType, start, end, allowed) values ("
			. $lot . ", "
			. $passType . ", "
			. $this->addSingleQuotes($start) . ", "
			. $this->addSingleQuotes($end) . ", "
			. $allow . ")";

		$result = mysql_query($sql);

		if ($result) return mysql_insert_id();
		else return false;
	}

	public function update_passType($id, $name) {
		$sql = "UPDATE passTypes SET name = "
			. $this->addSingleQuotes($name)
			. " WHERE id = $id";
		return mysql_query($sql);
	}

	public function delete_lot($ids) {
		$sql = "delete from lots where id in (" . $ids . ")";
		return mysql_query($sql);
	}
	public function delete_passType($ids) {
		$sql = "DELETE FROM passTypes WHERE id IN (" . $ids . ")";
		return mysql_query($sql);
	}
	public function delete_rule($ids) {
		$sql = "delete from rules where id in (" . $ids . ")";
		//echo $sql + "<br>";
		return mysql_query($sql);
	}
	public function delete_exception($ids) {
		$sql = "delete from exceptions where id in (" . $ids . ")";
		return mysql_query($sql);
	}

	private function whatPassTypesCanParkHere($id) {
		// returns array of passTypes, null = no pass types

		// set requested timestamp
		$requestedTime = new DateTime("now");

		// get rules
		$rules = $this->get_rulesByLot($id);
		$rules = $rules[$id]["rules"];

		$passTypes = null;

		// search for rules that apply to this passType
		if ($rules != null) {
			foreach ($rules as $rule) {
				if ($this->doesRuleApply($rule, $requestedTime)) {
					if ($passTypes == null) $passTypes = array();
					foreach ($rule["passTypes"] as $pass)
						$passTypes[$pass["id"]] = $pass;
				}
			}
		}

		return $passTypes;
	}
	private function doesRuleApply($rule, $parkTimestamp) {

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

// Data load methods.
//
// Lots and PassTypes
//  Can be sorted by any field,
//  but default to "name" if $sortColumn is null.
// Settings
//  $id = user id, 0 = global, null = all
// Schemes
//	$ids = single scheme id, null = all schemes
function GetLots($sortColumn) {
	global $data;
	return $data->get_lots(null, $sortColumn);
}
function GetPassTypes($sortColumn) {
	global $data;
	return $data->get_passTypes(null, $sortColumn);
}
function GetSettingsForUser($id) {
	global $data;
	return $data->get_settingsByUser($id);
}
function GetSchemes($id) {
	global $data;
	return $data->get_scheme($id);
}
function GetRulesByLot($id) {
	global $data;
	return $data->get_rulesByLot($id);
}

// Creation methods.
// The only unique is CreateRules, where $lots
//  and $passTypes need to be arrays where the rule will apply.
// All functions return the ID of the newly created object,
//  false on an unsuccessful database insertion.
// CreateRules will give an array of IDs of the created rules,
//  any entry that didn't go through will be false.
function CreateLot($name, $desc, $pic, $coords, $scheme) {
	global $data;
	return $data->insert_lot($name, $desc, $pic, $coords, $scheme);
}
function CreatePassType($name) {
	global $data;
	return $data->insert_passType($name);
}
function CreateRules($lots, $passTypes, $startDate, $endDate, $startTime, $endTime, $days) {
	global $data;
	$ruleIds = array();
	foreach ($lots as $lot) {
		foreach ($passTypes as $pass) {
			$newId = $data->insert_rule($lot, $pass, $startDate, $endDate, $startTime, $endTime, $days);
			if ($newId !== false) $ruleIds[] = $newId;
		}
	}
	if (count($ruleIds > 0)) return $ruleIds;
	else return false; // no ids to return
}
function CreateExceptions($lots, $passTypes, $start, $end, $allow) {
	global $data;
	$exceptionIds = array();
	foreach($lots as $lot) {
		foreach($passTypes as $pass) {
			$newId = $data->insert_exception($lot, $pass, $start, $end, $allow);
			if($newId !== false) $exceptionIds[] = $newId;
		}
	}
	if (count($exceptionIds > 0)) return $exceptionIds;
	else return false; // no ids to return
}

// Update methods.
function RenamePassType($id, $newName) {
	global $data;
	return $data->update_passType($id, $newName);
}

// Deletion methods.
// A single delete method takes a single value with the ID
//  of the object to delete from the database.
// The multiple delete methods take an array of IDs and deletes
//  every object.
// Functions return false if the deletion wasn't successful, true if it was.
function DeleteLots($ids) {
	global $data;
	return $data->delete_lot(implode(',', $ids));
}
function DeletePassTypes($ids) {
	global $data;
	return $data->delete_passType(implode(',', $ids));
}
function DeleteRules($ids) {
	global $data;
	return $data->delete_rule(implode(',', $ids));
}
function DeleteExceptions($ids) {
	global $data;
	return $data->delete_exception(implode(',', $ids));
}

// Deprecated method, you can find a list
//  of parkable pass types in each lot.
function CanIParkHereNow($lotId, $passTypeId) {
/*  function CanIParkHereNow($lotId, $passId)
	$lotId = single id of requested lot
	$passId = single id of requested pass type
	returns results for that lot
 */

	// Returns if you can or can not park in the requested
	// lot(s) based on current time of day and the passtype sent.		

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
}

function debug($a) {
	echo "<pre>";
	print_r($a);
	echo "</pre>";
}

?>
