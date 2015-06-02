<?php
/* song.php
   Implementation of the SONGS table as a model
   Gary McGath
   July 12, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../loggersetup.php');


class Song {

	const SONG_TABLE = 'SONGS';
	
	var $id;
	var $title;
	var $note;
	
	/** Return a Song matching the specified ID. If no song matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($songId) {
		$result = ORM::for_table(self::SONG_TABLE)->
			select('title')->
			select('note')->
			where_id_is($songId)->
			find_one();
		if ($result) {
			$song = new Song();
			$song->id = $songId;
			$song->title = $result->title;
			$song->note = $result->note;
			return $song;
		}
		return NULL;
//		$selstmt = "SELECT TITLE, NOTE FROM SONGS WHERE ID = '" . $songId . "'";
	}
	
	/** Returns an array of Songs matching the title. May be empty. */
	public static function findByTitle ($mysqli, $title) {
		$resultSet = ORM::for_table(self::SONG_TABLE)->
			select('id')->
			select('note')->
			where_equal('title', $title)->
			find_many();
		$retval = array();
		foreach ($resultSet as $result) {
			$song = new Song();
			$song->id = $result->id;
			$song->note = $result->note;
			$song->title = $title;
			$retval[] = $song;
		}
//		$selstmt = "SELECT ID, NOTE FROM SONGS WHERE TITLE = $ttl";
		return $retval;
	}
	
	/* Inserts a Song into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		$recToInsert = ORM::for_table(self::SONG_TABLE)->create();
		$recToInsert->title = $this->title;
		$recToInsert->note = $this->note;
//		$insstmt = "INSERT INTO SONGS (TITLE, NOTE) VALUES ($ttl, $nte)";
		$recToInsert->save();
		return $recToInsert->id();
	}
	
}
?>