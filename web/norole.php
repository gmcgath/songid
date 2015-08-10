<?php
/*
   norole.php
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once ('bin/supportfuncs.php');
require_once('bin/model/user.php');

session_start();
require_once('bin/sessioncheck.php');

if (!sessioncheck())
	return;	

$user = $_SESSION['user'];

// If user hasn't been activated, redirect
if ( $user->activated == 0 ) {
	header( "Location: inactiveuser.php", true, 302);
	return;
}

header("Content-type: text/html; charset=utf-8");

?>

<html>
<head>
	<meta charset="utf-8" />
	<link href="css/styles.css" rel="stylesheet">
	<title>Not allowed</title>
</head>
<body>
<?php
include ('menubar.php');
?>
<p>You aren't allowed to do that from your account.</p>


</body>
</html>
