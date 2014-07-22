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

include_once (dirname(__FILE__) . '/../supportfuncs.php');
include_once (dirname(__FILE__) . '/../password.php');	// Required prior to PHP 5.5

class Authcode {
	var $id;
	var $hash;
	
	/* Check the specified authcode. Return true if good,
	   false otherwise. authcode must not be escaped. */
	public static function verifyAuth($mysqli, $auth) {
		$usr = sqlPrep($username);
		$selstmt = "SELECT CODE_HASH FROM AUTHCODES";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting Users: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row))
					break;
				if (password_verify($auth, $row[0])) {
					return true;
				}
			}
		}
		return false;
	}


	/* Inserts an Authcode into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		$hsh = sqlPrep($this->hash);
		$insstmt = "INSERT INTO AUTHCODES (CODE_HASH) VALUES ($hsh)";
		$res = $mysqli->query ($insstmt);
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			return $this->id;
		}
		error_log ("Error inserting Auth code: " . $mysqli->error);
		throw new Exception ("Could not add auth code to database");
	}
}