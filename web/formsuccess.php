<?php
/*
   formsuccess.php
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

header("Content-type: text/html; charset=utf-8");

	require_once ('bin/supportfuncs.php');
	require_once('bin/model/user.php');

	session_start();
?>

<html>
<head>
	<meta charset="utf-8" />
	<link href="css/styles.css" rel="stylesheet">
	<title>Successful submission</title>
</head>
<body>
<?php
include ('menubar.php');
?>
<p>Thank you!</p>


</body>
</html>
