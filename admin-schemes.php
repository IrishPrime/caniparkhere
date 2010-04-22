<?php
# Create/Delete color schemes used by parking lots.

echo "<div class=\"ui-widget\">\n";
switch($_POST["action"]) {
	case "create":
		$result = @CreateScheme($_POST["create_name"], $_POST["create_line_color"], $_POST["create_line_width"], $_POST["create_line_opacity"], $_POST["create_fill_color"], $_POST["create_fill_opacity"]);

		echo ($result != null) ? $ui_info : $ui_alert;
		echo "\t\tCreating Color Scheme: <strong>".$_POST["create_name"]."</strong>\n\t</div>\n";
		break;
	case "delete":
		$result = @DeleteSchemes($_POST["delete_scheme"]);

		echo $results > 0 ? $ui_info : $ui_alert;
		echo "\t\tRules Deleted: <strong>".$results or "0"."</strong>\n\t</div>\n";
		break;
	default:
		break;
}
echo "</div>\n";

$schemes = GetSchemes();
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

<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/validate/jquery.validate.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#create_form").validate({
			rules: {
				create_name: {
					required: true,
					minlength: 1,
				},
				create_line_width: {
					required: true,
					range: [1, 30],
				},
				create_line_opacity: {
					required: true,
					range: [0, 1],
				},
				create_fill_opacity: {
					required: true,
					range: [0, 1],
				},
			}
		});
		$("#delete_form").validate({
			rules: {
			}
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
			hex2 = hexFromRGB(Math.floor(red * 0.9), Math.floor(green * 0.9), Math.floor(blue * 0.9));

		$("#swatch").css("background-color", "#" + hex);
		$("#create_line_color").val("#" + hex);
		$("#create_fill_color").val("#" + hex2);
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
		<form id="create_form" name="create" method="POST" action="">
			<label for="create_name">Scheme Name</label>
			<input type="text" name="create_name" class="required" minlength="1"/><br/>

			<label for="create_line_width">Line Width</label>
			<input type="text" name="create_line_width" value="10" class="required"/><br/>

			<label for="create_line_opacity">Line Opacity</label>
			<input type="text" name="create_line_opacity" value="0.8" class="required"/><br/>

			<label for="create_fill_opacity">Fill Opacity</label>
			<input type="text" name="create_fill_opacity" value="0.3" class="required"/><br/>

			<p class="ui-state-default ui-corner-all ui-helper-clearfix" style="padding:4px;">
			<span class="ui-icon ui-icon-gear" style="position:relative; float:left; margin:0 5px 0 0;"></span>
			Line &amp; Fill Color</p>

			<div id="red"></div>
			<div id="green"></div>
			<div id="blue"></div>

			<div id="swatch" class="ui-widget-content ui-corner-all"></div>
			<input type="hidden" name="create_line_color" id="create_line_color"/>
			<input type="hidden" name="create_fill_color" id="create_fill_color"/>

			<input type="hidden" name="action" value="create"/><br/>
			<p><input type="submit" value="Create Color Scheme"/></p>
		</form>
		<?php echo $ui_help_create; ?>
		<div id="create_help_dialog" title="Create Color Scheme Help">
			<p>Create <strong>Color Schemes</strong> to make lots more easily identifiable.</p>
			<p>The <strong>Scheme Name</strong> should be unique.</strong>
			<p><strong>Line Width</strong> specifies the width of the border around a parking lot in any map view.</p>
			<p><strong>Line Opacity</strong> specifies the opacity of the border around a parking lot in any map view. A value of 1 creates an opaque border.</p>
			<p><strong>Fill Opacity</strong> specifies the opacity of the area within the polygon which defines a parking lot. A value of 1 creates an opaque fill (not recommended).</p>
			<p>Use the <span style="color: #EF2929;">Red</span>, <span style="color: #8AE234;">Green</span>, and <span style="color: #729FCF;">Blue</span>, sliders to choose a color for the scheme. The line color will be equal to the color selected while the fill color will be set to 90% of the color selected.</p>
		</div>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
		<form id="delete_form" name="delete" method="POST" action="">
			<select id="delete_scheme" name="delete_scheme[]" multiple="multiple">
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
		<?php echo $ui_help_delete; ?>
		<div id="delete_help_dialog" title="Delete Color Scheme Help">
			<p>Select <strong>Color Schemes</strong> to delete.</p>
		</div>
	</div>
</div>
