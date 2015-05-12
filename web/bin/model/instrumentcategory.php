<?php
/* instrumentcategory.php
   Implementation of the INSTRUMENT_CATEGORIES table as a model
   Gary McGath
   July 23, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/../supportfuncs.php');
require_once (dirname(__FILE__) . '/../loggersetup.php');

class InstrumentCategory {
	var $id;
	var $name;
	var $displaySequence;

	/** Return an InstrumentCategory matching the specified ID. If no song matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($mysqli, $catId) {
		$selstmt = "SELECT NAME, DISPLAY_SEQUENCE FROM INSTRUMENT_CATEGORIES WHERE ID = '" . $catId . "'";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			$row = $res->fetch_row();
			if (is_null($row)) {
				return NULL;
			}
			$cat = new InstrumentCategory ();
			$cat->id = $catId;
			$cat->name = $row[0];
			$cat->displaySequence = $row[1];
			
			return $cat;
		}
		return NULL;
	}	
	
	/** Return an array of all InstrumentsCategorys [sic], ordered by display sequence. */
	public static function getAllCategories ($mysqli) {
		$selstmt = "SELECT ID, NAME, DISPLAY_SEQUENCE FROM INSTRUMENT_CATEGORIES " .
			"ORDER BY DISPLAY_SEQUENCE";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			throw new Exception ($mysqli->connect_error);
		}
		$cats = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row)) {
					break;
				}
				$cat = new InstrumentCategory();
				$cat->id = $row[0];
				$cat->name = $row[1];
				$cat->displaySequence = $row[2];
				$cats[] = $cat;
			}
		}
		// The cats have all been herded
		return $cats;
	}
}