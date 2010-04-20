<script type="text/javascript">
$(document).ready(function() {
	$("#passtext").show();
	$("#password").hide();

	$("#passtext").focus(function() {
		$(this).hide();
		$("#password").show();
		$("#password").focus();
	});

	$("#password").blur(function() {
		if($("#password").val() == "") {
			$("#passtext").show();
			$(this).hide();
		}
	});
});

function clear_text(field) {
	if(field.defaultValue == field.value) field.value = '';
	else if(field.value == '') field.value = field.defaultValue;
}
</script>

<li><a href="?page=register" title="<?php $title["register"]; ?>">Register</a></li>
<form name="login" id="login" method="POST" action="login-exec.php">
	<input name="email" id="email" type="text" value="E-Mail" onFocus="clear_text(this)" onBlur="clear_text(this)" />
	<input name="passtext" id="passtext" type="text" value="Password" autocomplete="off"/>
	<input name="password" id="password" type="password" value="" onFocus="clear_text(this)" onBlur="clear_text(this)" />
	<input type="submit" value="Log In" />
</form>
