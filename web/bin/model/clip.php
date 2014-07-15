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
	   Returns an array of clip objects. If idx is out of bounds,
	   returns an empty array. If there's a database problem,
	   throws an Exception with the SQL error message.
	*/
	public static function getRows ($mysqli, $idx, $n) {
		$selstmt = "SELECT ID, DESCRIPTION, URL, DATE FROM CLIPS LIMIT " . $idx . " , " . $n;
		error_log($selstmt);
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log("Error getting Clips: " . $mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		$rows = array();
		error_log("Adding rows to array");
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row)) {
					break;
				}
				dumpVar($row);
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
		
		error_log ("Clip.findById");
		$id = sqlPrep($id);
		$selstmt = "SELECT ID, DESCRIPTION, URL, DATE FROM CLIPS WHERE ID = $id";
		$res = $mysqli->query($selstmt);
		error_log ("Returned from query");
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
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
}

?>
