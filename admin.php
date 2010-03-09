<script type="text/javascript">
function check_form() {
	if(document.admin.fname.value == '') {
		alert('First name cannot be blank.');
		return false;
	}
	else if(document.admin.lname.value == '') {
		alert('Last name cannot be blank.');
		return false;
	}
	else if(document.admin.email.value == '') {
		alert('E-mail cannot be blank.');
		return false;
	}
	else if(document.admin.pass1.value == '') {
		alert('Password cannot be blank.');
		return false;
	}
	else if(document.admin.pass2.value != document.admin.pass1.value) {
		alert('Passwords do not match.');
		return false;
	}
	return true;
}
</script>

<form name="admin" id="admin" method="POST" action="?page=" onSubmit="">
	<label for="fname">First Name</label>
	<input id="fname" name="fname" type="text"/>
	<br />

	<label for="lname">Last Name</label>
	<input id="lname" name="lname" type="text"/>
	<br />

	<label for="email">E-Mail</label>
	<input id="email" name="email" type="text"/>
	<br />

	<label for="pass1">Password</label>
	<input id="pass1" name="pass1" type="password"/>
	<br />

	<label for="pass2">Confirm Password</label>
	<input id="pass2" name="pass2" type="password"/>
	<br />

	<label for="passtype">Pass Type</label>
	<select id="passtype" name="passtype">
		<?php
			//foreach() {
				echo "<option value=\"id\">name</option>";
			//}
		?>
	</select>
	<br />

	<input type="submit" value="Create" />
	<small>All fields required.</small>
</form>
