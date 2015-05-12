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
	var $id;
	var $title;
	var $note;
	
	/** Return a Song matching the specified ID. If no song matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($mysqli, $songId) {
		$selstmt = "SELECT TITLE, NOTE FROM SONGS WHERE ID = '" . $songId . "'";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			$row = $res->fetch_row();
			if (is_null($row)) {
				return NULL;
			}
			$song = new Song ();
			$song->id = $songId;
			$song->title = $row[0];
			$song->note = $row[1];
			
			return $song;
		}
		return NULL;
	}
	
	/** Returns an array of Songs matching the title. May be empty. */
	public static function findByTitle ($mysqli, $title) {
		$ttl = sqlPrep ($title);
		$selstmt = "SELECT ID, NOTE FROM SONGS WHERE TITLE = $ttl";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		$retval = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row))
					break;
				$song = new Song();
				$song->id = $row[0];
				$song->note = $row[1];
				$song->title = $title;
				$retval[] = $song;
			}		
		}
		return $retval;
	}
	
	/* Inserts a Song into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		$ttl = sqlPrep($this->title);
		$nte = sqlPrep($this->note);
		$insstmt = "INSERT INTO SONGS (TITLE, NOTE) VALUES ($ttl, $nte)";
		$res = $mysqli->query ($insstmt);
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			return $this->id;
		}
		$GLOBALS["logger"]->error ("Error inserting Song: " . $mysqli->error);
		throw new Exception ("Could not add Song {$this->title} to database");
	}
	
}
?>