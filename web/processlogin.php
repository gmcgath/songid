<?php
/*	processlogin.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the user tries to log in.
   It has no HTML and always redirects.  */

require_once ('bin/config.php');
require_once ('bin/supportfuncs.php');
require_once ('bin/model/user.php');
require_once ('bin/loggersetup.php');

/* Open the database */
$mysqli = opendb();
	
try {
	$userName = trim(strip_tags($mysqli->real_escape_string($_POST["user"])));
	$user = User::verifyLogin($mysqli, $userName, $_POST["pw"]);
	if ($user) {
		session_start();
		$_SESSION['user'] = $user;
		$GLOBALS["logger"]->debug("User login ID = $user->loginId");
		$_SESSION['report'] = NULL;
		header ("Location: cliplist.php", true, 302);
		return;
	}
} catch (Exception $e) {
	$GLOBALS["logger"]->error($e->getMessage());
}
$GLOBALS["logger"]->info ("Login error for $userName");
header ("Location: login.php?error=1", true, 302);	// Should add an error message to login.php

?>
