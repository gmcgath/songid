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
	const ROLE_ADMIN = 2;
	const ROLE_STAKEHOLDER = 3;
	const ROLE_SUPERUSER = 4;
	
	const USER_TABLE = 'USERS';
	const USERS_ROLES_TABLE = 'USERS_ROLES';
	
	var $id;
	var $loginId;
	var $name;
	var $passwordHash;
	var $dateRegistered;
	var $activated;
	var $selfInfo;
	var $roles;			// associative array with boolean values. Missing = false.
	
	public function __construct () {
		$this->roles = array();
	}
	
	/* Check the login credentials. Return a User object if good,
	   pw must NOT be escaped.
	   NULL otherwise. */
	public static function verifyLogin($username, $pw) {
		$result = ORM::for_table(self::USER_TABLE)->
			where_equal ('login_id', $username)->
			find_one();

		if ($result && password_verify ($pw, $result->password_hash)) {
			$user = new User();
			$user->populateFromResult( $result );
			$user->roles = $user->getRoles();
			return $user;
		}
		return NULL;		// no user match, or wrong password
	}
	
	/* Retrieve a row with a given login name, not checking the password */
	public static function findByLoginId ($username) {
		$result = ORM::for_table(self::USER_TABLE)->
			where_equal('login_id', $username)->
			find_one();
		if ($result) {
			$user = new User();
			$user->populateFromResult( $result );
			$user->roles = $user->getRoles();
			return $user;
		}
			
		return NULL;
	}
	
	
	/* Write the updated values of the user out. */
	public function update() {
		$recToUpdate = ORM::for_table(self::USER_TABLE)->find_one($this->id);
		if ($recToUpdate) {
			$recToUpdate->activated = $this->activated;
			// Add whatever else might be changed. Or just everything?
			$recToUpdate->save();
		}
	}
	
	/* Retrieve a row with a given ID */
	public static function findById ($id) {
		$result = ORM::for_table(self::USER_TABLE)->
			where_equal('id', $id)->
			find_one();
		if ($result) {
			$GLOBALS["logger"]->debug("User:findById found a user");
			$user = new User();
			$user->populateFromResult( $result );
			$user->roles = $user->getRoles();
			return $user;		
		}
		//$selstmt = "SELECT LOGIN_ID, PASSWORD_HASH, NAME FROM USERS WHERE ID = $id";
		return NULL;
	}
	
	/* Return an array of all the users. */
	public static function getAllUsers() {
		$resultSet = ORM::for_table(self::USER_TABLE)->
			find_many();
			
		$rows = array();
		foreach ($resultSet as $result) {
			$user = new User();
			$user->populateFromResult( $result );
			$user->roles = $user->getRoles();	
			$rows[] = $user;
		}
		return $rows;	
	}
	
	/* Return all the users with a specified role. */
	public static function getUsersWithRole( $role ) {
		$userRoleResultSet = ORM::for_table(self::USERS_ROLES_TABLE)->
			select('user_id')->
			where('role', $role);
		// Build a string of the admin user IDs 
		$inArray = array();
		foreach ($userRoleResultSet as $result) {
			$inArray[] = $result->user_id;
		}
		
		$resultSet = ORM::for_table(self::USER_TABLE)->
			where_in('id', $inarray)->
			find_many();
	}
	
	/* Set the user's activated status */
	public function activate () {
		$this->activated = 1;
		$this->update();
	}

	
	/* Return an array of all users that have no roles. */
	public static function getInactiveUsers() {
		$resultSet = ORM::for_table(self::USER_TABLE)->
			where_not_equal( 'activated', 0 )->
			find_many();
		$retval = array();
		$rows = array();
		foreach ($resultSet as $result) {
			$user = new User();
			$user->id = $result->id;
			$user->loginId = $result->login_id;
			$user->name = $result->name;
			$user->activated = $result->activated;
			$user->selfInfo = $result->self_info;
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
			
		$this->roles[$role] = false;
	}
	
	
	/* Return true if a user has a specified role, otherwise false. */
	public function hasRole ($role) {
		// If user isn't activated, force to having no roles
		if ( !$this->activated ) {
			return false;
		}

		// $this->roles is an associative array. If a value is missing, treat it as false.
		if (!array_key_exists ($role, $this->roles))
			return false;
			
		$val = $this->roles[$role];
		if (is_null ($val))
			$val = false;
		return $val;
	}
	
	/* Return an array of all roles belonging to a user. */
	public function getRoles() {
		$retval = array();

		// If user isn't activated, always return an empty array of roles
		if ( !$this->activated ) {
			return $retval;
		}

		$roleSet = ORM::for_table(self::USERS_ROLES_TABLE)->
			select('role')->
			where_equal ('user_id', $this->id)->
			find_many();
			
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
		$newUser = ORM::for_table(self::USER_TABLE)->create();
		$newUser->login_id = $this->loginId;
		$newUser->password_hash = $this->passwordHash;
		$newUser->name = $this->name;
		$newUser->activated = $this->activated;
		$newUser->self_info = $this->selfInfo;
		$newUser->save();
		$this->id = $newUser->id();
		$GLOBALS["logger"]->debug("New user id is " . $this->id);
		return $newUser->id();
	}
	
	/* Populates a user from a query result. Doesn't get roles. */
	public function populateFromResult($result) {
		if ( isset( $result->id )) {
			$this->id = $result->id;
		}
		if ( isset( $result->login_id )) {
			$this->loginId = $result->login_id;
		}
		if ( isset( $result->password_hash )) {
			$this->passwordHash = $result->password_hash;
		}
		if ( isset( $result->name )) {
			$this->name = $result->name;
		}
		if ( isset( $result->activated )) {
			$this->activated = $result->activated;
		}
		if ( isset( $result->self_info )) {
			$this->selfInfo = $result->self_info;
		}
	}
}

?>