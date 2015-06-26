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
require_once (dirname(__FILE__) . '/../initorm.php');

class User {

	/* Definitions of user roles */
	const ROLE_CONTRIBUTOR = 1;
	const ROLE_EDITOR = 2;
	const ROLE_ADMINISTRATOR = 3;
	
	const USER_TABLE = 'USERS';
	const USERS_ROLES_TABLE = 'USERS_ROLES';
	
	var $id;
	var $loginId;
	var $name;
	var $passwordHash;
	var $dateRegistered;
	var $roles;			// associative array with boolean values. Missing = false.
	
	public function __construct () {
		$this->roles = array();
	}
	
	/* Check the login credentials. Return a User object if good,
	   pw must NOT be escaped.
	   NULL otherwise. */
	public static function verifyLogin($username, $pw) {
		$result = ORM::for_table(self::USER_TABLE)->
			select('id')->
			select('password_hash')->
			select('name')->
			where_equal ('login_id', $username)->
			find_one();

		if ($result && password_verify ($pw, $result->password_hash)) {
			$user = new User();
			$user->id = $result->id;
			$user->loginId = $username;
			$user->name = $result->name;
			$user->roles = $user->getRoles();
			return $user;
		}
		return NULL;		// no user match, or wrong password
	}
	
	/* Retrieve a row with a given login name, not checking the password */
	public static function findByLoginId ($username) {
		$result = ORM::for_table(self::USER_TABLE)->
			select('id')->
			select('password_hash')->
			select('name')->
			where_equal('login_id', $username)->
			find_one();
		if ($result) {
			$user = new User();
			$user->id = $result->id;
			$user->loginId = $username;
			$user->passwordHash = $result->password_hash;
			$user->name = $result->name;
			$user->roles = $user->getRoles();
			return $user;
		}
			
		return NULL;
	}
	
	/* Retrieve a row with a given ID */
	public static function findById ($id) {
		$result = ORM::for_table(self::USER_TABLE)->
			select('login_id')->
			select('password_hash')->
			select('name')->
			where_equal('id', $id)->
			find_one();
		if ($result) {
			$GLOBALS["logger"]->debug("User:findById found a user");
			$user = new User();
			$user->id = $id;
			$user->loginId = $result->login_id;
			$user->passwordHash = $result->password_hash;
			$user->name = $result->name;
			$user->roles = $user->getRoles();
			return $user;		
		}
		//$selstmt = "SELECT LOGIN_ID, PASSWORD_HASH, NAME FROM USERS WHERE ID = $id";
		return NULL;
	}
	
	/* Get all the users. */
	public static function getAllUsers() {
//		$selstmt = "SELECT ID, LOGIN_ID, NAME FROM USERS";
//		$res = $mysqli->query($selstmt);
		$resultSet = ORM::for_table(self::USER_TABLE)->
			select('id')->
			select('login_id')->
			select('name')->
			find_many();
			
		$rows = array();
		foreach ($resultSet as $result) {
			$user = new User();
			$user->id = $result->id;
			$user->loginId = $result->login_id;
			$user->name = $result->name;
			$user->roles = $user->getRoles();	
			$rows[] = $user;
		}
		return $rows;	
	}
	
	/* Assign a role to a user. */
	public function assignRole ($role) {
		// Check if the role is already assigned
		if ($this->hasRole ($role)) {
			return;			// nothing to do
		}
		
		$roleToInsert = ORM::for_table(self::USERS_ROLES_TABLE)->create();		// Create empty idiorm object
		$roleToInsert->user_id = $this->id;
		$roleToInsert->role = $role;
		$GLOBALS["logger"]->debug ("assignRole, role user ID = " . $roleToInsert->user_id);
		$roleToInsert->save();							// Insert into database
			
//		$insstmt = "INSERT INTO USERS_ROLES (USER_ID, ROLE) VALUES " .
//				"({$this->id}, $role)";
//		$res = $mysqli->query($insstmt);
//		if ($mysqli->connect_errno) {
//			$GLOBALS["logger"]->error("Error getting User role: " . $mysqli->connect_error);
//			throw new Exception ($mysqli->connect_error);
//		}
		$this->roles[$role] = true;
	}
	
	/* Remove a role from a user. */
	public function removeRole ($role) {
		if (!$this->hasRole ($role)) {
			return;			// nothing to do
		}
		$roleToDel = ORM::for_table(self::USERS_ROLES_TABLE)->
			where_equal('user_id', $this->id)->
			find_one();
		$roleToDel->delete ();
			
//		$delstmt = "DELETE FROM USERS_ROLES WHERE USER_ID = '" .
//			$this->id .
//			"' AND ROLE = '" .
//			$role .
//			"'";
//		$res = $mysqli->query($delstmt);
//		if ($mysqli->connect_errno) {
//			$GLOBALS["logger"]->error("Error getting User role: " . $mysqli->connect_error);
//			throw new Exception ($mysqli->connect_error);
//		}
		$this->roles[$role] = false;
	}
	
	
	/* Return true if a user has a specified role, otherwise false. */
	public function hasRole ($role) {
		// $this->roles is an associative array. If a value is missing, treat it as false.
		if (!array_key_exists ($role, $this->roles))
			return false;
			
		$val = $this->roles[$role];
		if (is_null ($val))
			$val = false;
		return $val;
	}
	
	/* Return an array of all roles belonging to a user. */
	private function getRoles() {
		$roleSet = ORM::for_table(self::USERS_ROLES_TABLE)->
			select('role')->
			where_equal ('user_id', $this->id)->
			find_many();
			
//		$selstmt = "SELECT ROLE FROM USERS_ROLES WHERE USER_ID = {$this->id} ";
		$retval = array();
		foreach ($roleSet as $roleResult) {
			$roleVal = $roleResult->role;
			$retval[intval($roleVal)] = true;
		}
		return $retval;
	}
	
	/* Inserts a User into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert () {
		//$lgn = sqlPrep($this->loginId);
		//$pwh = sqlPrep($this->passwordHash);
		//$nm = sqlPrep($this->name);
		//$insstmt = "INSERT INTO USERS (LOGIN_ID, PASSWORD_HASH, NAME) VALUES ($lgn, $pwh, $nm)";
		$newUser = ORM::for_table(self::USER_TABLE)->create();
		$newUser->login_id = $this->loginId;
		$newUser->password_hash = $this->passwordHash;
		$newUser->name = $this->name;
		$newUser->save();
		$this->id = $newUser->id();
		$GLOBALS["logger"]->debug("New user id is " . $this->id);
		return $newUser->id();
	}
}

?>