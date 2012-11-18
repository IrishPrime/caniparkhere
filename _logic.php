<?php
# Data manipulation and function library.
# TODO: Reduce duplicate/redundant data returned.
# TODO: GetLots should return only lot info, schemes/passes should be linked instead of embedded

error_reporting(E_ALL ^ E_NOTICE);
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
	private $sql;

	function __construct() {
		require("./_settings.php");
		// constructor (connects to mysql server and changes to CIPH database)

		// create connection
		$conn = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or die("Can't connect to MySQL server: " . mysql_error());

		// open database
		mysql_select_db(MYSQL_DB, $conn) or die("Can't open database: " . mysql_error());
	}

	private function create_admins($result) {
		$admins = null;
		if(mysql_num_rows($result) != 0) {
			while($row = mysql_fetch_assoc($result)) {
				$admins[$row["id"]] = array(
					"id" => $row["id"],
					"lastName" => $row["lastName"],
					"firstName" => $row["firstName"],
					"email" => $row["email"]);
			}
		}
		return $admins;
	}
	private function create_lastLoc($result) {
		$lastLoc = null;
		if(mysql_num_rows($result) != 0) {
			$lastLoc = mysql_fetch_assoc($result);
		}
		return $lastLoc;
	}
	private function create_lots($result) {
		$lots = null;
		if (mysql_num_rows($result) != 0) {
			$lots = array();
			while ($row = mysql_fetch_assoc($result)) {
				$id = $row["id"];
				$name = $row["name"];
				$desc = $row["description"];
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
					"middle" => $middle,
					"coords" => $coords->cdlArray(), // array
					"scheme" => $scheme, // array
					"currentPassTypes" => $currentPasses); // array
			}
		}
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
	private function create_rulesByLots($result) {
		$rules = null;

		// if there are rules to sort
		if (mysql_num_rows($result) != 0) {
			$rules = array();

			// loop through and create lot arrays
			while ($row = mysql_fetch_assoc($result)) {
				// keys
				$lot = $row["lot"];
				$dateRange = "[" . $row["startDate"] . "][" . $row["endDate"] . "]";
				$timeRange = "[" . $row["startTime"] . "][" . $row["endTime"] . "]";
				$dow = "[" . $row["days"] . "]";
				$passType = $row["passType"];

				// check for and create lot data
				if (!array_key_exists($lot, $rules)) {
					$rules[$lot] = array(
						"name" => $row["lotName"],
						"description" => $row["lotDescription"],
						"dateRange" => array());
				}

				// check for and create dateRange data
				if (!array_key_exists($dateRange, $rules[$lot]["dateRange"])) {
					$rules[$lot]["dateRange"][$dateRange] = array(
						"startDate" => $row["startDate"],
						"endDate" => $row["endDate"],
						"timeRange" => array());
				}

				// check for and create timeRange data
				if(!array_key_exists($timeRange, $rules[$lot]["dateRange"][$dateRange]["timeRange"])) {
					$rules[$lot]["dateRange"][$dateRange]["timeRange"][$timeRange] = array(
						"startTime" => $row["startTime"],
						"endTime" => $row["endTime"],
						"dow" => array());
				}

				// check for and create dow data
				if (!array_key_exists($dow, $rules[$lot]["dateRange"][$dateRange]["timeRange"][$timeRange]["dow"])) {
					$rules[$lot]["dateRange"][$dateRange]["timeRange"][$timeRange]["dow"][$dow] = array(
						"days" => $row["days"],
						"passTypes" => array());
				}

				// special passType object
				// the array path will have been created by this point
				$rules[$lot]["dateRange"][$dateRange]["timeRange"][$timeRange]["dow"][$dow]["passTypes"][$passType] = array(
					"ruleId" => $row["rule"],
					"id" => $passType,
					"name" => $row["passTypeName"]);
			}
		}
		return $rules;
	}
	private function create_exceptionsByLot($result) {
		$exceptions = null;

		// if there are rules to sort
		if (mysql_num_rows($result) != 0) {
			$exceptions = array();

			// loop through and create lot arrays
			while ($row = mysql_fetch_assoc($result)) {
				// keys
				$lot = $row["lot"];
				$exceptionKey = 
					"[" . $row["start"] . "]" .
					"[" . $row["end"] . "]" .
					"[" . $row["allowed"] . "]";
				$passType = $row["passType"];

				// check for and create lot data
				if (!array_key_exists($lot, $exceptions)) {
					$exceptions[$lot] = array(
						"name" => $row["lotName"],
						"description" => $row["lotDescription"],
						"exceptions" => array());
				}

				// check for and create dateRange data
				if (!array_key_exists($exceptionKey, $exceptions[$lot]["exceptions"])) {
					$exceptions[$lot]["exceptions"][$exceptionKey] = array(
						"start" => $row["start"],
						"end" => $row["end"],
						"allowed" => $row["allowed"],
						"passTypes" => array());
				}

				// special passType object
				// the array path will have been created by this point
				$exceptions[$lot]["exceptions"][$exceptionKey]["passTypes"][$passType] = array(
					"exceptionId" => $row["exception"],
					"id" => $passType,
					"name" => $row["passTypeName"]);
			}
		}
		return $exceptions;
	}
	private function create_settings($result, $user) {
		$settings = null;
		if (mysql_num_rows($result) != 0) {
			$settings = array();
			while ($row = mysql_fetch_assoc($result)) {
				$settings[$row["name"]] = $row["value"];
			}
			if ($user != null) {
				$row = mysql_fetch_assoc($user);
				$settings["passType"] = $row["passType"];
				$settings["lastLoc"] = $row["lastLoc"];
			}
		}
		return $settings;
	}
	private function create_scheme($result) {
		$scheme = null;
		if (mysql_num_rows($result) != 0) {
			$scheme = array();
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
	private function create_schemes($result) {
		$schemes = null;
		if (mysql_num_rows($result) != 0) {
			$schemes = array();
			while($row = mysql_fetch_assoc($result)) {
				$schemes[$row["id"]] = array(
					"id" => $row["id"],
					"name" => $row["name"],
					"lineColor" => $row["lineColor"],
					"lineWidth" => $row["lineWidth"],
					"lineOpacity" => $row["lineOpacity"],
					"fillColor" => $row["fillColor"],
					"fillOpacity" => $row["fillOpacity"]);
			}
		}
		return $schemes;
	}

	public function get_admins($sortColumn) {
		$sql = "SELECT * FROM users WHERE admin = 1 ORDER BY $sortColumn";
		$result = mysql_query($sql);
		if(!$result) die("MySQL error: get_admins($sortColumn)");
		else return $this->create_admins($result);
	}
	public function get_lots($ids = null, $sortColumn = "name") {
		$sql = "SELECT * FROM lots";
		if ($ids != null) $sql .= " WHERE id in (" . $ids . ")";
		if ($sortColumn == null) $sortColumn = "name";
		$sql .= " ORDER BY " . $sortColumn . " ASC";

		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_lots($ids)");
		else return $this->create_lots($result);
	}
	public function get_lastLoc($id) {
		$sql = "SELECT lastLoc FROM users WHERE id = $id";
		$result = mysql_query($sql);
		if(!$result) die("MySQL error: get_lastLoc($id)");
		else return $this->create_lastLoc($result);
	}
	public function get_passTypes($ids, $sortColumn) {
		$sql = "SELECT * FROM passTypes";
		if ($ids != null) $sql .= " WHERE id IN (" . $ids . ")";
		if ($sortColumn == null) $sortColumn = "name"; // was id
		$sql .= " ORDER BY " . $sortColumn . " ASC";

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
		$sql = "select lot, lots.name as lotName, lots.description as lotDescription,"
			. " passType, passTypes.name as passTypeName,"
			. " rules.id as rule, startDate, startTime, endDate, endTime, days"
			. " from rules"
			. " inner join lots on lots.id = rules.lot"
			. " inner join passTypes on passTypes.id = rules.passType";
		if ($id != null) $sql .= " where lot in (" . $id . ")";
		$sql .= " order by lotName, endDate desc, endTime desc, days asc, passTypeName";

		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_rulesByLots($id)");
		else return $this->create_rulesByLots($result); 
	}
	public function get_exceptionsByLot($ids) {
		$sql = "select lot, lots.name as lotName, lots.description as lotDescription,"
			. " passType, passTypes.name as passTypeName,"
			. " exceptions.id as exception, start, end, allowed"
			. " from exceptions"
			. " inner join lots on lots.id = exceptions.lot"
			. " inner join passTypes on passTypes.id = exceptions.passType";
		if ($ids != null) $sql .= " where lot in (" . $ids . ")";
		$sql .= " order by lotName, end desc, passTypeName";

		$result = mysql_query($sql);
		if (!$result) die("MySQL error: get_exceptionsByLots($ids)");
		else return $this->create_exceptionsByLot($result);
	}
	public function get_settingsByUser($id) {
		$sql = "select * from settings";
		if ($id != null) $sql .= " where user in (" . $id . ")";
		$sql .= " order by id";
		$result = mysql_query($sql);
		$user = null;

		if ($id != null) {
			$sql = "select passType, lastLoc from users";
			$sql .= " where id in (" . $id . ")";
			$user = mysql_query($sql);
			if(!$user) die("MySQL error: get_settingByUser($id)");
			if ($id == 0) $user = null; // no global
		}

		if (!$result) die("MySQL error: get_settingsByUser($id)");
		else return $this->create_settings($result, $user);
	}
	public function get_scheme($id) {
		$sql = "SELECT * FROM schemes";
		if ($id != null) $sql .= " where id in (" . $id . ")";
		$sql .= " ORDER BY id";

		$result = mysql_query($sql);
		if (!$result) die ("MySQL error: get_scheme($id)");
		else return $this->create_scheme($result);
	}
	public function get_schemes($id) {
		$sql = "SELECT * FROM schemes";
		if ($id != null) $sql .= " where id in (" . $id . ")";
		$sql .= " ORDER BY id";

		$result = mysql_query($sql);
		if (!$result) die ("MySQL error: get_schemes($id)");
		else return $this->create_schemes($result);
	}

	private function addSingleQuotes($string) {
		return "'" . $string . "'";
	}
	public function insert_lot($name, $desc, $coords, $scheme) {
		$sql = "insert into lots "
			. "(name, description, coords, scheme) "
			. "values (" 
			. $this->addSingleQuotes($name) . ", "
			. $this->addSingleQuotes($desc) . ", "
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
	public function insert_scheme($name, $lineColor, $lineWidth, $lineOpacity, $fillColor, $fillOpacity) {
		$sql = "insert into schemes (name, lineColor, lineWidth, lineOpacity, fillColor, fillOpacity) values ("
			. $this->addSingleQuotes($name) . ", "
			. $this->addSingleQuotes($lineColor) . ", "
			. $this->addSingleQuotes($lineWidth) . ", "
			. $this->addSingleQuotes($lineOpacity) . ", "
			. $this->addSingleQuotes($fillColor) . ", "
			. $this->addSingleQuotes($fillOpacity) . ")";

		$result = mysql_query($sql);

		if ($result) return mysql_insert_id();
		else return false;
	}

	public function update_passType($id, $name) {
		$sql = "UPDATE passTypes SET"
			. " name = " . $this->addSingleQuotes($name)
			. " WHERE id = $id";
		return mysql_query($sql);
	}
	public function update_lot($id, $name, $desc, $coords, $scheme) {
		$sql = "UPDATE lots SET"
			. " name = " . $this->addSingleQuotes($name) . ","
			. " description = " . $this->addSingleQuotes($desc) . ","
			. " coords = " . $this->addSingleQuotes($coords) . ","
			. " scheme = $scheme"
			. " WHERE id = $id";
		return mysql_query($sql);
	}
	public function update_scheme($id, $name, $lineColor, $lineWidth, $lineOpacity, $fillColor, $fillOpacity) {
		$sql = "UPDATE schemes SET"
			. " name = " . $this->addSingleQuotes($name) . ","
			. " lineColor = " . $this->addSingleQuotes($lineColor) . ","
			. " lineWidth = " . $this->addSingleQuotes($lineWidth) . ","
			. " lineOpacity = " . $this->addSingleQuotes($lineOpacity) . ","
			. " fillColor = " . $this->addSingleQuotes($fillColor) . ","
			. " fillOpacity = " . $this->addSingleQuotes($fillOpacity)
			. " WHERE id = $id";
		return mysql_query($sql);
	}

	public function delete_lot($ids) {
		$sql = "DELETE FROM rules WHERE lot IN (" . $ids . ")";
		mysql_query($sql);
		$sql = "DELETE FROM exceptions WHERE lot IN (" . $ids . ")";
		mysql_query($sql);
		$sql = "DELETE FROM lots WHERE id IN (" . $ids . ")";
		return mysql_query($sql);
	}

	public function delete_passType($ids) {
		$sql = "DELETE FROM rules WHERE passType IN (" . $ids . ")";
		mysql_query($sql);
		$sql = "DELETE FROM exceptions WHERE passType IN (" . $ids . ")";
		mysql_query($sql);
		$sql = "DELETE FROM passTypes WHERE id IN (" . $ids . ")";
		return mysql_query($sql);
	}

	public function delete_rule($ids) {
		$sql = "DELETE FROM rules WHERE id IN (" . $ids . ")";
		mysql_query($sql);
		return mysql_affected_rows();
	}

	public function delete_exception($ids) {
		$sql = "DELETE FROM exceptions WHERE id IN (" . $ids . ")";
		mysql_query($sql);
		return mysql_affected_rows();
	}

	public function delete_schemes($ids) {
		// Detele Schemes
		$sql = "DELETE FROM schemes WHERE id IN (" . $ids . ")";
		mysql_query($sql);
		$count = mysql_affected_rows();

		// Find lowest scheme id remaining
		$sql = "SELECT id FROM schemes ORDER BY id ASC";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$low = $row[0];
		// Set any lots using deleted schemes to use lowest scheme
		$sql = "UPDATE lots SET scheme=$low WHERE scheme IN (". $ids . ")";
		mysql_query($sql);

		// Return number of schemes deleted
		return $count;
	}

	private function whatPassTypesCanParkHere($id) {
		// returns array of passTypes, null = no pass types

		// set requested timestamp
		$requestedTime = new DateTime(null, new DateTimeZone('America/New_York'));

		// get rules & exceptions
		$lots = $this->get_rulesByLot($id);
		$rules = $lots[$id]["dateRange"];
		$lots = $this->get_exceptionsByLot($id);
		$exceptions = $lots[$id]["exceptions"];

		// copy passTypes allowed by current rules
		$allowedPassTypes = array();

		// copy passTypes that are affected by current rules
		if ($rules != null) {
			foreach ($rules as $dateRange) {
				foreach ($dateRange["timeRange"] as $timeRange) {
					foreach ($timeRange["dow"] as $dow) {							
						$passTypes = $dow["passTypes"];
						// rule converted into one deep array for doesRuleApply()
						$oldRule = array(
							"startDate" => $dateRange["startDate"],
							"endDate" => $dateRange["endDate"],
							"startTime" => $timeRange["startTime"],
							"endTime" => $timeRange["endTime"],
							"days" => $dow["days"]);

						if ($this->doesRuleApply($oldRule, $requestedTime)) {
							if ($passTypes != null) {
								foreach ($passTypes as $passType) {
									$allowedPassTypes[$passType["id"]] = $passType;
								}
							}
						}
					}
				}
			}
		}

		// copy passTypes that are affected by current exceptions
		if ($exceptions != null) {
			foreach ($exceptions as $exception) {
				$passTypes = $exception["passTypes"];
				if ($this->doesExceptionApply($exception, $requestedTime)) {
					if ($passTypes != null) {
						foreach ($passTypes as $passType) {
							if ($exception["allowed"]) {
								$allowedPassTypes[$passType["id"]] = $passType;
							}
							else {
								unset($allowedPassTypes[$passType["id"]]);
							}
						}
					}
				}
			}
		}

		return (count($allowedPassTypes) == 0 ? null : $allowedPassTypes);
	}

	private function doesRuleApply($rule, $parkTimestamp) {
		// make sure you're in date range
		// compares unix time formatted now and start/end dates of rule
		$now = (int)$parkTimestamp->format('U');
		$start = strtotime($rule["startDate"] . ' 00:00:00');
		$end = strtotime($rule["endDate"] . ' 23:59:59');
		if(!($start <= $now && $end >= $now)) return false;

		// make sure you're in time range
		// uses current date + start and end times to see if you're in correct time
		$nowTime = date_parse($parkTimestamp->format('Y-m-d H:i:s'));
		$startTime = explode(":", $rule["startTime"]);
		$start = new DateTime();
		$start->setDate($nowTime["year"], $nowTime["month"], $nowTime["day"]);
		$start->setTime($startTime[0], $startTime[1], $startTime[2]);

		$endTime = explode(":", $rule["endTime"]);
		$end = new DateTime();
		$end->setDate($nowTime["year"], $nowTime["month"], $nowTime["day"]);
		$end->setTime($endTime[0], $endTime[1], $endTime[2]);
		if(!($start->format("U") <= $now && $end->format("U") >= $now)) echo false;

		// make sure you're in day of week
		$now = getdate($parkTimestamp->format('U'));
		$haystack = explode(",", $rule["days"]);
		$found = array_search($now["wday"], $haystack);
		if($found === false) return false;
		else return true;
	}

	private function doesExceptionApply($exception, $parkTimestamp) {
		$now = (int)$parkTimestamp->format('U');
		$start = strtotime($exception["start"]);
		$end = strtotime($exception["end"]);
		return ($start <= $now && $end >= $now ? true : false);
	}

	public function whereAmI($point) {
		$lots = $this->get_lots();
		foreach($lots as $lot) {
			if ($this->isInPolygon($point, $lot["coords"])) {
				return $lot;
			}
		}
	}

	private function isInPolygon($point, $polygon) {
		$point = $this->pointStringToCoordinates($point);
		$path = array(); 
		foreach ($polygon as $vertex) {
			$path[] = $this->pointStringToCoordinates($vertex); 
		}

		$j = 0;
		$oddNodes = false;
		$x = floatval($point["x"]);
		$y = floatval($point["y"]);
		$pathLength = count($path);

		for ($i = 0; $i < $pathLength; $i++) {
			$j++;
			if ($j == $pathLength) $j = 0;

			$iLat = floatval($path[$i]["y"]);
			$iLng = floatval($path[$i]["x"]);
			$jLat = floatval($path[$j]["y"]);
			$jLng = floatval($path[$j]["x"]);

			$a = floatval($y - $iLat);
			$b = floatval($jLat - $iLat);
			$c = floatval($jLng - $iLng);
			$d = ($b == 0 ? 0 : floatval($iLng + $a / $b * $c));

			if ((($iLat < $y) && ($jLat >= $y)) || (($jLat < $y) && ($iLat >= $y))) {
				if ($d < $x ) {
					$oddNodes = !$oddNodes;
				}
			}
		}

		return $oddNodes;
	}

	private function pointStringToCoordinates($pointString) {
		$coordinates = explode(",", $pointString);
		return array("x" => $coordinates[0], "y" => $coordinates[1]);
	}

	function __destruct() {
		mysql_close();
	}
}

$data = new data();

// Data load methods.
// Lots and PassTypes
//  Can be sorted by any field,
//  but default to "name" if $sortColumn is null.
// Settings
//  $id = user id, 0 = global, null = all
// Schemes
//	$ids = single scheme id, null = all schemes
function GetAdmins($sortColumn = "lastName") {
	global $data;
	return $data->get_admins($sortColumn);
}
function GetLots($sortColumn = null) {
	global $data;
	return $data->get_lots(null, $sortColumn);
}
function GetPassTypes($sortColumn = null) {
	global $data;
	return $data->get_passTypes(null, $sortColumn);
}
function GetSettingsForUser($id = null) {
	global $data;
	return $data->get_settingsByUser($id);
}
function GetSchemes($id = null) {
	global $data;
	return $data->get_schemes($id);
}
function GetRulesByLot($id = null) {
	global $data;
	return $data->get_rulesByLot($id);
}
function GetExceptionsByLot($id = null) {
	global $data;
	return $data->get_exceptionsByLot($id);
}

// Logic functions.
// Returns modified data from database.
function WhereCanIPark($id) {
	global $data;
	$lots = $data->get_lots();
	$allowedLots = array();

	if ($lots != null) {
		foreach($lots as $lot) {
			$currentPassTypes = $lot["currentPassTypes"];
			if ($currentPassTypes != null) {
				$found = false;
				foreach($currentPassTypes as $passType) {
					if ($passType["id"] == $id) {
						$allowedLots[$lot["id"]] = $lot["name"];
					}
				}
			}
		}
	}

	return $allowedLots;
}
function WhereDidIPark($id) {
	global $data;
	return $data->get_lastLoc($id);
}
function CanIParkHere($location, $passType) {
	if ($location != null) {
		global $data;
		$lot = $data->whereAmI($location);
		$allowedLots = WhereCanIPark($passType);

		$ciph = array_key_exists($lot["id"], $allowedLots);
		$lotName = ($lot != null ? $lot["name"] : null);

		$answer = array(
			"ciph" => $ciph,
			"lotName" => $lotName
		);
		return $answer;
	}
}

// Creation methods.
// CreateRules, where $lots
// 	$lots and $passTypes are arrays of affected lots and passTypes.
// All functions return the ID(s) of the newly created object,
//  false on an unsuccessful database insertion.
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
	$exceptionIDs = array();
	foreach($lots as $lot) {
		foreach($passTypes as $pass) {
			$newId = $data->insert_exception($lot, $pass, $start, $end, $allow);
			if($newId !== false) $exceptionIDs[] = $newId;
		}
	}
	if (count($exceptionIDs > 0)) return $exceptionIDs;
	else return false; // no ids to return
}

// Update methods.
// Will create new entry if $id == 0.
// Returns true on successful update, false on uncessesful.
// If the object is new, returns the new object's unique id.
function UpdatePassType($id, $newName) {
	global $data;
	if ($id == 0) return $data->insert_passType($newName);
	elseif ($id > 0) return $data->update_passType($id, $newName);
}
function UpdateLot($id, $name, $desc, $coords, $scheme) {
	global $data;
	if($id == 0) return $data->insert_lot($name, $desc, $coords, $scheme);
	elseif ($id > 0) return $data->update_lot($id, $name, $desc, $coords, $scheme);
}
function UpdateScheme($id, $name, $lineColor, $lineWidth, $lineOpacity, $fillColor, $fillOpacity) {
	global $data;
	if ($id == 0) return $data->insert_scheme($name, $lineColor, $lineWidth, $lineOpacity, $fillColor, $fillOpacity);
	elseif ($id > 0) return $data->update_scheme($id, $name, $lineColor, $lineWidth, $lineOpacity, $fillColor, $fillOpacity);
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
function DeleteSchemes($ids) {
	global $data;
	return $data->delete_schemes(implode(',', $ids));
}

function debug($a) {
	echo "<pre>";
	print_r($a);
	echo "</pre>";
}
?>
