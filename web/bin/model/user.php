<?php

/* user.php
   Implementation of the USERS table as a model
   Gary McGath
   July 16, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/../config.php');
require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../password.php');	// Required prior to PHP 5.5
require_once (dirname(__FILE__) . '/../loggersetup.php');


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
	var $roles;			// associative array with boolean values. Missing = false.
	
	/* Check the login credentials. Return a User object if good,
	   pw must NOT be escaped.
	   NULL otherwise. */
	public static function verifyLogin(mysqli $mysqli, $username, $pw) {
		$usr = sqlPrep($username);
		$selstmt = "SELECT ID, PASSWORD_HASH, NAME FROM USERS WHERE LOGIN_ID = $usr";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			$GLOBALS["logger"]->error("Error getting Users: " . $mysqli->connect_error);
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
				$user->roles = $user->getRoles($mysqli);
				return $user;
			}
		}
		return NULL;
	}
	
	/* Retrieve a row with a given login name, not checking the password */
	public static function findByLoginId (mysqli $mysqli, $username) {
		$usr = sqlPrep($username);
		$selstmt = "SELECT ID, PASSWORD_HASH, NAME FROM USERS WHERE LOGIN_ID = $usr";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			$GLOBALS["logger"]->error("Error getting Users: " . $mysqli->connect_error);
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
			$user->roles = $user->getRoles($mysqli);
			return $user;
		}
		return NULL;
	}
	
	/* Retrieve a row with a given ID */
	public static function findById (mysqli $mysqli, $id) {
		$selstmt = "SELECT LOGIN_ID, PASSWORD_HASH, NAME FROM USERS WHERE ID = $id";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			$GLOBALS["logger"]->error("Error getting Users: " . $mysqli->connect_error);
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
			$user->roles = $user->getRoles($mysqli);
			return $user;
		}
		return NULL;
	}
	
	/* Get all the users. */
	public static function getAllUsers(mysqli $mysqli) {
		$selstmt = "SELECT ID, LOGIN_ID, NAME FROM USERS";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			$GLOBALS["logger"]->error("Connection error getting Users: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		$rows = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row)) {
					break;
				}
				$user = new User();
				$user->id = $row[0];
				$user->loginId = $row[1];
				$user->name = $row[2];
				$user->roles = $user->getRoles($mysqli);
				$rows[] = $user;
			}
		}
		return $rows;	
	}
	
	/* Assign a role to a user. */
	public function assignRole (mysqli $mysqli, $role) {
		// Check if the role is already assigned
		if ($this->hasRole ($role)) {
			return;			// nothing to do
		}
		
		$insstmt = "INSERT INTO USERS_ROLES (USER_ID, ROLE) VALUES " .
				"({$this->id}, $role)";
		$res = $mysqli->query($insstmt);
		if ($mysqli->connect_errno) {
			$GLOBALS["logger"]->error("Error getting User role: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		$this->roles[$role] = true;
	}
	
	/* Remove a role from a user. */
	public function removeRole (mysqli $mysqli, $role) {
		if (!$this->hasRole ($role)) {
			return;			// nothing to do
		}
		$delstmt = "DELETE FROM USERS_ROLES WHERE USER_ID = '" .
			$this->id .
			"' AND ROLE = '" .
			$role .
			"'";
		$res = $mysqli->query($delstmt);
		if ($mysqli->connect_errno) {
			$GLOBALS["logger"]->error("Error getting User role: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		$this->roles[$role] = false;
	}
	
	
	/* Return true if a user has a specified role, otherwise false. */
	public function hasRole ($role) {
		// $this->roles is an associative array. If a value is missing, treat it as false.
		$val = $this->roles[$role];
		if (is_null ($val))
			$val = false;
		return $val;
	}
	
	/* Return an array of all roles belonging to a user. */
	private function getRoles(mysqli $mysqli) {
		$selstmt = "SELECT ROLE FROM USERS_ROLES WHERE USER_ID = {$this->id} ";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			$GLOBALS["logger"]->error("Connect error getting User roles: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		$retval = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row)) {
					break;
				}
				$retval[intval($row[0])] = true;
			}
			return $retval;
		}
		$GLOBALS["logger"]->error("Error in getRoles: " . $mysqli->error);
		return false;
	}
	
	/* Inserts a User into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert (mysqli $mysqli) {
		$lgn = sqlPrep($this->loginId);
		$pwh = sqlPrep($this->passwordHash);
		$nm = sqlPrep($this->name);
		$insstmt = "INSERT INTO USERS (LOGIN_ID, PASSWORD_HASH, NAME) VALUES ($lgn, $pwh, $nm)";
		$res = $mysqli->query ($insstmt);
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			return $this->id;
		}
		$GLOBALS["logger"]->error ("Error inserting User: " . $mysqli->error);
		throw new Exception ("Could not add User {$this->loginId} to database");
	}
}
?>