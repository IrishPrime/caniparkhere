$(document).ready(function() {
	// Tabs
	$("#tabs").tabs({ fx: { opacity: "toggle", duration: "fast" } });

	// Accordions
	$("#accordion").accordion({
		header: "h1",
		autoHeight: false,
		collapsible: true,
		active: false,
	});
	$("#nested_accordion").accordion({
		header: "h2",
		autoHeight: false,
		collapsible: true,
		active: false,
	});

	// Buttons
	$("input:submit, input:button, input:checkbox, button").button();

	// Help Dialogs
	$("#create_help_dialog").dialog({
		autoOpen: false,
		width: 600,
		show: "drop",
		hide: "drop",
	});
	$("#create_help_opener").hover(function() {
		$(this).toggleClass("ui-state-hover");
		return true;
	});
	$("#create_help_opener").click(function() {
		$("#create_help_dialog").dialog("open");
	});

	$("#delete_help_dialog").dialog({
		autoOpen: false,
		width: 600,
		show: "drop",
		hide: "drop",
	});
	$("#delete_help_opener").hover(function() {
		$(this).toggleClass("ui-state-hover");
		return false;
	});
	$("#delete_help_opener").click(function() {
		$("#delete_help_dialog").dialog("open");
	});

	$("#modify_help_dialog").dialog({
		autoOpen: false,
		width: 600,
		show: "drop",
		hide: "drop",
	});
	$("#modify_help_opener").hover(function() {
		$(this).toggleClass("ui-state-hover");
		return false;
	});
	$("#modify_help_opener").click(function() {
		$("#modify_help_dialog").dialog("open");
	});
});

function clear_text(field) {
	if(field.defaultValue == field.value) field.value = '';
	else if(field.value == '') field.value = field.defaultValue;
}
