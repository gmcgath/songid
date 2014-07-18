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
include_once (dirname(__FILE__) . '/../password.php');	// Required prior to PHP 5.5

class User {
	var $id;
	var $loginId;
	var $email;
	var $passwordHash;
	
	/* Check the login credentials. Return a User object if good,
	   pw must NOT be escaped.
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
		return NULL;
	}
	
	/* Retrieve a row with a given username, not checking the password */
	public static function findByLoginId ($mysqli, $username) {
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
				return NULL;
			$user = new User();
			$user->id = $row[0];
			$user->loginId = $username;
			$user->passwordHash = $row[1];
			$user->email = $row[2];
			return $user;
		}
		return NULL;
	}
	
	
	/* Inserts a User into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		$lgn = sqlPrep($this->loginId);
		$pwh = sqlPrep($this->passwordHash);
		$eml = sqlPrep($this->email);
		$insstmt = "INSERT INTO USERS (LOGIN_ID, PASSWORD_HASH, EMAIL) VALUES ($lgn, $pwh, $eml)";
		$res = $mysqli->query ($insstmt);
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			return $this->id;
		}
		error_log ("Error inserting User: " . $mysqli->error);
		throw new Exception ("Could not add User {$this->loginId} to database");
	}
}
?>