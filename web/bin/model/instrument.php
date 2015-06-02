<?php
/* instrument.php
   Implementation of the INSTRUMENTS table as a model
   Gary McGath
   July 23, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../loggersetup.php');

class Instrument {

	const INST_TABLE = 'INSTRUMENTS';
	var $id;
	var $name;
	var $categoryId;

	/** Return an Instrument matching the specified ID. If no song matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById (mysqli $mysqli, $instId) {
		$result = ORM::for_table(self::INST_TABLE)->
			select('name')->
			select('category_id')->
			where_equal('id', $instId)->
			find_one();
//		$selstmt = "SELECT NAME, CATEGORY_ID FROM INSTRUMENTS WHERE ID = '" . $instId . "'";
		if ($result) {
			$inst = new Instrument();
			$inst->id = $instId);
			$inst->name = $result->name;
			$inst->categoryId = $result->category_id;
			return $inst;
		}
		return NULL;
	}	
	
	/** Return an array of Instruments, ordered by name, that belong to a
	    specified category. */
	public static function getInstrumentsByCategory (mysqli $mysqli, $catId) {
//		$selstmt = "SELECT ID, NAME FROM INSTRUMENTS WHERE CATEGORY_ID = $catId " .
//			"ORDER BY NAME";
		$resultSet = ORM::for_table(self::INST_TABLE)
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		$instruments = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row)) {
					break;
				}
				$inst = new Instrument();
				$inst->id = $row[0];
				$inst->categoryId = $catId;
				$inst->name = $row[1];
				$instruments[] = $inst;
			}
		}
		return $instruments;
	}
}