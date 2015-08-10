<?php

/* recording.php
   Implementation of the RECORDINGS table as a model
   Gary McGath
   July 2, 2015

   Copyright 2015 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../loggersetup.php');

class Recording {

	const RECORDINGS_TABLE = 'RECORDINGS';

	/* Variables corresponding to coluns in the table */	
	var $id;
	var $path;
	var $event;
	var $year;
	
	/* Return the Clip with the specified ID, or NULL. */
	public static function findById($id) {
		$result = ORM::for_table(self::RECORDINGS_TABLE)->
			select('id')->
			select('path')->
			select('event')->
			select('year')->
			where_equal('id', $id)->
			find_one();
		if (!$result) {
			$GLOBALS["logger"]->debug ("No recording found with ID $id");
			return NULL;
		}
		$rec = new Recording();
		$rec->getFromOrm($result);
		return $rec;
	}

	/* Inserts a Recording into the database. Throws an Exception on failure.
	   Returns the ID if successful. 
	*/
	public function insert () {
		$newRecord = ORM::for_table(self::RECORDINGS_TABLE)->create();
		$newRecord->event = $this->event;
		$newRecord->year = $this->year;
		$newRecord->path = $this->path;
		$newRecord->save();
		$this->id = $newRecord->id();
		return $newRecord->id();
	}

	/* Write the updated values of the recording out. */
	public function update() {
		$recToUpdate = ORM::for_table(self::RECORDINGS_TABLE)->find_one($this->id);
		if ($recToUpdate) {
			$recToUpdate->path = $this->path;
			$recToUpdate->event = $this->event;
			$recToUpdate->year = $this->year;
			$recToUpdate->save();
		}
	}
	
	/* Load the values from an ORM result. */
	private function getFromOrm ($result) {
		$this->id = $result->id;
		$this->path = $result->path;
		$this->event = $result->event;
		$this->year = $result->year;
	}
	
}