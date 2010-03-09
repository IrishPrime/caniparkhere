<?php

	require_once("./data.php");

	$data = new data();
	echo("<pre>");
	print_r($data->get_lots());
	echo("</pre>");

?>
