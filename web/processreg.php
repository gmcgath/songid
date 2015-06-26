<?php
/*	processreg.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when a new user tries to register.
   It has no HTML and always redirects.  */

require_once ('bin/config.php');
require_once ('bin/supportfuncs.php');
require_once ('bin/model/authcode.php');
require_once ('bin/model/user.php');
require_once ('bin/loggersetup.php');

	
try {
	$loginId = $_POST["user"];
	$pw = $_POST["pw"];
	$realName = trim(strip_tags($_POST["realname"]));
	$authcode = trim(strip_tags($_POST["authcode"]));
	
	// Check for valid fields
	if ($loginId == NULL || $pw == NULL || $realName == NULL || $authcode == NULL) {
		// Empty string is == to null, so we catch empty as well as missing values
		header ("Location: register.php?error=3", true, 302);
		return;
	}
	if (strlen($pw) < 8 || strlen($pw) > 24) {
		header ("Location: register.php?error=1", true, 302);
		return;
	}
	$regex = "%^[a-zA-Z0-9_]+$%";	// letters, digits, underscore
	if (!preg_match($regex, $loginId)) {
		header ("Location: register.php?error=4", true, 302);
		return;
	}
	if (!preg_match($regex, $pw)) {
		header ("Location: register.php?error=5", true, 302);
		return;
	}
	
	// Check authorization code
	if (!Authcode::verifyAuth($authcode)) {
		header ("Location: register.php?error=6", true, 302);
		return;
	}
	
	// Check if login name is already in use
	if (User::findByLoginId($loginId)) {
		header ("Location: register.php?error=2", true, 302);
		return;
	}
	
	// Create the user
	$user = new User;
	$user->loginId = $loginId;
	$user->name = $realName;
	$user->passwordHash = password_hash($pw, PASSWORD_DEFAULT);
	$user->insert();
	$GLOBALS["logger"]->debug("user id after creation is " . $user->id);
	$user->assignRole(User::ROLE_CONTRIBUTOR);
	$GLOBALS["logger"]->debug("returned from assignRole, redirecting");
	
	header ("Location: registerok.php", true, 302);
	return;
	
} catch (Exception $e) {
	$GLOBALS["logger"]->error($e->getMessage());
}
$GLOBALS["logger"]->info ("Registration error for $loginId");
header ("Location: register.php?error=-1", true, 302);	

?>
