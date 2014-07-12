<?php

/* clip.php
   Implementation of the CLIPS table as a model
   Gary McGath
   July 11, 2014
*/
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
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		$rows = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if ($row == NULL) {
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
	
	public static function findById($mysqli, $id) {
		$selstmt = "SELECT ID, DESCRIPTION, URL, DATE FROM CLIPS WHERE ID = '$id'";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			return NULL;
		}
		if ($res) {
			$row = $res->fetch_row();
			if ($row != NULL) {
				$clip = new Clip();
				$clip->id = $row[0];
				$clip->description = $row[1];
				$clip->url= $row[2];
				$clip->date = $row[3];
				return $clip;
			}
		}
		return NULL;
	}
}

?>
