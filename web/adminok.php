<?php
/*
   adminok.php
   
   October 14, 2014
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
header("Content-type: text/html; charset=utf-8");

include_once('bin/config.php');
include_once('bin/model/user.php');
include_once('bin/supportfuncs.php');
session_start();
include_once('bin/sessioncheck.php');
if (!sessioncheck())
	return;

?>

<html>
<head>
	<meta charset="utf-8" />
	<link href="css/styles.css" rel="stylesheet">
	<title>Users modified</title>
</head>
<body>
<?php
include ('menubar.php');
?>
<p class="notice">
<?php
$nu = $_GET['nusers'];
if (!ctype_digit ($nu))
	$nu = 0;		// defeat dirty tricks
if ($nu == 0)
	echo ("No changes made.");
else if ($nu == 1)
	echo ("1 user modified.");
else
	echo("" . $nu . " users modified.");
?>
</p>

</body>
</html>

