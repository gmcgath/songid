<?php
/* report.php
   Implementation of the REPORTS table as a model
   Gary McGath
   July 12, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once (dirname(__FILE__) . '/actor.php');
include_once (dirname(__FILE__) . '/song.php');
include_once (dirname(__FILE__) . '/clip.php');

class Report {

	// values for soundType in database
	const SOUND_TYPE_PERFORMANCE = 1;
	const SOUND_TYPE_TALK = 2;
	const SOUND_TYPE_OTHER = 3;

	// values for soundSubtype in database
	const SOUND_SUBTYPE_PRF_SONG = 1;
	const SOUND_SUBTYPE_PRF_MEDLEY = 2;
	const SOUND_SUBTYPE_PRF_INST = 3;
	const SOUND_SUBTYPE_PRF_SPOKEN = 4;
	const SOUND_SUBTYPE_PRF_OTHER = 5;
	const SOUND_SUBTYPE_TALK_ANNC = 11;
	const SOUND_SUBTYPE_TALK_CONV = 12;
	const SOUND_SUBTYPE_TALK_AUCTION = 13;
	const SOUND_SUBTYPE_TALK_SONGID = 14;
	const SOUND_SUBTYPE_TALK_OTHER = 15;
	const SOUND_SUBTYPE_NOISE_SETUP = 21;
	const SOUND_SUBTYPE_NOISE_SILENCE = 22;
	const SOUND_SUBTYPE_NOISE_OTHER = 23;
	
	// values for performerType in database
	const PERFORMER_TYPE_SINGLE_MALE = 1;
	const PERFORMER_TYPE_SINGLE_FEMALE = 2;
	const PERFORMER_TYPE_SINGLE_UNSPEC = 3;
	const PERFORMER_TYPE_GROUP_MALE = 4;
	const PERFORMER_TYPE_GROUP_FEMALE = 5;
	const PERFORMER_TYPE_GROUP_MIXED = 6;
	const PERFORMER_TYPE_GROUP_UNSPEC = 7;
	

	var $id;
	var $date;
	var $clip;		// a Clip pointed to by CLIP_ID
	var $user;		// a User pointed to by USER_ID
	var $soundType;	// Use one of the SOUND_TYPE_xxx constants
	var $soundSubtype;	// Use one of the SOUNT_SUBTYPE_xxx constants
	var $performerType;	// Use one of the PERFORMER_TYPE_xxx constants
	var $song;		// a Song pointed to by SONG_ID
	var $singalong;	// boolean
	var $performers;	// An array of Actors, or null
	var $composers;		// An array of Actors, or null
	
	// Map from sound type strings to numeric constants 
	var $soundTypeMap = array (
		"performance"=>Report::SOUND_TYPE_PERFORMANCE,
		"talk"=>Report::SOUND_TYPE_TALK,
		"noise"=>Report::SOUND_TYPE_OTHER
	);
	
	// Map from sound subtype strings to numeric constants
	var $soundSubtypeMap = array (
		"perftype_song"=>Report::SOUND_SUBTYPE_PRF_SONG,
		"perftype_medley"=>Report::SOUND_SUBTYPE_PRF_MEDLEY,
		"perftype_inst"=>Report::SOUND_SUBTYPE_PRF_INST,
		"perftype_spoken"=>Report::SOUND_SUBTYPE_PRF_SPOKEN,
		"talktype_annc"=>Report::SOUND_SUBTYPE_TALK_ANNC,
		"talktype_conv"=>Report::SOUND_SUBTYPE_TALK_CONV,
		"talktype_auction"=>Report::SOUND_SUBTYPE_TALK_AUCTION,
		"talktype_songid"=>Report::SOUND_SUBTYPE_TALK_SONGID,
		"noisetype_setup"=>Report::SOUND_SUBTYPE_NOISE_SETUP,
		"noisetype_silence"=>Report::SOUND_SUBTYPE_NOISE_SILENCE,
		"noisetype_other"=>Report::SOUND_SUBTYPE_NOISE_OTHER
	);
	
	public function Report () {
		// possibly redundant initialization for clarity
		$this->singalong = false;
		$this->song = NULL;
		$this->user = NULL;
		$this->clip = NULL;
		$this->soundType = 1;
		$this->soundSubtype = 0;
	}
	
	/** Return a Report matching the specified ID. If no report matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($mysqli, $reportId) {
		$selstmt = "SELECT CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, " .
			"PERFORMER_TYPE, SONG_ID, SINGALONG, DATE " .
			"FROM REPORTS WHERE ID = " . $reportID;
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
			$report->buildFromRow($mysqli, $row);
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
		$sndsbtyp = sqlPrep($this->soundSubtype);
		$insstmt = "INSERT INTO REPORTS (CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, SONG_ID, SINGALONG) " .
			" VALUES ($clpid, $usrid, $sndtyp, $sndsbtyp, $sngid, $sngalng)";
		$res = $mysqli->query ($insstmt);
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			$this->writePerformers ($mysqli);
			return $this->id;
		}
		
		return false;
	}
	
	/* After writing the Report, write the Performers if necessary. */
	private function writePerformers ($mysqli) {
		if ($this->performers != NULL) {
			foreach ($this->performers as $performer) {
				// $performer is an Actor
				$rptid = sqlPrep($this->id);
				$actid = sqlPrep($performer->id);
				$insstmt = "INSERT INTO REPORTS_PERFORMERS (REPORT_ID, ACTOR_ID) " .
					"VALUES ($rptid, $actid)";
				$mysqli->query($insstmt);
				if ($mysqli->connect_errno) {
					error_log($mysqli->connect_error);
					throw new Exception ("Error writing performers: " . $mysqli->connect_error);
				}
			}
		}
	}
	
	/* Set the sound type, either using one of the string constants or integers. */
	public function setSoundType ($typ) {
		if (ctype_digit ($typ))
			$this->soundType = $typ;
		else if (!is_null($this->soundTypeMap[$typ]))
			$this->soundType = $this->soundTypeMap[$typ];
	}
	
		/* Set the sound subtype, either using one of the string constants or integers. */
	public function setSoundSubtype ($typ) {
		if (ctype_digit ($typ))
			$this->soundSubtype = $typ;
		else if (!is_null($this->soundTypeMap[$typ]))
			$this->soundType = $this->soundTypeMap[$typ];
	}
	
	

	
	/* Return an array of reports, starting with report m and
	   returning up to n reports. The ordering of the reports is
	   reverse chronological. Later we may want to add other orderings.
	*/
	public static function getReports ($mysqli, $m, $n) {
			$selstmt = "SELECT ID, CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, " .
			"PERFORMER_TYPE, SONG_ID, SINGALONG, DATE " .
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
				$rep->buildFromRow($mysqli, $row);
				$reports[] = $rep;
				}
		}
		return $reports;
	}
	
	/* Construct the report from a result row. This is used from getReports and
	   findById, which need to return the same row elements. */
	private function buildFromRow($mysqli, $row) {
		error_log("buildFromRow");
		dumpVar($row);
		$this->id = $row[0];
		$clipId = $row[1];
		$this->clip = Clip::findById($mysqli, $clipId);
		if (is_null ($this->clip)) {
			error_log ("Could not find clip with ID $clipId");
			throw new Exception ("Database problem getting clips");
		}
		// TODO add user later
		$this->soundType = $row[3];
		$this->soundSubtype = $row[4];
		$this->performerType = $row[5];
		$songId = $row[6];
		if (!is_null($songId)) {
			error_log ("Finding song by ID $songId");
			$this->song = Song::findById($mysqli, $songId);
		}
		$this->singalong = ($row[7] == 1) ? true : false;
		$this->date = $row[8];
		$this->getPerformers($mysqli);
	}
	
	/* Add any performers to the Report object */
	private function getPerformers ($mysqli) {
		$rptid = sqlPrep($this->id);
		$selstmt = "SELECT ACTOR_ID FROM REPORTS_PERFORMERS WHERE REPORT_ID = $rptid";
		error_log($selstmt);
		$res = $mysqli->query($selstmt);
		$this->performers = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row))
					break;
				error_log("Got a performer row");
				$actorId = $row[0];
				$performer = Actor::findById($mysqli, $actorId);
				if ($performer != NULL) {
					$this->performers[] = $performer;
				}
			}
		}
	}
	   
}

?>