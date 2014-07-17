<?php
/*	processlogin.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the user tries to log in.
   It has no HTML and always redirects.  */

include_once ('bin/config.php');
include_once ('bin/supportfuncs.php');
include_once ('bin/model/user.php');

/* Open the database */
$mysqli = opendb();
	
try {
	$userName = $mysqli->real_escape_string($_POST["user"]);
	$pw = $mysqli->real_escape_string($_POST["pw"]);
	if (User::verifyLogin($mysqli, $userName, $pw)) {
		header ("Location: cliplist.php", true, 302);
	}
} catch (Exception $e) {
	error_log($e->getMessage());
}
header ("Location: login.php?error=1", true, 302);	// Should add an error message to login.php

?>
