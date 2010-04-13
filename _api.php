<?php
# Web service for _logic.php
require_once("./_logic.php");
@header("Content-Type: application/x-javascript");

foreach($_GET as $get) {
	addslashes($get);
}

switch($_GET["function"]) {
	case "CanIParkHereNow":
		$json = CanIParkHereNow($_GET["lot"], $_GET["pass"]);
		break;
	case "GetExceptionsByLot":
		$json = GetExceptionsByLot($_GET["lot"]);
		break;
	case "GetLots":
		$json = GetLots($_GET["sort"]);
		break;
	case "GetPassTypes":
		$json = GetPassTypes($_GET["sort"]);
		break;
	case "GetRulesByLot":
		$json = GetRulesByLot($_GET["lot"]);
		break;
	case "GetSchemes":
		$json = GetSchemes($_GET["id"]);
		break;
	case "GetSettingsForUser":
		$json = GetSettingsForUser($_GET["id"]);
		break;
	default:
		@header("Content-Type: text/html");
		printf("<a href=\"./_api.php?function=CanIParkHereNow&arg1=1\">CanIParkHereNow</a><br/>\n");
		printf("<a href=\"./_api.php?function=GetExceptionsByLot&lot=1\">GetExceptionsByLot</a><br/>\n");
		printf("<a href=\"./_api.php?function=GetLots\">GetLots</a><br/>\n");
		printf("<a href=\"./_api.php?function=GetPassTypes\">GetPassTypes</a><br/>\n");
		printf("<a href=\"./_api.php?function=GetRulesByLot&lot=1\">GetRulesByLot</a><br/>\n");
		printf("<a href=\"./_api.php?function=GetSchemes\">GetSchemes</a><br/>\n");
		printf("<a href=\"./_api.php?function=GetSettingsForUser&id=0\">GetSettingsForUser</a><br/>\n");
		$json = array();
		break;
}

@array_map("htmlentities", $json);
echo stripslashes($json != null ? json_encode($json): "");
?>
