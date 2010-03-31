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
		<form>
			<p class="ui-state-default ui-corner-all ui-helper-clearfix" style="padding:4px;">
			<span class="ui-icon ui-icon-pencil" style="float:left; margin:-2px 5px 0 0;"></span>
			Colorpicker
			</p>

			<div id="red"></div>
			<div id="green"></div>
			<div id="blue"></div>

			<div id="swatch" class="ui-widget-content ui-corner-all"></div>

			<input type="hidden" name="action" value="create"/>
		</form>
	</div>

	<!-- Delete Tab -->
	<div id="delete_tab">
	</div>
</div>
