<?php
/* registeruser.php 

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   This is a standalone command-line application as a temporary measure.
*/

	require ('config.php');
	require ('password.php');		// Compatibility file needed till we get PHP 5.5
	require ('supportfuncs.php');
	require ('model/user.php');
	
	/* Open the database */
	$mysqli = opendb();

	
	echo ("User name: ");
	$uname = trim(fgets(STDIN));
	echo ("Email: ");
	$email = trim(fgets(STDIN));
	echo ("Password: ");
	$pw = trim(fgets(STDIN));
	$pwHash = password_hash($pw, PASSWORD_DEFAULT);

	// Check if user already exists
	$user = User::findByLoginId($mysqli, $uname);
	if (!is_null($user)) {
		echo ("User already exists.\n");
		exit;
	}
	$user = new User;
	$user->loginId = $uname;
	$user->email = $email;
	$user->passwordHash = $pwHash;
	echo ("Hash is " . $pwHash . "\n");
	if (password_verify($pw, $pwHash))
		echo ("Hash verified.\n");
	else {
		echo ("Hash failed to verify. Exiting.\n");
		exit;
	}
	try {
		$user->insert($mysqli);
		echo ("Successfully added user " . $uname);
	} catch (Exception $ex) {
		echo ($ex->getMessage());
	}

?>