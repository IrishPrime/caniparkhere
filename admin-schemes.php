<?php
# Create/Delete color schemes used by parking lots.

mysql_connect($mysql_server, $mysql_user, $mysql_password);
mysql_select_db($mysql_db_name);

$sql = "SELECT id, name FROM schemes";
$result = mysql_query($sql);

while($row = mysql_fetch_assoc($result)) {
	$schemes[$row["id"]] = $row;
}
?>
<style type="text/css">
	#red, #green, #blue {
		float: left;
		clear: left;
		width: 300px;
		margin: 15px;
	}
	#swatch {
		width: 120px;
		height: 100px;
		margin-top: 18px;
		margin-left: 350px;
		background-image: none;
	}
	#red .ui-slider-range { background: #ef2929; }
	#red .ui-slider-handle { border-color: #ef2929; }
	#green .ui-slider-range { background: #8ae234; }
	#green .ui-slider-handle { border-color: #8ae234; }
	#blue .ui-slider-range { background: #729fcf; }
	#blue .ui-slider-handle { border-color: #729fcf; }
</style>

<script type="text/javascript">
	$(document).ready(function() {
		$("#create_help_dialog").dialog({
			autoOpen: false,
			width: 600,
			show: "drop",
			hide: "drop",
		});
		$("#create_help_opener").hover(function() {
			$(this).toggleClass("ui-state-hover");
		});
		$("#create_help_opener").click(function() {
			$("#create_help_dialog").dialog("open");
			return false;
		});

		$("#delete_help_dialog").dialog({
			autoOpen: false,
			width: 600,
			show: "drop",
			hide: "drop",
		});
		$("#delete_help_opener").hover(function() {
			$(this).toggleClass("ui-state-hover");
		});
		$("#delete_help_opener").click(function() {
			$("#delete_help_dialog").dialog("open");
			return false;
		});
	});

	function hexFromRGB (r, g, b) {
		var hex = [
			r.toString(16),
			g.toString(16),
			b.toString(16)
		];
		$.each(hex, function (nr, val) {
			if(val.length == 1) {
				hex[nr] = '0' + val;
			}
		});
		return hex.join('').toUpperCase();
	}

	function refreshSwatch() {
		var red = $("#red").slider("value"),
			green = $("#green").slider("value"),
			blue = $("#blue").slider("value"),
			hex = hexFromRGB(red, green, blue);

		$("#swatch").css("background-color", "#" + hex);
		$("#create_line_color").val("#" + hex);
	}

	$(function() {
		$("#red, #green, #blue").slider({
			orientation: 'horizontal',
			range: "min",
			max: 255,
			value: 127,
			slide: refreshSwatch,
			change: refreshSwatch
		});
		$("#red").slider("value", 255);
		$("#green").slider("value", 140);
		$("#blue").slider("value", 60);
	});
</script>

<div id="tabs">
	<ul>
		<li><a href="#create_tab">Create Color Scheme</a></li>
		<li><a href="#delete_tab">Delete Color Schemes</a></li>
	</ul>

	<!-- Create Tab -->
	<div id="create_tab">
		<form id="create_scheme" name="create_scheme">
			<label for="create_name">Scheme Name</label>
			<input type="text" name="create_name"/><br/>

			<label for="create_line_width">Line Width</label>
			<input type="text" name="create_line_width" value="10"/><br/>

			<label for="create_line_opacity">Line Opacity</label>
			<input type="text" name="create_line_opacity" value="0.8"/><br/>

			<label for="create_fill_opacity">Fill Opacity</label>
			<input type="text" name="create_fill_opacity" value="0.3"/><br/>

			<p class="ui-state-default ui-corner-all ui-helper-clearfix" style="padding:4px;">
			<span class="ui-icon ui-icon-gear" style="float:left; margin:0 5px 0 0;"></span>
			Colorpicker</p>

			<div id="red"></div>
			<div id="green"></div>
			<div id="blue"></div>

			<div id="swatch" class="ui-widget-content ui-corner-all"></div>
			<input type="hidden" name="create_line_color" id="create_line_color"/>

			<input type="hidden" name="action" value="create"/><br/>
			<p><input type="submit" value="Create Color Scheme"/></p>
		</form>
		<a href="#" id="create_help_opener" class="ui-state-default ui-corner-all" style="padding: .4em 1em .4em 1.4em; text-decoration: none; position: relative;"><span class="ui-icon ui-icon-help" style="margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;"></span>Help</a>
		<div id="create_help_dialog" title="Create Color Scheme Help">
			<p>Create <strong>Color Schemes</strong> to make lots more easily identifiable.</p>
		</div>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<form id="delete_schemes">
			<select id="delete_scheme" name="delete_scheme" multiple="multiple">
				<optgroup label="Color Schemes">
				<?php
					foreach($schemes as $scheme) {
						echo "<option value=\"".$scheme["id"]."\">".$scheme["name"]."</option>\n";
					}
				?>
				</optgroup>
			</select>
			<input type="hidden" name="action" value="delete"/>
			<p><input type="submit" value="Delete Color Scheme"/></p>
		</form>
		<a href="#" id="delete_help_opener" class="ui-state-default ui-corner-all" style="padding: .4em 1em .4em 1.4em; text-decoration: none; position: relative;"><span class="ui-icon ui-icon-help" style="margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;"></span>Help</a>
		<div id="delete_help_dialog" title="Delete Color Scheme Help">
			<p>Select a <strong>Color Scheme</strong> to delete.</p>
		</div>
	</div>
</div>
