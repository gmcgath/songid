<?php
/* report.php
   Implementation of the REPORTS table as a model
   Gary McGath
   July 12, 2014

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/actor.php');
require_once (dirname(__FILE__) . '/song.php');
require_once (dirname(__FILE__) . '/clip.php');
require_once (dirname(__FILE__) . '/instrument.php');
require_once (dirname(__FILE__) . '/user.php');
require_once (dirname(__FILE__) . '/../shortdate.php');
require_once (dirname(__FILE__) . '/../loggersetup.php');

class Report {

	const REPORT_TABLE = 'REPORTS';
	const REPTS_PERFS_TABLE = 'REPORTS_PERFORMERS';
	const REPTS_INSTS_TABLE = 'REPORTS_INSTRUMENTS';
	
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
	var $flagged;		// If nonzero, clip is flagged as inappropriate
	
	// Map from sound type strings to numeric constants 
	var $soundTypeMap = array (
		"performance"=>self::SOUND_TYPE_PERFORMANCE,
		"talk"=>self::SOUND_TYPE_TALK,
		"noise"=>self::SOUND_TYPE_OTHER
	);
	
	// Map from sound subtype strings to numeric constants
	var $soundSubtypeMap = array (
		"perftype_song"=>self::SOUND_SUBTYPE_PRF_SONG,
		"perftype_medley"=>self::SOUND_SUBTYPE_PRF_MEDLEY,
		"perftype_instrumental"=>self::SOUND_SUBTYPE_PRF_INST,
		"perftype_spoken"=>self::SOUND_SUBTYPE_PRF_SPOKEN,
		"perftype_other"=>self::SOUND_SUBTYPE_PRF_OTHER,
		"talktype_annc"=>self::SOUND_SUBTYPE_TALK_ANNC,
		"talktype_conv"=>self::SOUND_SUBTYPE_TALK_CONV,
		"talktype_auction"=>self::SOUND_SUBTYPE_TALK_AUCTION,
		"talktype_songid"=>self::SOUND_SUBTYPE_TALK_SONGID,
		"talktype_other"=>self::SOUND_SUBTYPE_TALK_OTHER,
		"noisetype_setup"=>self::SOUND_SUBTYPE_NOISE_SETUP,
		"noisetype_silence"=>self::SOUND_SUBTYPE_NOISE_SILENCE,
		"noisetype_other"=>self::SOUND_SUBTYPE_NOISE_OTHER
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
		$this->flagged = 0;
	}
	
	/** Return a Report matching the specified ID. If no report matches,
	    returns null. Throws an Exception if there is an SQL error. */
	public static function findById ($reportId) {
		$result = ORM::for_table(self::REPORT_TABLE)->
			where_equal('id', $reportId)->
			find_one();
//		$selstmt = "SELECT ID, CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, " .
//			"PERFORMER_TYPE, SONG_ID, SINGALONG, DATE, MASTER_ID, SEQ_NUM, FLAGGED " .
//			"FROM REPORTS WHERE ID = " . $reportId;
		if ($result) {
			$report = new Report ();
			$report->buildFromOrm($result);
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
	public function insert () {
		$GLOBALS["logger"]->debug("report:insert");
		
		$newRec = ORM::for_table (self::REPORT_TABLE)->create();
		$GLOBALS["logger"]->debug('Created newRec');
		$newRec->clip_id = $this->clip->id;
		$newRec->master_id = $this->masterId;
		$sngid = NULL;
		if (!is_null($this->song)) 
			$sngid = $this->song->id;
		$newRec->song_id = $sngid;
		$newRec->singalong = $this->singalong;
		$newRec->seq_num = $this->seqNum;
		$newRec->user_id = $this->user->id;
		$newRec->sound_type = $this->soundType;
		$newRec->sound_subtype = $this->soundSubtype;
		$newRec->performer_type = $this->performerType;
		$newRec->flagged = $this->flagged;
		$GLOBALS["logger"]->debug("Saving newRec");
		$newRec->save();
		$GLOBALS["logger"]->debug("insert: saved new record into reports table");
		return $newRec->id();
//		$insstmt = "INSERT INTO REPORTS (CLIP_ID, MASTER_ID, SEQ_NUM, USER_ID, SOUND_TYPE, " .
//			" SOUND_SUBTYPE, SONG_ID, PERFORMER_TYPE, SINGALONG, FLAGGED) " .
//			" VALUES ($clpid, $mstrid, $seqn, $usrid, $sndtyp, $sndsbtyp, $sngid, $prftyp, $sngalng, $flgd)";
	}
	
	/* Deletes the current Report from the database. */
	public function delete () {
		$recToDelete = ORM::for_table(self::REPORT_TABLE)->find_one($this->id);
		$recToDelete->delete();
	}
	
	/* Convert a soundSubtype constant to a user-friendly string. */
	public static function soundSubtypeToString ($sndSubtype) {
		$val = NULL;
		switch ($sndSubtype) {
			case self::SOUND_SUBTYPE_PRF_SONG:
				$val = "song";
				break;
			case self::SOUND_SUBTYPE_PRF_MEDLEY:
				$val = "medley";
				break;
			case self::SOUND_SUBTYPE_PRF_INST:
				$val = "instrumental";
				break;
			case self::SOUND_SUBTYPE_PRF_SPOKEN:
				$val = "spoken or shtick";
				break;
			case self::SOUND_SUBTYPE_PRF_OTHER:
				$val = "other";
				break;
			case self::SOUND_SUBTYPE_TALK_ANNC:
				$val = "announcement";
				break;
			case self::SOUND_SUBTYPE_TALK_CONV:
				$val = "conversation";
				break;
			case self::SOUND_SUBTYPE_TALK_AUCTION:
				$val = "auction";
				break;
			case self::SOUND_SUBTYPE_TALK_SONGID:
				$val = "song identification";
				break;
			case self::SOUND_SUBTYPE_TALK_OTHER:
				$val = "other";
				break;
			case self::SOUND_SUBTYPE_NOISE_SETUP:
				$val = "setup";
				break;
			case self::SOUND_SUBTYPE_NOISE_SILENCE:
				$val = "silence";
				break;
			case self::SOUND_SUBTYPE_NOISE_OTHER:
				$val = "other";
				break;
		}
		return $val;
	}
	
	/* Convert a performerType constant into a user-friendly string. */
	public static function performerTypeToString ($perfType) {
		$val = NULL;
		switch ($perfType) {
			case self::PERFORMER_TYPE_UNSPEC:
				$val = "unspecified";
				break;
			case self::PERFORMER_TYPE_SINGLE_MALE:
				$val = "Solo male";
				break;
			case self::PERFORMER_TYPE_SINGLE_FEMALE:
				$val = "Solo female";
				break;
			case self::PERFORMER_TYPE_SINGLE_UNSPEC:
				$val = "Solo gender unspecified";
				break;
			case self::PERFORMER_TYPE_GROUP_MALE:
				$val = "Group male";
				break;
			case self::PERFORMER_TYPE_GROUP_FEMALE:
				$val = "Group female";
				break;
			case self::PERFORMER_TYPE_GROUP_MIXED:
				$val = "Group mixed";
				break;
			case self::PERFORMER_TYPE_GROUP_UNSPEC:
				$val = "Group gender unspecified";
				break;
		}
		return $val;
	}

	/* After writing the Report, write the Performers if necessary. */
	private function writePerformers () {
		$GLOBALS["logger"]->debug("writePerformers");
		if ($this->performers != NULL) {
			foreach ($this->performers as $performer) {
				$GLOBALS["logger"]->debug("Got a performer {$performer->id}");
				// $performer is an Actor
				$newRec = ORM::for_table(self::REPTS_PERFS_TABLE)->create();
				$newRec->report_id = $this->id;
				$newRec->actor_id = $performer->id;
//				$insstmt = "INSERT INTO REPORTS_PERFORMERS (REPORT_ID, ACTOR_ID) " .
//					"VALUES ($rptid, $actid)";
			}
		}
	}
	
	/* After writing the Report, write the Instruments if necessary. */
	private function writeInstruments () {
		if ($this->instruments != NULL) {
			foreach ($this->instruments as $instrument) {
				$newRec = ORM::for_table(self::REPTS_INSTS_TABLE)->create();
				$newRec->report_id = $this->id;
				$newRec->instrument_id = $instrument->id;
				$newRec->save();
//				$insstmt = "INSERT INTO REPORTS_INSTRUMENTS (REPORT_ID, INSTRUMENT_ID) " .
//					"VALUES ($rptid, $instid)";
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
		$GLOBALS["logger"]->debug("setSoundSubtype: $typ");
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
	   
	   startDate and endDate are ShortDate objects or null.
	   
	   -1 for either range value gets you all the reports.
	   
	   This requires one of those 2-argument LIMIT clauses that Idiorm can't handle,
	   so we need a raw query;
	   otherwise we could get away with just the where_raw function.
	*/
	public static function getReports ($m, 
			$n, 
			ShortDate $startDate, 
			ShortDate $endDate) {
		$GLOBALS["logger"]->debug('getReports');
		// Basic where clause; get the head of each chain, signified by a null MASTER_ID
		$whereClause = "master_id IS NULL";
		if ($startDate) {
			$whereClause .= " AND date >= timestamp '" . $startDate->toDateTime('start') . "'";
		}
		if ($endDate) {
			$whereClause .= " AND date <= timestamp '" . $endDate->toDateTime('end') . "'";
		}
		$selstmt = "SELECT * " .
			"FROM " .
			self::REPORT_TABLE .
			" WHERE " .
			$whereClause .
			" ORDER BY date DESC";
		if ($m >= 0 && $n >= 0)
			$selstmt .= " LIMIT $m, $n ";
		$GLOBALS["logger"]->debug('selstmt = ' . $selstmt);
		$resultSet = ORM::for_table(self::REPORT_TABLE)->
			raw_query($selstmt)->
			find_many();
		return self::getReports1 ($resultSet, $m, $n);
	}
	
	/* Like getReports, but for just one clip */
	public static function getReportsForClip($clipid, 
			$m, 
			$n, 
			ShortDate $startDate, 
			ShortDate $endDate) {
		$GLOBALS["logger"]->debug('getReportsForClip');
		$selstmt = "SELECT * " .
			"FROM " .
			self::REPORT_TABLE .
			" WHERE master_id IS NULL AND clip_id = '$clipid' " .
			"ORDER BY date DESC " .
			"LIMIT $m, $n ";
		$GLOBALS["logger"]->debug('selstmt = ' . $selstmt);
		$resultSet = ORM::for_table(self::REPORT_TABLE)->
			raw_query($selstmt)->
			find_many();
		$GLOBALS["logger"]->debug('returned from query');
		return self::getReports1 ($resultSet, $m, $n);
	}


	private static function getReports1($resultSet, $m, $n) {
		$GLOBALS["logger"]->debug('getReports1');
		$reports = array();
		foreach ($resultSet as $result) {
			$GLOBALS["logger"]->debug('Got a report result');
			$rep = new Report();
			$rep->buildFromOrm($result);
			$reports[] = $rep;
			$rep->buildChain();
		}
		return $reports;
	}
	
	/* Construct the report from an ORM result object. This is used from getReports and
	   findById, which need to return the same row elements. */
	private function buildFromOrm($result) {
		$GLOBALS["logger"]->debug('buildFromOrm');
		$this->id = $result->id;
		$clipId = $result->clip_id;
		$this->clip = Clip::findById($clipId);
		
		$userId = $result->user_id;
		$GLOBALS["logger"]->debug("User id = $userId");
		if ($userId) {
			$this->user = User::findById($userId);
			if ($this->user) {
				$GLOBALS["logger"]->debug("Got user: " . $this->user->name);
			}
		}
		$this->soundType = $result->sound_type;
		$this->soundSubtype = $result->sound_subtype;
		$this->performerType = $result->performer_type;
		$songId = $result->song_id;
		if (!is_null($songId)) {
			$GLOBALS["logger"]->debug ("Finding song by ID $songId");
			$this->song = Song::findById($songId);
		}
		$this->singalong = ($result->singalong == 1) ? true : false;
		$this->date = $result->date;
		$this->masterId = $result->master_id;
		$this->seqNum = $result->seq_num;
		$this->flagged = $result->flagged;
		$this->addPerformers();
		$this->addInstruments();
	}
	
	/* Add any performers to the Report object */
	private function addPerformers () {
//		$selstmt = "SELECT ACTOR_ID FROM REPORTS_PERFORMERS WHERE REPORT_ID = $rptid";
		$resultSet = ORM::for_table (self::REPTS_PERFS_TABLE)->
			select('actor_id')->
			where_equal('report_id', $this->id)->
			find_many();
		$this->performers = array();
		foreach ($resultSet as $result) {
			$actorId = $result->actor_id;
			$performer = Actor::findById($actorId);
			if ($performer != NULL) {
				$this->performers[] = $performer;
			}
		}
	}
	
	/* Add any instruments to the Report object */
	private function addInstruments () {
//		$rptid = sqlPrep($this->id);
//		$selstmt = "SELECT INSTRUMENT_ID FROM REPORTS_INSTRUMENTS WHERE REPORT_ID = $rptid";
		$resultSet = ORM::for_table (self::REPTS_INSTS_TABLE)->
			select('instrument_id')->
			where_equal('report_id', $this->id)->
			find_many();
		$this->instruments = array();
		foreach ($resultSet as $result) {
			$instId = $result->instrument_id;
			$instrument = Instrument::findById($instId);
			if ($instrument != NULL) {
				$this->instruments[] = $instrument;
			}
		}
	}
	
	/* Build the chain of reports that follows a master report. */
	private function buildChain () {
		$rpt = $this;
		//$rptid = sqlPrep($this->id);
		//$selstmt = "SELECT ID, CLIP_ID, USER_ID, SOUND_TYPE, SOUND_SUBTYPE, " .
		//	"PERFORMER_TYPE, SONG_ID, SINGALONG, DATE, MASTER_ID, SEQ_NUM, FLAGGED " .
		//	"FROM REPORTS WHERE MASTER_ID = " . $rptid;
		$resultSet = ORM::for_table(self::REPORT_TABLE)->
			where_equal('master_id', $this->id)->
			find_many();
			
		foreach ($resultSet as $result) {
			$report = new Report ();
			$report->buildFromOrm($result);
			$rpt->nextReport = $report;
			$rpt = $report;
		}
	}
	   
}

?>