<?php

/* clip.php
   Implementation of the CLIPS table as a model
   Gary McGath
   July 11, 2014

   Copyright 2014-2015 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../loggersetup.php');

class Clip {

	const CLIPS_TABLE = 'CLIPS';
	const REPORTS_TABLE = 'REPORTS';
	
	var $id;
	var $description;
	var $performer;		// free-form text field
	var $event;			// free-form text field
	var $year;			// of the performance or recording
	var $url;
	var $date;
	var $recording_id;

	public function __construct() {
		$this->performer = null;
		$this->event = null;
		$this->year = null;
		$this->recording_id = null;
	}
	
	/* Return n rows starting with idx.
	   If $onlyUnrep is true, show only clips with zero reports
	   Returns an array of clip objects. If idx is out of bounds,
	   returns an empty array. If there's a database problem,
	   throws an Exception with the SQL error message.
	*/
	public static function getRows ($idx, $n, $onlyUnrep) {
		// *** Changing this to Idiorm is impossible because it doesn't
		// support LIMIT with lower bounds, and it would be messier than
		// the SQL even if it were possible. There's a raw_query method
		// or something like it. Use that.
		$clptab = self::CLIPS_TABLE;
		$rpttab = self::REPORTS_TABLE;
		$selstmt = NULL;
		if ($onlyUnrep) {
			$selstmt = "SELECT DISTINCT c.id, c.description, " .
				"c.performer, c.event, c.year, c.url, c.date, " .
				"c.recording_id " .
				"FROM $clptab c LEFT JOIN $rpttab r " .
				"ON c.id = r.clip_id " .
				"WHERE r.id IS NULL " .
				"LIMIT :idx , :n";
		}
		else {
			$selstmt = "SELECT id, description, performer, event, year, url, date, recording_id FROM " .
			"$clptab LIMIT :idx , :n";
		}
		$paramArray = array ('idx' => $idx , 'n' => $n);
		$GLOBALS["logger"]->debug("Querying for rows");
		$resultSet = ORM::for_table('CLIPS')->
			raw_query($selstmt, $paramArray)->
			find_many();
		$rows = array();
		foreach ($resultSet as $result) {
			$clip = new Clip();
			$clip->getFromOrm($result);
			$rows[] = $clip;
		}
		return $rows;	
	}
	
	/* Return the Clip with the specified ID, or NULL. */
	public static function findById($id) {
		$result = ORM::for_table(self::CLIPS_TABLE)->
			select('id')->
			select('description')->
			select('performer')->
			select('event')->
			select('year')->
			select('url')->
			select('date')->
			select('recording_id')->
			where_equal('id', $id)->
			find_one();
		if (!$result) {
			$GLOBALS["logger"]->debug ("No clip found with ID $id");
			return NULL;
		}
		$clip = new Clip();
		$clip->getFromOrm($result);
		return $clip;
	}
	
	/* Write the updated values of the clip out. */
	public function update() {
		$recToUpdate = ORM::for_table(self::CLIPS_TABLE)->find_one($this->id);
		if ($recToUpdate) {
			$recToUpdate->description = $this->description;
			$recToUpdate->url = $this->url;
			$recToUpdate->save();
		}
	}

	/* Inserts a Clip into the database. Throws an Exception on failure.
	   Returns the ID if successful. 
	   The Date field will not be filled in. You have to re-get the Clip to do that.
	*/
	public function insert () {
		$newRecord = ORM::for_table(self::CLIPS_TABLE)->create();
		$newRecord->description = $this->description;
		$newRecord->performer = $this->performer;
		$newRecord->event = $this->event;
		$newRecord->year = $this->year;
		$newRecord->url = $this->url;
		$newRecord->recording_id = $this->recording_id;
		$newRecord->save();
		$this->id = $newRecord->id();
		return $newRecord->id();
	}
	
	/* Load the values from an ORM result. */
	private function getFromOrm ($result) {
		$this->id = $result->id;
		$this->description = $result->description;
		$this->performer = $result->performer;
		$this->event = $result->event;
		$this->year = $result->year;
		$this->url = $result->url;
		$this->date = $result->date;
		$this->recording_id = $result->recording_id;
	}
}

?>
