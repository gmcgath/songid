<?php
/* shortdate.php
   
   This class encapsulates dates of the form yyyymmdd, used in URL parameters.
   
   Gary McGath
   October 19, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
class ShortDate {

	/* year, month, and day are integers. month is 1-based. */
	var $year;
	var $month;
	var $day;
	
	/* Constructor. If dateStr is not provided, set the date to today. */
	public function __construct($dateStr) {
		if (!$dateStr) {
			// Set the date to today
			$this->year = date("Y");
			$this->month = date("m");
			$this->day = date("d");
		}
		else {
			if (strlen($dateStr) != 8 || !ctype_digit($dateStr)) {
				throw new Exception ('Malformed date $dateStr in ShortDate');
			}
			$this->year = intval( substr($dateStr, 0, 4));
			$this->month = intval(substr($dateStr, 4, 2));
			$this->day = intval(substr($dateStr, 6, 2));
		}
	}
	
	/* Return a string suitable for a query on a DATETIME field.
	   For a closing date to be included, set the argument to 'end'; for
	   an opening date to be included, set the argument to 'start'. */
	public function toDateTime ($which) {
		if ($which == 'end') {
			$timepart = ' 23:59:59 ';
		} else
			$timepart = ' 00:00:00 ';
		return $this->year . '-' . $this->month . '-' . $this->day . $timepart;
	}
	
	/* Return a string in the same format passed to the constructor */
	public function toShortString () {
		return sprintf('%04d%02d%02d', $this->year, $this->month, $this->day);
	}
}