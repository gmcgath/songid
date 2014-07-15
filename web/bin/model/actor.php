<?php

/* actor.php
   Implementation of the ACTORS table as a model
   Gary McGath
   July 15, 2014
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once (dirname(__FILE__) . '/../supportfuncs.php');

class Actor {

	var $id;
	var $name;
	var $typeId;		// TYPE_INDIVIDUAL or TYPE_GROUP
	
	const TYPE_INDIVIDUAL = 1;
	const TYPE_GROUP = 2;
	
	/** Return an Actor matching the specified ID. If no Actor matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($mysqli, $actorId) {
		$actId = sqlPrep($actorId);
		$selstmt = "SELECT NAME, TYPE_ID FROM ACTORS WHERE ID = $actId";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			$row = $res->fetch_row();
			if (is_null($row)) {
				return NULL;
			}
			$actor = new Actor ();
			$actor->id = $actorId;
			$actor->name = $row[0];
			$actor->typeId = $row[1];
			
			return $actor;
		}
		return NULL;
	}
	
	/** Returns an Actor matching the name, or NULL.
	    We use the table ACTOR_NAMES to check all aliases.
	    By convention, the primary name in ACTORS is also in ACTOR_NAMES.
	    Names in ACTOR_NAMES are unique, so there will be no more than one. */
	public static function findByName ($mysqli, $name) {
		$nam = sqlPrep ($name);
		$selstmt = "SELECT ACTOR_ID FROM ACTOR_NAMES WHERE NAME = $nam";
		error_log ($selstmt);
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		$retval = array();
		if ($res) {
			$row = $res->fetch_row();
			if (is_null($row))
				return NULL;
			$actorId = $row[0];
			$actor = Actor::findById ($mysqli, $actorId);
			if (is_null ($actor)) {
				error_log ("Database inconsistency: No actor with ID $actid");
			}
			return $actor;
		}
		return NULL;
	}
	
	/* Inserts an Actor into the database. Throws an Exception on failure.
	   Also puts the name into ACTOR_NAMES.
	   We're using MyISAM, so we can't use a transaction. LOCK TABLES isn't
	   the same thing, as it doesn't cause a rollback if the second insert fails.
	   TODO The best way to do it seems to be a stored procedure:
	   http://stackoverflow.com/questions/15725630/mysql-inserting-into-2-tables-at-once-that-have-primary-key-and-foreign-key
	   
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		
		$nam = sqlPrep($this->name);
		$tpid = sqlPrep($this->typeId);
		$insstmt = "INSERT INTO ACTORS (NAME, TYPE_ID) VALUES ($nam, $tpid)";
		error_log("Inserting Actor: " . $insstmt);
		$res = $mysqli->query ($insstmt);
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;

			// Now add the name to ACTOR_NAMES
			$actrid = sqlPrep($this->id);
			$insstmt2 = "INSERT INTO ACTOR_NAMES (ACTOR_ID, NAME) values ($actrid, $nam)";
			error_log ("Inserting into actor_names: " . $insstmt2);
			$res = $mysqli->query ($insstmt2);
			if ($res) 
				return $this->id;
		}
		error_log ("Error inserting Actor: " . $mysqli->error);
		throw new Exception ("Could not add Actor {$this->name} to database");
	}

}

?>