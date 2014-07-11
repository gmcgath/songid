<?php

/* clip.php
   Implementation of the CLIPS table as a model
   Gary McGath
   July 11, 2014

class Clip {
	var $id;
	var $description;
	var $type;
	var $date;

/* Return n rows starting with idx.
   Returns an array of clip objects. If idx is out of bounds,
   returns an empty array. If there's a database problem,
   throws an Exception with the SQL error message.
*/
static function getRows ($mysqli, $idx, $n) {
	var $selstmt = "SELECT * FROM CLIPS LIMIT " . $idx . ", " . $n);
	$res = $mysqli->query($selstmt);
	if ($mysqli->connect_errno) {
		throw new Exception ($mysqli->connect_error);
	}
	var $rows = array();
	if ($res) {
		while (true) {
			var $row = $res->fetch_row();
			if ($row == NULL) {
				break;
			}
			var $clip = new Clip();
			$clip.id = $row['ID'];
			$clip.description = $row['DESCRIPTION'];
			$clip.type = $row['TYPE'];
			$clip.date = $row['DATE'];
			$rows[] = $clip;
		}
	}
	return rows;	
}

?>
