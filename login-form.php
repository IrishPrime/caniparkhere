<li><a href="?page=register" title="<?php $title["register"]; ?>">Register</a></li>
<form name="login" id="login" method="POST" action="login-exec.php">
	<input name="email" id="email" type="text" value="E-Mail" onFocus="clear_text(this)" onBlur="clear_text(this)" />
	<input name="password" id="password" type="password" value="Password" onFocus="clear_text(this)" onBlur="clear_text(this)" />
	<input type="submit" value="Log In" />
</form>
