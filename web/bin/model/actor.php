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
			$GLOBALS["logger"]->debug ("Found actor with name " . $actor->name);
			$actor->typeId = $result->type_id;
			
			return $actor;
		}
		return NULL;
	}
	
	/** Returns an Actor matching the name, or NULL.
	    Do we really need ACTOR_NAMES at all? The idea of really identifying actors
	    by multiple names seems like a waste of effort. */
	public static function findByName ($name) {
//		$selstmt = "SELECT ACTOR_ID FROM ACTOR_NAMES WHERE NAME = $nam";
		$result = ORM::for_table('ACTORS')->
			select('id')->
			where_equal('name', $name)->
			find_one();
		
		if ($result) {
			$actor = new Actor();
			$actor->id = $result->id;
			$actor->name = $name;
			$actor->typeId = $result->type_id;
		}
		else {
			$actor = null;
		}
		return $actor;
	}
	
	/* Inserts an Actor into the database. Throws an Exception on failure.
	   TODO Make this a transaction.
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
//		$actorNameObj = ORM::for_table('ACTOR_NAMES')->create();
//		$actorNameObj->actor_id = $this->id;
//		$actorNameObj->name = $this->name;
		return $this->id;
//		$insstmt = "INSERT INTO ACTORS (NAME, TYPE_ID) VALUES ($nam, $tpid)";
	}

}

?>