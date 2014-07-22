<?php
/*	login.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
?>

<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Sign up</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>

<h1>Sign up</h1>
<?php
$err = $_GET["error"];
if (!is_null($err)) {
	switch ((int) $err) {
		case 1:
			$errmsg = "Password must be 8 to 24 characters long.";
			break;
		case 2:
			$errmsg = "That user name is already in use.";
			break;
		case 3:
			$errmsg = "All fields must be filled in.";
			break;
		case 4:
			$errmsg = "The user name may contain only letters, digits, and underscores.";
			break;
		case 5:
			$errmsg = "The password may contain only letters, digits, and underscores.";
			break;
		case 6:
			$errmsg = "Invalid authorization code.";
			break;
		default:
			$errmsg = "Invalid response.";
	}
}
	echo ("<p class='errormsg'>$errmsg</p>\n");
?>
<p>The user name and password may contain only letters, digits, and underscores.</p>
<div>
<form action="processreg.php" method="post">
<table class="logintab">
<tr>
<td class="formlabel">User name:</td>
<td><input type="text" name="user" class="loginbox" required autofocus>
</tr>
<tr>
<td class="formlabel">Your actual name:</td>
<td><input type="text" name="realname" class="loginbox" required>
</tr>
<tr>
<td class="formlabel">Password (8-24 characters):</td>
<td><input type="password" name="pw" class="loginbox" required>
</tr>
<tr>
<td class="formlabel">Authorization code:</td>
<td><input type="password" name="authcode" class="loginbox" required>
</tr>
<tr>
<td><input type="submit" class="submitbutton" value="Register"></td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</div>

</body>
</html>