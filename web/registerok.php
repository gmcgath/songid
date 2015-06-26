<?php
/*
   registerok.php
   
   July 20, 2014
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
header("Content-type: text/html; charset=utf-8");

include_once('bin/model/user.php');

?>

<html>
<head>
	<meta charset="utf-8" />
	<link href="css/styles.css" rel="stylesheet">
	<title>Successful registration</title>
</head>
<body>



<p>You have successfully registered. You may now <a href="login.php">log in</a>.</p>
<?php

if (array_key_exists ("login", $_GET)) {
	$login = $_GET["login"];
}
else {
	$login = "";
}
if (array_key_exists ("name", $_GET)) {
	$userName = $_GET["name"];
}
else {
	$userName = "";
}

echo ('<p>Login ID: ' . $login . "</p>\n");
echo ('<p>Your name: ' . $userName . "</p>\n");
echo ("</ul>\n");
?>

</body>
</html>

