<?php
/* report.php
   Implementation of the REPORTS table as a model
   Gary McGath
   July 12, 2014
*/

include_once (dirname(__FILE__) . '/song.php');
include_once (dirname(__FILE__) . '/clip.php');

class Report {

	const SOUND_TYPE_PERFORMANCE = 1;
	const SOUND_TYPE_TALK = 2;
	const SOUND_TYPE_OTHER = 3;

	const SOUND_TYPE_PERFORMANCE_STR = "performance";
	const SOUND_TYPE_TALK_STR = "chatter";
	const SOUND_TYPE_OTHER_STR = "noise";
	// Yeah, there's some inconsistency.
	
	var $id;
	var $date;
	var $clip;		// a Clip pointed to by CLIP_ID
	var $user;		// a User pointed to by USER_ID
	var $soundType;	// Use one of the SOUND_TYPE_xxx constants
	var $song;		// a Song pointed to by SONG_ID
	var $singalong;	// boolean
	
	public function Report () {
		// possibly redundant initialization for clarity
		$this->singalong = false;
		$this->song = NULL;
		$this->user = NULL;
		$this->clip = NULL;
	}
	
	/** Return a Report matching the specified ID. If no report matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($mysqli, $reportId) {
		$selstmt = "SELECT CLIP_ID, USER_ID, SOUND_TYPE, SONG_ID, SINGALONG, DATE " .
			" FROM REPORTS WHERE ID = " . $reportID;
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			$row = $res->fetch_row();
			if (is_null($row)) {
				return NULL;
			}
			$report = new Report ();
			$report->id = $reportId;
			$clipId = $row[0];
			if (is_null($clipId))
				$report->clip = Clip::findById($mysqli, $clipId);
			$userId = $row[1];
			// TODO get User. For now we use a constant ID.
			$report->soundType = $row[2];
			$songId = $row[3];
			if (!is_null($songId))
				$this->song = Song::findById ($songId);
			$report->singalong = $row[4];
			$report->date = $row[5];
			
			return $report;
		}
		return NULL;
	}


	/* Inserts a Report into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		// TEMPORARY userId till we add users. Afterwards get the ID from a User object.
		$userId = 1;
		$sngid = NULL;
		if (!is_null($this->song)) 
			$sngid = $this->song->id;
		$sngid = sqlPrep ($sngid);
		$sngalng = sqlPrep($this->singalong);
		$clpid = sqlPrep($this->clip->id);
		$usrid = sqlPrep($userId);
		$sndtyp = sqlPrep($this->soundType);
		$insstmt = "INSERT INTO REPORTS (CLIP_ID, USER_ID, SOUND_TYPE, SONG_ID, SINGALONG) " .
			" VALUES ($clpid, $usrid, $sndtyp, $sngid, $sngalng)";
		error_log ($insstmt);		// DEBUG***
		$res = $mysqli->query ($insstmt);
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			return $this->id;
		}
		
		return false;
	}
	
	/* Set the sound type, either using one of the string constants or integers. */
	public function setSoundType ($typ) {
		if (ctype_digit ($typ))
			$this->soundType = $typ;
		else if ($typ == self::SOUND_TYPE_PERFORMANCE_STR)
			$this->soundType = self::SOUND_TYPE_PERFORMANCE;
		else if ($typ == self::SOUND_TYPE_TALK_STR)
			$this->soundType = self::SOUND_TYPE_TALK;
		else if ($typ == self::SOUND_TYPE_OTHER_STR)
			$this->soundType = self::SOUND_TYPE_OTHER;
	}
	
	/* Return the sound type as a string. */
	public function getSoundTypeAsString () {
		$val = NULL;
		switch ($this->soundType) {
			case self::SOUND_TYPE_PERFORMANCE:
				$val = self::SOUND_TYPE_PERFORMANCE_STR;
				break;
			case self::SOUND_TYPE_TALK:
				$val = self::SOUND_TYPE_TALK_STR;
				break;
			case self::SOUND_TYPE_OTHER:
				$val = self::SOUND_TYPE_OTHER_STR;
				break;
		}
		return $val;
	}
	
	/* Return an array of reports, starting with report m and
	   returning up to n reports. The ordering of the reports is
	   reverse chronological. Later we may want to add other orderings.
	*/
	public static function getReports ($mysqli, $m, $n) {
			$selstmt = "SELECT CLIP_ID, USER_ID, SOUND_TYPE, SONG_ID, SINGALONG, DATE " .
			"FROM REPORTS  " .
			"ORDER BY DATE DESC";
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		$reports = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				error_log("Got a report row");
				dumpVar($row);
				if (is_null($row))
					break;
				$rep = new Report();
				$clipId = $row[0];
				$rep->clip = Clip::findById($mysqli, $clipId);
				if (is_null ($rep->clip)) {
					error_log ("Could not find clip with ID $clipId");
					throw new Exception ("Database problem getting clips");
				}
				// TODO add user later
				$rep->soundType = $row[2];
				$songId = $row[3];
				if (!is_null($songId)) {
					error_log ("Finding song by ID $songId");
					$rep->song = Song::findById($mysqli, $songId);
					dumpVar ($rep->song);
				}
				$rep->singalong = ($row[4] == 1) ? true : false;
				$rep->date = $row[5];
				$reports[] = $rep;
			}
		}
		error_log("Dumping reports");
		dumpVar ($reports);
		return $reports;
	}
	   
}

?>