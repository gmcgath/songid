<?php

/* user.php
   Implementation of the USERS table as a model
   Gary McGath
   July 16, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once (dirname(__FILE__) . '/../config.php');
include_once (dirname(__FILE__) . '/../supportfuncs.php');
include_once (dirname(__FILE__) . '/../password.php');	// Required prior to PHP 5.5

class User {

	/* Definitions of user roles */
	const ROLE_CONTRIBUTOR = 1;
	const ROLE_EDITOR = 2;
	const ROLE_ADMINISTRATOR = 3;
	
	var $id;
	var $loginId;
	var $name;
	var $passwordHash;
	var $dateRegistered;
	
	/* Check the login credentials. Return a User object if good,
	   pw must NOT be escaped.
	   NULL otherwise. */
	public static function verifyLogin($mysqli, $username, $pw) {
		$usr = sqlPrep($username);
		$selstmt = "SELECT ID, PASSWORD_HASH, NAME FROM USERS WHERE LOGIN_ID = $usr";
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
				$user->name = $row[2];
				return $user;
			}
		}
		return NULL;
	}
	
	/* Retrieve a row with a given login name, not checking the password */
	public static function findByLoginId ($mysqli, $username) {
		$usr = sqlPrep($username);
		$selstmt = "SELECT ID, PASSWORD_HASH, NAME FROM USERS WHERE LOGIN_ID = $usr";
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
			$user->name = $row[2];
			return $user;
		}
		return NULL;
	}
	
	/* Retrieve a row with a given ID */
	public static function findById ($mysqli, $id) {
		$selstmt = "SELECT LOGIN_ID, PASSWORD_HASH, NAME FROM USERS WHERE ID = $id";
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
			$user->id = $id;
			$user->loginId = $row[0];
			$user->passwordHash = $row[1];
			$user->name = $row[2];
			return $user;
		}
		return NULL;
	}
	
	/* Assign a role to a user. */
	public function assignRole ($mysqli, $role) {
		// Check for duplicates
		if ($this->hasRole ($mysqli, $role)) {
			return;			// nothing to do
		}
		
		$insstmt = "INSERT INTO USERS_ROLES (USER_ID, ROLE) VALUES " .
				"({$this->id}, $role)";
		$res = $mysqli->query($insstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting User role: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
	}
		
		/* Return true if a user has a specified role. */
	public function hasRole ($mysqli, $role) {
		$cntstmt = "SELECT COUNT(*) FROM USERS_ROLES WHERE USER_ID = {$this->id} " .
					"AND ROLE = $role";
		error_log ("hasRole: $cntstmt");
		$res = $mysqli->query($cntstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting User role: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			$row = $res->fetch_row();
			if (!is_null($row)) {
				$count = (int) $row[0];
				if ($count > 0)
					return true;
			}
		}
		return false;
	}
	
	
	
	/* Inserts a User into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		$lgn = sqlPrep($this->loginId);
		$pwh = sqlPrep($this->passwordHash);
		$nm = sqlPrep($this->name);
		$insstmt = "INSERT INTO USERS (LOGIN_ID, PASSWORD_HASH, NAME) VALUES ($lgn, $pwh, $nm)";
		error_log($insstmt);
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