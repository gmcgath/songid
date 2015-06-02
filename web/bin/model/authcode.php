<?php

/* authcode.php
   Implementation of the AUTHCODES table as a model
   Gary McGath
   July 19, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   The authorization code is a way to certify that people who try to register
   are authorized to use the system. Normally there will be only one authorization
   code, but there could be more than one. 
   
   The authorization code is hashed, like a password. It can be changed at any time
   without affecting existing registrations.
*/

require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../password.php');	// Required prior to PHP 5.5
require_once (dirname(__FILE__) . '/../loggersetup.php');

class Authcode {

	const AUTHCODE_TABLE = 'AUTHCODES';
	
	var $id;
	var $hash;
	
	/* Check the specified authcode. Return true if good,
	   false otherwise. authcode must not be escaped. */
	public static function verifyAuth($auth) {
		$resultSet = ORM::for_table(self::AUTHCODE_TABLE)->
			select('code_hash')->
			find_many();
		foreach ($resultSet as $result) {
			$hash = $result->code_hash;
			if (password_verify($auth, $hash)) {
				return true;
			}
		}
		return false;
		//$selstmt = "SELECT CODE_HASH FROM AUTHCODES";
	}


	/* Inserts an Authcode into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert () {
		$insertObj = ORM::for_table(self::AUTHCODE_TABLE)->create();
		$insertObj->code_hash = $this->hash;
		$insertObj->save();
		return $insertObj->id();
		//$hsh = sqlPrep($this->hash);
		//$insstmt = "INSERT INTO AUTHCODES (CODE_HASH) VALUES ($hsh)";
	}
}