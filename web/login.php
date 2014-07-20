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
	<title>Log in</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>


<h1>Log in</h1>
<?php
if (!is_null($_GET["error"]))
	echo ("<p class='errormsg'>Login error.</p>\n");
?>
<div>
<form action="processlogin.php" method="post">
<table class="logintab">
<tr>
<td class="loginlabel">User:</td>
<td><input type="text" name="user" class="loginbox" required>
</tr>
<tr>
<td class="loginlabel">Password:</td>
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