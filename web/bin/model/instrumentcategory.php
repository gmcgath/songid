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

	const INST_CAT_TABLE = 'INSTRUMENT_CATEGORIES';
	
	var $id;
	var $name;
	var $displaySequence;

	/** Return an InstrumentCategory matching the specified ID. If no song matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($catId) {
		$result = ORM::for_table(self::INST_CAT_TABLE)->
			select('name')->
			select('display_sequence')->
			where_equal ('id', $catId)->
			find_one();
		if ($result) {
			$cat = new InstrumentCategory();
			$cat->id = $catId;
			$cat->name = $result->name;
			$cat->displaySequence = $result->display_sequence;
			return $cat;
		}
		
		return NULL;
//		$selstmt = "SELECT NAME, DISPLAY_SEQUENCE FROM INSTRUMENT_CATEGORIES WHERE ID = '" . $catId . "'";
	}	
	
	/** Return an array of all InstrumentsCategorys [sic], ordered by display sequence. */
	public static function getAllCategories ($mysqli) {
		$resultSet = ORM::for_table(self::INST_CAT_TABLE)->
			select('id')->
			select('name')->
			select('display_sequence')->
			order_by_asc('display_sequence')->
			find_many();
//		$selstmt = "SELECT ID, NAME, DISPLAY_SEQUENCE FROM INSTRUMENT_CATEGORIES " .
//			"ORDER BY DISPLAY_SEQUENCE";
		$cats = array();
		foreach ($resultSet as $result) {
			$cat = new InstrumentCategory();
			$cat->id = $result->id;
			$cat->name = $result->name;
			$cat->displaySequence = $result->display_sequence;
			$cats[] = $cat;
		}
		// The cats have all been herded
		return $cats;
	}
}