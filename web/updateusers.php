<?php
/*	updateusers.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the user admin form is submitted. 
   It has no HTML and always redirects.  */

require_once ('bin/config.php');
require_once ('bin/supportfuncs.php');
require_once ('bin/model/user.php');
require_once ('bin/loggersetup.php');

session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;

$selfUser = $_SESSION['user'];
if (!($selfUser->hasRole(User::ROLE_SUPERUSER))) {
	header ("Location: norole.php", true, 302);
	return;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header ("Location: 405.html", true, 405);
	return;
}


/* Find all the users, keying off the hidden fields userxx */
$userids = array();
while ( list( $param, $val ) = each( $_POST ) ) {
	if (substr($param, 0, 4) == "user") {
		//$GLOBALS["logger"]->debug("updateusers: Got parameter " . $param);
		$userids[] = intval(substr($param, 4));
	}
}

// Count the number of users changed for reporting
$usersChanged = 0;

foreach ($userids as $userid) {
	$u = User::findById($userid);
	if (!is_null($u)) {
		$changed = false;
		$supru = 'supr' . $userid;		// superuser element name in form
		$admiu = 'admi' . $userid;		// administrator
		$contu = 'cont' . $userid;		// contributor
		$staku = 'stak' . $userid;		// stakeholder
		$actvu = 'actv' . $userid;		// activated checkbox
		$superChecked = array_key_exists($supru, $_POST) && !is_null($_POST[$supru]);
		$admiChecked = array_key_exists($admiu, $_POST) && !is_null($_POST[$admiu]);
		$contribChecked = array_key_exists($contu, $_POST) && !is_null($_POST[$contu]);
		$stakeholderChecked = array_key_exists($staku, $_POST) && !is_null($_POST[$staku]);
		$activateChecked = array_key_exists($actvu, $_POST) && !is_null($_POST[$actvu]);
		$GLOBALS["logger"]->info ("activateChecked = " . $activateChecked . " for user " . $userid);
		
		// Handle addition of roles
		if ($superChecked && !$u->hasRole(User::ROLE_SUPERUSER)) {
			$changed = true;
			$u->assignRole(User::ROLE_SUPERUSER);
		}
		if ($admiChecked && !$u->hasRole(User::ROLE_ADMIN)) {
			$changed = true;
			$u->assignRole(User::ROLE_ADMIN);
		}
		if ($contribChecked && !$u->hasRole(User::ROLE_CONTRIBUTOR)) {
			$changed = true;
			$u->assignRole(User::ROLE_CONTRIBUTOR);
		}
		if ($stakeholderChecked && !$u->hasRole(User::ROLE_STAKEHOLDER)) {
			$changed = true;
			$GLOBALS["logger"]->info ("Adding Stakeholder role for " . $u->loginId);
			$u->assignRole(User::ROLE_STAKEHOLDER);
		}
		
		// Handle removal of roles
		if (!$superChecked && $u->hasRole(User::ROLE_SUPERUSER)) {
			$changed = true;
			$u->removeRole(User::ROLE_SUPERUSER);
		}
		if (!$admiChecked && $u->hasRole(User::ROLE_ADMIN)) {
			$changed = true;
			$u->removeRole(User::ROLE_ADMIN);
		}
		if (!$contribChecked && $u->hasRole(User::ROLE_CONTRIBUTOR)) {
			$changed = true;
			$u->removeRole(User::ROLE_CONTRIBUTOR);
		}
		if (!$stakeholderChecked && $u->hasRole(User::ROLE_STAKEHOLDER)) {
			$changed = true;
			$u->removeRole(User::ROLE_STAKEHOLDER);
		}
		
		// Handle activation (we currently don't do deactivation)
		if ($activateChecked) {
			$changed = true;
			$GLOBALS["logger"]->info ("Activating " . $u->loginId);
			$u->activate();
			
		}
		
		if ($changed)
			$usersChanged++;
	}
}
$GLOBALS["logger"]->info ("Users that would have been changed: " . $usersChanged);
header ("Location: adminok.php?nusers=$usersChanged", true, 302);
?>