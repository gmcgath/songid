<?php

/* actor.php
   Implementation of the ACTORS table as a model
   Gary McGath
   July 15, 2014
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../loggersetup.php');
require_once (dirname(__FILE__) . '/../initorm.php');

class Actor {

	var $id;
	var $name;
	var $typeId;		// TYPE_INDIVIDUAL or TYPE_GROUP
	
	const TYPE_INDIVIDUAL = 1;
	const TYPE_GROUP = 2;
	
	const ACTOR_TABLE = 'ACTORS';
	
	/** Return an Actor matching the specified ID. If no Actor matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($actorId) {
		$result = ORM::for_table(self::ACTOR_TABLE)->
			select('name')->
			select('type_id')->
			where_equal('id', $actorId)->
			find_one();
//		$selstmt = "SELECT NAME, TYPE_ID FROM ACTORS WHERE ID = $actId";
		if ($result) {
			$actor = new Actor ();
			$actor->id = $actorId;
			$actor->name = $result->name;
			$actor->typeId = $result->typeId;
			
			return $actor;
		}
		return NULL;
	}
	
	/** Returns an Actor matching the name, or NULL.
	    We use the table ACTOR_NAMES to check all aliases.
	    By convention, the primary name in ACTORS is also in ACTOR_NAMES.
	    Names in ACTOR_NAMES are unique, so there will be no more than one. */
	public static function findByName ($name) {
//		$selstmt = "SELECT ACTOR_ID FROM ACTOR_NAMES WHERE NAME = $nam";
		$resultSet = ORM::for_table('ACTOR_NAMES')->
			select('actor_id')->
			where_equal('name', $name)->
			find_one();
		
		$actor = Actor::findById($result->actor_id);
		if (is_null ($actor)) {
			$GLOBALS["logger"]->info ("Database inconsistency: No actor with ID $actid");
		}
		return $actor;
	}
	
	/* Inserts an Actor into the database. Throws an Exception on failure.
	   Also puts the name into ACTOR_NAMES.
	   We're using MyISAM, so we can't use a transaction. LOCK TABLES isn't
	   the same thing, as it doesn't cause a rollback if the second insert fails.
	   TODO The best way to do it seems to be a stored procedure:
	   http://stackoverflow.com/questions/15725630/mysql-inserting-into-2-tables-at-once-that-have-primary-key-and-foreign-key
	   
	   Returns the ID if successful.
	*/
	public function insert () {
		$tpid = sqlPrep($this->typeId);
		$actorObj = ORM::for_table(self::ACTOR_TABLE)->create();
		$actorObj->name = $this->name;
		$actorObj->type_id = $this->typeId;
		$actorObj->save();
		$this->id = $actorObj->id();
		
		// Now add the name to ACTOR_NAMES
		$actorNameObj = ORM::for_table('ACTOR_NAMES')->create();
		$actorNameObj->actor_id = $this->id;
		$actorNameObj->name = $this->name;
		return $this->id;
//		$insstmt = "INSERT INTO ACTORS (NAME, TYPE_ID) VALUES ($nam, $tpid)";
	}

}

?>