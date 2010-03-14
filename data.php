<?php

// canIParkHere(lotId, passId)
	
class data {

	private $conn;
	private $db_name = "ciph";
	private $sql;

	// Constructor
	function __construct() { //$mysql_server, $mysql_user, $mysql_password) {
		require("./_settings.php");
		
		// create connection
		$conn = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect: " . mysql_error());
		//$conn = mysql_connect("banshee:3306", "mysql", "cpsc123") or die("Could not connect: " . mysql_error());
		if (!$conn) die("Unable to connect to database.");
		
		// open database
		mysql_select_db($this->db_name, $conn) or die(mysql_error());
	}
	
	public function canIParkHere($lotId, $passId) {
		if ($passId == 6) {
			if ($lotId == 1) return true;
			else if ($lotId == 2) return true;
			else if ($lotId == 3) return true;
		} else if ($passId == 7) {
			if ($lotId == 1) return true;
			else if ($lotId == 2) return true;
			else if ($lotId == 3) return false;
		} else {
			return false;
		}
	}
	
	public function get_lots() {
		$sql = "select * from lots order by id asc;";
		$result = mysql_query($sql);
		$lots = array();
		
		if (mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_assoc($result)) {
				$lots[$row["id"]] = 
					array(
						"id" => $row["id"],
						"name" => $row["lotName"],
						"description" => $row["lotDescription"]);
			}
			return $lots;
		}
		else {
			echo "No lots defined.";
		}
	}
	public function get_passTypes() {
		$sql = "select * from passTypes order by passName asc;";
		$result = mysql_query($sql);
		$passTypes = array();
		
		if (mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_assoc($result)) {
				$passTypes[$row["id"]] = $row["passName"];
			}
			return $passTypes;
		}
		else {
			echo "No passtypes defined.";
		}
	}
	
	public function insert_passType($name) {
		$sql = "insert into passTypes values (" + $name + ");";
		$result = mysql_query($sql);
		
		if ($result) { // return id
			return mysql_insert_id();
		}
		else {
			echo "Couldn't insert passtype " + $name + ".";
		}
	}
	public function delete_passType($id) {
		$sql = "delete from passTypes where id in (" + $id + ")";
		
		return mysql_query($sql);
		
		//$result = mysql_query($sql);
		
		//if ($result) {
		//	echo "Passtype deleted.";
		//	return true;
		//}
		//else {
		//	echo "Passtype couldn't be deleted.";
		//	return false;
		//}
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
