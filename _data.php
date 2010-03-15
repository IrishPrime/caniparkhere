<?php
	
class data {

	private $conn;
	private $db_name = "ciph";
	private $sql;
	
	// **TO-DO** order all SQL queries so
	// data comes out in consistant order (by IDs)

	// constructor (connects to mysql server and changes to ciph db)
	function __construct() {
		require("./_settings.php");
		
		// create connection - mysql_connect("banshee:3306", "mysql", "cpsc123")
		$conn = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Can't connect to MySQL server: " . mysql_error());
		
		// open database
		mysql_select_db($this->db_name, $conn) or die("Can't open database: " . mysql_error());
	}
	
	public function get_lots($ids) {
		$sql = "select * from lots";
		if ($ids != null) $sql .= " where id in (" . $ids . ")";
		$sql .= " order by id asc";
		$result = mysql_query($sql);
		
		if (!$result) die("Error getting lots from DB.");
		
		$lots = array();
		
		if (mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_assoc($result)) {
				$lots[$row["id"]] = array(
						"name" => $row["lotName"],
						"description" => $row["lotDescription"]);
			}
			return $lots;
		}
		else { //echo "No lots defined.";
			return null;
		}
	}
	public function get_passTypes($ids) {
		$sql = "select * from passTypes";
		if ($ids != null) $sql .= " where id in (" . $ids . ")";
		$sql .= " order by passName asc";
		$result = mysql_query($sql);
		//echo ($sql);
		
		if (!$result) die("Error getting passTypes from DB.");
		
		$passTypes = array();
		
		if (mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_assoc($result)) {
				$passTypes[$row["id"]] = array (
					"name" => $row["passName"]);
			}
			//print_r($passTypes);
			return $passTypes;
		}
		else { //echo "No passtypes defined.";
			return null;
		}
	}
	public function get_rulesForLots($ids) {
		$sql = "select * from rules";
		if ($ids != null) $sql .= " where lot in (" . $ids . ")";
		$sql .= " order by startDate, endDate";
		$result = mysql_query($sql);
		
		if (!$result) die("Error getting rules from DB (by lots).");
		
		$rules = array();
		
		if (mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_assoc($result)) {
				$rules[$row["id"]] = array(
					"lotId" => $row["lot"],
					"passTypeId" => $row["passType"],
					"startDate" => $row["startDate"],
					"endDate" => $row["endDate"],
					"startTime" => $row["startTime"],
					"endTime" => $row["endTime"],
					"days" => $row["days"]);
			}
			return $rules;
		}
		else {
			//echo "No rules defined for that lot.";
			return null;
		}
	}
	public function get_rulesForPassTypes($ids) {
		$sql = "select * from rules";
		if ($ids != null) $sql .= " where passType in (" . $ids . ")";
		$sql .= " order by startDate, endDate";
		$result = mysql_query($sql);
		
		if (!$result) die("Error getting rules from DB (by passTypes).");
		
		$rules = array();
		
		if (mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_assoc($result)) {
				$rules[$row["id"]] = array(
					"name" => $row["ruleName"],
					"lotId" => $row["lotId"],
					"passTypeId" => $row["passTypeId"],
					"startDate" => $row["startDate"],
					"endDate" => $row["endDate"],
					"startTime" => $row["startTime"],
					"endTime" => $row["endTime"],
					"days" => $row["days"]);
			}
			return $rules;
		}
		else {
			//echo "No rules defined for that passtype.";
			return null;
		}
	}
	
	public function insert_passType($name) {
		$sql = "insert into passTypes values (" + $name + ")";
		$result = mysql_query($sql);
		
		if ($result) { // return id
			return mysql_insert_id();
		}
		else {
			//echo "Couldn't insert passtype " + $name + ".";
			return null;
		}
	}
	public function delete_passType($id) {
		$sql = "delete from passTypes where id in (" + $id + ")";
		return mysql_query($sql);
	}
	
	// Destructor
	function __destruct() {
		$this->close_me();
	}
	
	// Disconnect
	public function close_me() {
		mysql_close();
	}

}
?>
