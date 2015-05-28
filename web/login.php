<?php
/*	login.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
header("Content-type: text/html; charset=utf-8");
?>

<html lang="en">
<head>
	<title>Log in</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>


<h1>Log in</h1>
<?php
if (array_key_exists("error", $_GET))
	echo ("<p class='errormsg'>Login error.</p>\n");
?>
<div>
<form action="processlogin.php" method="post" accept-charset="UTF-8">
<table class="logintab">
<tr>
<td class="formlabel">User:</td>
<td><input type="text" name="user" class="loginbox" required autofocus>
</tr>
<tr>
<td class="formlabel">Password:</td>
<td><input type="password" name="pw" class="loginbox" required>
</tr>
<tr>
<td><input type="submit" class="submitbutton" value="Log in"></td>
<td>&nbsp;</td>
</tr>
</table>
</form>
</div>
<hr>
<p><a href="register.php">Sign up</a></p>

</body>
</html>