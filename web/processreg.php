<?php
/*	processreg.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when a new user tries to register.
   It has no HTML and always redirects.  */

include_once ('bin/config.php');
include_once ('bin/supportfuncs.php');
include_once ('bin/model/authcode.php');
include_once ('bin/model/user.php');

/* Open the database */
$mysqli = opendb();
	
try {
	$userName = $_POST["user"];
	$pw = $_POST["pw"];
	$realname = $mysqli->real_escape_string($_POST["realname"]);
	$authcode = $mysqli->real_escape_string($_POST["authcode"]);
	
	error_log ("userName = $userName");
	error_log ("pw = $pw");
	error_log ("realname = $realname");
	error_log ("authcode = $authcode");
	
	// Check for valid fields
	if ($userName == NULL || $pw == NULL || $realname == NULL || $authcode == NULL) {
		// Empty string is == to null, so we catch empty as well as missing values
		header ("Location: register.php?error=3", true, 302);
		return;
	}
	if (strlen($pw) < 8 || strlen($pw) > 24) {
		header ("Location: register.php?error=1", true, 302);
		return;
	}
	$regex = "%^[a-zA-Z0-9_]+$%";	// letters, digits, underscore
	if (!preg_match($regex, $userName)) {
		header ("Location: register.php?error=4", true, 302);
		return;
	}
	if (!preg_match($regex, $pw)) {
		header ("Location: register.php?error=5", true, 302);
		return;
	}
	
	// Check authorization code
	if (!Authcode::verifyAuth($mysqli, $authcode)) {
		header ("Location: register.php?error=6", true, 302);
		return;
	}
	
	// TODO create user
} catch (Exception $e) {
	error_log($e->getMessage());
}
error_log ("Registration error for $userName");
header ("Location: register.php?error=-1", true, 302);	

?>
