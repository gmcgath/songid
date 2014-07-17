<?php

/* user.php
   Implementation of the USERS table as a model
   Gary McGath
   July 16, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once (dirname(__FILE__) . '/../supportfuncs.php');

class User {
	var $id;
	var $loginId;
	var $email;
	
	/* Check the login credentials. Return a User object if good,
	   NULL otherwise. */
	public static function verifyLogin($mysqli, $username, $pw) {
		$usr = sqlPrep($username);
		$selstmt = "SELECT ID, PASSWORD_HASH, EMAIL FROM USERS WHERE LOGIN_ID = $usr";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting Users: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			$row = $res->fetch_row();
			if (is_null($row))
				return NULL;				// no matching user
			if (password_verify($pw, $row[1])) {
				$user = new User();
				$user->id = $row[0];
				$user->loginId = $username;
				$user->email = $row[2];
				return $user;
			}
		}
	}
}
?>