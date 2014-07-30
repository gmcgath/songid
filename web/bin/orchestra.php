<?php
/* orchestra.php
   
   The Orchestra class is a helper to idform. It organizes all the instruments
   found in the database for convenient display.
   
   Gary McGath
   July 23, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once (dirname(__FILE__) . '/supportfuncs.php');
include_once (dirname(__FILE__) . '/model/instrument.php');
include_once (dirname(__FILE__) . '/model/instrumentcategory.php');

class Section {
	var $category;		// an InstrumentCategory
	var $instruments;	// an array of Instruments
	
	function Section ($cat) {
		$this->category = $cat;
	}
}

class Orchestra {

	var $mysqli;
	var $sections;		// an array of Sections, ordered by the display sequence
	
	public function Orchestra ($mysqli) {
		error_log("Constructing Orchestra");
		$this->mysqli = $mysqli;
		$this->sections = array();
	}
	
	/* Build the full orchestra and populate this object with it. */
	public function assemble () {
		$instCats = InstrumentCategory::getAllCategories($this->mysqli);
		foreach ($instCats as $instCat) {
			// Build the list for each category
			$section = new Section ($instCat);
			error_log("section name: " . $section->category->name);
			$section->instruments = Instrument::getInstrumentsByCategory($this->mysqli, $instCat->id);
			$this->sections[] = $section;
		}
	}
	
}