<?php
/* makeauthcode.php 

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   This is a standalone command-line application for creating authorization codes
   as a temporary measure.
*/

	require_once ('config.php');
	require_once ('password.php');		// Compatibility file needed till we get PHP 5.5
	require_once ('supportfuncs.php');
	require_once ('model/authcode.php');
	
	/* Open the database */
	$mysqli = opendb();

	echo ("Auth code: ");
	$authc = trim(fgets(STDIN));
	$acHash = password_hash($authc, PASSWORD_DEFAULT);

	$authcode = new Authcode;
	$authcode->hash = $acHash;
	try {
		$authcode->insert($mysqli);
		echo ("Successfully created authcode.");
	} catch (Exception $ex) {
		echo ($ex->getMessage());
	}

?>