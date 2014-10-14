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

session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header ("Location: 405.html", true, 405);
	return;
}

/* Open the database */
$mysqli = opendb();

/* Find all the users, keying off the hidden fields userxx */
$userids = array();
while ( list( $param, $val ) = each( $_POST ) ) {
	error_log ("Examining POST param " . $param . "   value " . $val);
	if (substr($param, 0, 4) == "user") {
		error_log("updateusers: Got parameter " . $param);
		$userids[] = intval(substr($param, 4));
	}
}

// Count the number of users changed for reporting
$usersChanged = 0;

foreach ($userids as $userid) {
	$u = User::findById($mysqli, $userid);
	if (!is_null($u)) {
		$changed = false;
		$adminChecked = !is_null($_POST['admn' . $userid]);
		$editorChecked = !is_null($_POST['edit' . $userid]);
		$contribChecked = !is_null($_POST['cont' . $userid]);
		
		// Handle addition of roles
		if ($adminChecked && !$u->hasRole(User::ROLE_ADMINISTRATOR)) {
			$changed = true;
			error_log ("Adding Administrator role for " . $u->loginId);
			$u->assignRole($mysqli, User::ROLE_ADMINISTRATOR);
		}
		if ($editorChecked && !$u->hasRole(User::ROLE_EDITOR)) {
			$changed = true;
			error_log ("Adding Editor role for " . $u->loginId);
			$u->assignRole($mysqli, User::ROLE_EDITOR);
		}
		if ($contribChecked && !$u->hasRole(User::ROLE_CONTRIBUTOR)) {
			$changed = true;
			error_log ("Adding Contributor role for " . $u->loginId);
			$u->assignRole($mysqli, User::ROLE_CONTRIBUTOR);
		}
		
		// Handle removal of roles
		if (!$adminChecked && $u->hasRole(User::ROLE_ADMINISTRATOR)) {
			$changed = true;
			error_log ("Would remove Administrator role for " . $u->loginId);
		}
		if (!$editorChecked && $u->hasRole(User::ROLE_EDITOR)) {
			$changed = true;
			error_log ("Removing Editor role for " . $u->loginId);
			$u->removeRole($mysqli, User::ROLE_EDITOR);
		}
		if (!$contribChecked && $u->hasRole(User::ROLE_CONTRIBUTOR)) {
			$changed = true;
			error_log ("Removing Contributor role for " . $u->loginId);
			$u->removeRole($mysqli, User::ROLE_CONTRIBUTOR);
		}
		
		if ($changed)
			$usersChanged++;
	}
}
error_log ("Users that would have been changed: " . $usersChanged);
header ("Location: adminok.php?nusers=$usersChanged", true, 302);
?>