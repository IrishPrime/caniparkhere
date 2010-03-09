<?php
class data {
	private $conn;
	private $db_name = "ciph";
	private $sql;

	// Constructor
	function __construct($mysql_server, $mysql_user, $mysql_password) {
		// create connection
		$conn = mysql_connect($mysql_server, $mysql_user, $mysql_password) or die("Could not connect: " . mysql_error());
		if (!$conn) {
			echo "Unable to connect to database.";
			exit;
		}
		
		// open database
		mysql_select_db($db_name, $conn)) or die(mysql_error());
	}
	
	public function get_lotNames() {
		$sql = "select name from lots";
		$result = mysql_query($sql);
		$lotNames = array();
		
		//if (mysql_num_rows($result) != 0) {
		
		if 
		
		while ($row = mysql_fetch_assoc($result)) {
			$lotNames = $row["name"];
		}
		
		}
		else {
			echo "get_lots() error<br>\n";
		}
	}

	// Destructor
	function __destruct() {
		close_me();
	}

	// Disconnect
	public function close_me() {
		mysql_close();
	}
}
?>
