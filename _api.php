<?php
# Web service for _logic.php
# TODO: Authentication
# TODO: Output format selection

require_once("./_logic.php");
@header("Content-Type: application/x-javascript");

foreach($_GET as $get) {
	addslashes($get);
}

switch($_GET["function"]) {
	case "CanIParkHereNow":
		$result = CanIParkHereNow($_GET["lot"], $_GET["pass"]);
		break;
	case "GetCurrentLot":
		$result = GetCurrentLot($_GET["lat"], $_GET["lng"]);
		break;
	case "GetExceptionsByLot":
		$result = GetExceptionsByLot($_GET["lot"]);
		break;
	case "GetLots":
		$result = GetLots($_GET["sort"]);
		break;
	case "GetPassTypes":
		$result = GetPassTypes($_GET["sort"]);
		break;
	case "GetRulesByLot":
		$result = GetRulesByLot($_GET["lot"]);
		break;
	case "GetSchemes":
		$result = GetSchemes($_GET["id"]);
		break;
	case "GetSettingsForUser":
		$result = GetSettingsForUser($_GET["id"]);
		break;
	case "WhereDidIPark":
		$result = WhereDidIPark($_GET["id"]);
		break;
	default:
		@header("Content-Type: text/html");
		echo "<a href=\"./_api.php?function=CanIParkHereNow&arg1=1\">CanIParkHereNow</a><br/>\n";
		echo "<a href=\"./_api.php?function=GetExceptionsByLot&lot=1\">GetExceptionsByLot</a><br/>\n";
		echo "<a href=\"./_api.php?function=GetCurrentLot\">GetCurrentLot</a><br/>\n";
		echo "<a href=\"./_api.php?function=GetLots\">GetLots</a><br/>\n";
		echo "<a href=\"./_api.php?function=GetPassTypes\">GetPassTypes</a><br/>\n";
		echo "<a href=\"./_api.php?function=GetRulesByLot&lot=1\">GetRulesByLot</a><br/>\n";
		echo "<a href=\"./_api.php?function=GetSchemes\">GetSchemes</a><br/>\n";
		echo "<a href=\"./_api.php?function=GetSettingsForUser&id=0\">GetSettingsForUser</a><br/>\n";
		echo "<a href=\"./_api.php?function=WhereDidIPark&id=1\">WhereDidIPark</a><br/>\n";
		$result = array();
		break;
}

@array_map("htmlentities", $result);

switch($_GET["format"]) {
	case "xml":
		break;
	case "json":
		echo stripslashes($result != null ? json_encode($result): "");
		break;
	default:
		echo stripslashes($result != null ? json_encode($result): "");
		break;
}
?>
