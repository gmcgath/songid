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

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header ("Location: 405.html", true, 405);
	return;
}


/* Find all the users, keying off the hidden fields userxx */
$userids = array();
while ( list( $param, $val ) = each( $_POST ) ) {
	if (substr($param, 0, 4) == "user") {
		$GLOBALS["logger"]->debug("updateusers: Got parameter " . $param);
		$userids[] = intval(substr($param, 4));
	}
}

// Count the number of users changed for reporting
$usersChanged = 0;

foreach ($userids as $userid) {
	$u = User::findById($userid);
	if (!is_null($u)) {
		$changed = false;
		$admnu = 'admn' . $userid;
		$editu = 'edit' . $userid;
		$contu = 'cont' . $userid;
		$adminChecked = array_key_exists($admnu, $_POST) && !is_null($_POST[$admnu]);
		$editorChecked = array_key_exists($editu, $_POST) && !is_null($_POST[$editu]);
		$contribChecked = array_key_exists($contu, $_POST) && !is_null($_POST[$contu]);
		
		// Handle addition of roles
		if ($adminChecked && !$u->hasRole(User::ROLE_ADMINISTRATOR)) {
			$changed = true;
			$GLOBALS["logger"]->info ("Adding Administrator role for " . $u->loginId);
			$u->assignRole(User::ROLE_ADMINISTRATOR);
		}
		if ($editorChecked && !$u->hasRole(User::ROLE_EDITOR)) {
			$changed = true;
			$GLOBALS["logger"]->info ("Adding Editor role for " . $u->loginId);
			$u->assignRole(User::ROLE_EDITOR);
		}
		if ($contribChecked && !$u->hasRole(User::ROLE_CONTRIBUTOR)) {
			$changed = true;
			$GLOBALS["logger"]->info ("Adding Contributor role for " . $u->loginId);
			$u->assignRole(User::ROLE_CONTRIBUTOR);
		}
		
		// Handle removal of roles
		if (!$adminChecked && $u->hasRole(User::ROLE_ADMINISTRATOR)) {
			$changed = true;
			$GLOBALS["logger"]->info ("Would remove Administrator role for " . $u->loginId);
			$u->removeRole(User::ROLE_ADMINISTRATOR);
		}
		if (!$editorChecked && $u->hasRole(User::ROLE_EDITOR)) {
			$changed = true;
			$GLOBALS["logger"]->info ("Removing Editor role for " . $u->loginId);
			$u->removeRole(User::ROLE_EDITOR);
		}
		if (!$contribChecked && $u->hasRole(User::ROLE_CONTRIBUTOR)) {
			$changed = true;
			$GLOBALS["logger"]->info ("Removing Contributor role for " . $u->loginId);
			$u->removeRole(User::ROLE_CONTRIBUTOR);
		}
		
		if ($changed)
			$usersChanged++;
	}
}
$GLOBALS["logger"]->info ("Users that would have been changed: " . $usersChanged);
header ("Location: adminok.php?nusers=$usersChanged", true, 302);
?>