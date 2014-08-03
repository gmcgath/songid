<?php

/* clip.php
   Implementation of the CLIPS table as a model
   Gary McGath
   July 11, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once (dirname(__FILE__) . '/../supportfuncs.php');

class Clip {
	var $id;
	var $description;
	var $url;
	var $date; 

	/* Return n rows starting with idx.
	   If $onlyUnrep is true, show only clips with zero reports
	   Returns an array of clip objects. If idx is out of bounds,
	   returns an empty array. If there's a database problem,
	   throws an Exception with the SQL error message.
	*/
	public static function getRows ($mysqli, $idx, $n, $onlyUnrep) {
		if ($onlyUnrep) 
			$selstmt = "SELECT DISTINCT c.ID, c.DESCRIPTION, c.URL, c.DATE FROM CLIPS c LEFT JOIN REPORTS r " .
				"ON c.ID = r.CLIP_ID " .
				"WHERE r.ID IS NULL " .
				"LIMIT " . $idx . " , " . $n;
		else
			$selstmt = "SELECT ID, DESCRIPTION, URL, DATE FROM CLIPS LIMIT " . $idx . " , " . $n;
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting Clips: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		$rows = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row)) {
					break;
				}
				$clip = new Clip();
				$clip->id = $row[0];
				$clip->description = $row[1];
				$clip->url= $row[2];
				$clip->date = $row[3];
				$rows[] = $clip;
			}
		}
		return $rows;	
	}
	
	/* Return the Clip with the specified ID, or NULL. */
	public static function findById($mysqli, $id) {
		
		$id = sqlPrep($id);
		$selstmt = "SELECT ID, DESCRIPTION, URL, DATE FROM CLIPS WHERE ID = $id";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting clip by ID: " . $mysqli->connect_error);
			return NULL;
		}
		if ($res) {
			$row = $res->fetch_row();
			if (!is_null($row)) {
				$clip = new Clip();
				$clip->id = $row[0];
				$clip->description = $row[1];
				$clip->url= $row[2];
				$clip->date = $row[3];
				return $clip;
			}
		}
		error_log ("No clip found with ID $id");
		return NULL;
	}
	
	/* Write the updated values of the clip out. */
	public function update($mysqli) {
		$desc = sqlPrep($this->description);
		$ur = sqlPrep($this->url);
		$id = sqlPrep($this->id);
		$updstmt = "UPDATE CLIPS SET " .
			"DESCRIPTION = $desc, ".
			"URL = $ur " .
			"WHERE ID = $id";
		$res = $mysqli->query($updstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting clip by ID: " . $mysqli->connect_error);
		}
	}

	/* Inserts a Clip into the database. Throws an Exception on failure.
	   Returns the ID if successful. 
	   The Date field will not be filled in. You have to re-get the Clip to do that.
	*/
	public function insert ($mysqli) {
		$dsc = sqlPrep($this->description);
		$url = sqlPrep($this->url);
		$insstmt = "INSERT INTO CLIPS (DESCRIPTION, URL) VALUES ($dsc, $url)";
		$res = $mysqli->query ($insstmt);
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			return $this->id;
		}
		error_log ("Error inserting Clip: " . $mysqli->error);
		throw new Exception ("Could not add Clip {$this->description} to database");
	}
}

?>
