<?php
include("data.php");
$data = new data();
?>

<div id="map_canvas" style="width:100%; height:84%"><script type="text/javascript">initialize();</script></div>

<div class="column" style="border-right: 1px dashed black;">
<h2>Pass Types</h2>
<ol>
<?php
$passes = $data->get_passTypes();

if(is_array($passes))
	foreach($passes as $pass) {
		echo "<li>".$pass."</li>\n";
	}
?>
</ol>
</div>

<div class="column" style="">
<h2>Parking Lots</h2>
<ol>
<?php
$lots = $data->get_lots();

if(is_array($lots))
	foreach($lots as $lot) {
		echo "<li>".$lot["name"]." - ".$lot["description"]."</li>\n";
	}
?>
</ol>
</div>
