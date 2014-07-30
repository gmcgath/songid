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
include_once (dirname(__FILE__) . '/instrument.php');

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
	const PERFORMER_TYPE_UNSPEC = 0;
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
	var $instruments;	// An array of Instruments, or null
	var $seqNum;		// Sequence number in chain, starting with 0
	var $nextReport;	// Next report in chain, or null
	var $masterId;		// ID of the master report, null if this is the master
	
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
		"perftype_instrumental"=>Report::SOUND_SUBTYPE_PRF_INST,
		"perftype_spoken"=>Report::SOUND_SUBTYPE_PRF_SPOKEN,
		"perftype_other"=>Report::SOUND_SUBTYPE_PRF_OTHER,
		"talktype_annc"=>Report::SOUND_SUBTYPE_TALK_ANNC,
		"talktype_conv"=>Report::SOUND_SUBTYPE_TALK_CONV,
		"talktype_auction"=>Report::SOUND_SUBTYPE_TALK_AUCTION,
		"talktype_songid"=>Report::SOUND_SUBTYPE_TALK_SONGID,
		"talktype_other"=>Report::SOUND_SUBTYPE_TALK_OTHER,
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
		$this->seqNum = 0;
	}
	
	/** Return a Report matching the specified ID. If no report matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($mysqli, $reportId) {
		$selstmt = "SELECT ID, CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, " .
			"PERFORMER_TYPE, SONG_ID, SINGALONG, DATE, MASTER_ID, SEQ_NUM " .
			"FROM REPORTS WHERE ID = " . $reportId;
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
	
	/* Make this a chained Report by setting the masterId and seqNum fields. */
	public function addToChain($prevReport) {
		if ($prevReport->masterId != null)
			$this->masterId = null;
		else
			$this->masterId = $prevReport->id;
		$this->seqNum = $prevReport->seqNum + 1;
	}


	/* Inserts a Report into the database. Throws an Exception on failure.
	   Returns the ID if successful.
	*/
	public function insert ($mysqli) {
		$sngid = NULL;
		if (!is_null($this->song)) 
			$sngid = $this->song->id;
		$sngid = sqlPrep ($sngid);
		$sngalng = sqlPrep($this->singalong);
		$clpid = sqlPrep($this->clip->id);
		$mstrid = sqlPrep($this->masterId);
		$seqn = sqlPrep($this->seqNum);
		$usrid = sqlPrep($this->user->id);
		$sndtyp = sqlPrep($this->soundType);
		$sndsbtyp = sqlPrep($this->soundSubtype);
		$prftyp = sqlPrep($this->performerType);
		$insstmt = "INSERT INTO REPORTS (CLIP_ID, MASTER_ID, SEQ_NUM, USER_ID, SOUND_TYPE, " .
			" SOUND_SUBTYPE, SONG_ID, PERFORMER_TYPE, SINGALONG) " .
			" VALUES ($clpid, $mstrid, $seqn, $usrid, $sndtyp, $sndsbtyp, $sngid, $prftyp, $sngalng)";
		$res = $mysqli->query ($insstmt);
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			// Retrieve the ID of the row we just inserted
			$this->id = $mysqli->insert_id;
			$this->writePerformers ($mysqli);
			$this->writeInstruments ($mysqli);
			return $this->id;
		}
		
		return false;
	}
	
	/* Deletes the current Report from the database. */
	public function delete ($mysqli) {
		$id = sqlPrep($this->id);
		$delstmt = "DELETE FROM REPORTS WHERE ID = $id";
		$res = $mysqli->query ($delstmt);
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
	}
	
	/* After writing the Report, write the Performers if necessary. */
	private function writePerformers ($mysqli) {
		error_log("writePerformers");
		if ($this->performers != NULL) {
			foreach ($this->performers as $performer) {
				error_log("Got a performer {$performer->id}");
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

	/* After writing the Report, write the Instruments if necessary. */
	private function writeInstruments ($mysqli) {
		if ($this->instruments != NULL) {
			foreach ($this->instruments as $instrument) {
				$rptid = sqlPrep($this->id);
				$instid = sqlPrep($instrument->id);
				$insstmt = "INSERT INTO REPORTS_INSTRUMENTS (REPORT_ID, INSTRUMENT_ID) " .
					"VALUES ($rptid, $instid)";
				$mysqli->query($insstmt);
				if ($mysqli->connect_errno) {
					error_log($mysqli->connect_error);
					throw new Exception ("Error writing instruments: " . $mysqli->connect_error);
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
		error_log("setSoundSubtype: $typ");
		if (ctype_digit ($typ))
			$this->soundSubtype = $typ;
		else if (!is_null($this->soundSubtypeMap[$typ]))
			$this->soundSubtype = $this->soundSubtypeMap[$typ];
	}
	
	

	
	/* Return an array of reports, starting with report m and
	   returning up to n reports. The ordering of the reports is
	   reverse chronological. Later we may want to add other orderings.
	   
	   Only the head report of each chain (the one with a null MASTER_ID)
	   is put into the array. Chained reports are linked by the nextReport
	   field.
	*/
	public static function getReports ($mysqli, $m, $n) {
			$selstmt = "SELECT ID, CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, " .
			"PERFORMER_TYPE, SONG_ID, SINGALONG, DATE, MASTER_ID, SEQ_NUM " .
			"FROM REPORTS  " .
			"WHERE MASTER_ID IS NULL " .
			"ORDER BY DATE DESC " .
			"LIMIT $m, $n ";
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
				if (is_null($row))
					break;
				$rep = new Report();
				$rep->buildFromRow($mysqli, $row);
				$reports[] = $rep;
				$rep->buildChain($mysqli);
				}
		}
		return $reports;
	}
	
	/* Construct the report from a result row. This is used from getReports and
	   findById, which need to return the same row elements. */
	private function buildFromRow($mysqli, $row) {
		$this->id = $row[0];
		$clipId = $row[1];
		$this->clip = Clip::findById($mysqli, $clipId);
		
		$userId = $row[2];
		error_log("User id = $userId");
		if ($userId) {
			$this->user = User::findById($mysqli, $userId);
		}
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
		$this->masterId = $row[9];
		$this->seqNum = $row[10];
		$this->addPerformers($mysqli);
		$this->addInstruments($mysqli);
	}
	
	/* Add any performers to the Report object */
	private function addPerformers ($mysqli) {
		$rptid = sqlPrep($this->id);
		$selstmt = "SELECT ACTOR_ID FROM REPORTS_PERFORMERS WHERE REPORT_ID = $rptid";
		$res = $mysqli->query($selstmt);
		$this->performers = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row))
					break;
				$actorId = $row[0];
				$performer = Actor::findById($mysqli, $actorId);
				if ($performer != NULL) {
					$this->performers[] = $performer;
				}
			}
		}
	}
	
	/* Add any instruments to the Report object */
	private function addInstruments ($mysqli) {
		$rptid = sqlPrep($this->id);
		$selstmt = "SELECT INSTRUMENT_ID FROM REPORTS_INSTRUMENTS WHERE REPORT_ID = $rptid";
		error_log($selstmt);
		$res = $mysqli->query($selstmt);
		$this->instruments = array();
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row))
					break;
				$instId = $row[0];
				$instrument = Instrument::findById($mysqli, $instId);
				if ($instrument != NULL) {
					$this->instruments[] = $instrument;
				}
			}
		}
	}
	
	/* Build the chain of reports that follows a master report. */
	private function buildChain ($mysqli) {
		$rpt = $this;
		$rptid = sqlPrep($this->id);
		$selstmt = "SELECT ID, CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, " .
			"PERFORMER_TYPE, SONG_ID, SINGALONG, DATE, MASTER_ID, SEQ_NUM " .
			"FROM REPORTS WHERE MASTER_ID = " . $rptid;
		$res = $mysqli->query($selstmt);
		if ($mysqli->connect_errno) {
			error_log($mysqli->connect_error);
			throw new Exception ($mysqli->connect_error);
		}
		if ($res) {
			while (true) {
				$row = $res->fetch_row();
				if (is_null($row)) {
					break;
				}
				$report = new Report ();
				$report->buildFromRow($mysqli, $row);
				$rpt->nextReport = $report;
				$rpt = $report;
			}
		}
		return NULL;
	}
	   
}

?>