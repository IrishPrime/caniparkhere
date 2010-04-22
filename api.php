<?php
# Web service for _logic.php
# TODO: Authentication

require_once("./_logic.php");
@header("Content-Type: application/x-javascript");

@array_map(addslashes, $_GET);

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
		echo "<p><a href=\"./api.php?function=CanIParkHereNow&arg1=1\">CanIParkHereNow</a>(lot, pass)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=GetExceptionsByLot&lot=1\">GetExceptionsByLot</a>(lot)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=GetCurrentLot\">GetCurrentLot</a>(lat, lng)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=GetLots\">GetLots</a>(sort)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=GetPassTypes\">GetPassTypes</a>(sort)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=GetRulesByLot&lot=1\">GetRulesByLot</a>(lot)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=GetSchemes\">GetSchemes</a>(id)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=GetSettingsForUser&id=0\">GetSettingsForUser</a>(id)<br/>\n";
		echo "</p>\n";

		echo "<p><a href=\"./api.php?function=WhereDidIPark&id=1\">WhereDidIPark</a>(id)<br/>\n";
		echo "</p>\n";
		$result = array();
		break;
}

@array_map(htmlentities, $result);

echo stripslashes($result != null ? json_encode($result): "");
?>
