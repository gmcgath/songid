<?php
/* This class provides functions for assembing a Report.
	Input sanitization is done here.

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/globalconstants.php');
require_once (dirname(__FILE__) . '/model/actor.php');
require_once (dirname(__FILE__) . '/model/clip.php');
require_once (dirname(__FILE__) . '/model/report.php');
require_once (dirname(__FILE__) . '/model/song.php');
require_once (dirname(__FILE__) . '/model/instrument.php');
require_once (dirname(__FILE__) . '/model/instrumentcategory.php');
require_once (dirname(__FILE__) . '/supportfuncs.php');

class ReportBuilder {
	var $report;
	var $isSongAmbiguous;
	
	/* Constructor. Creates an empty Report object. */
	public function __construct () {
		$this->report = new Report();
		$this->isSongAmbiguous = false;
	}

	/* Populate the report from a POST array, as generated by idform.php */
	public function populate ($postarray, $user) {
		$GLOBALS["logger"]->debug('ReportBuilder:pouplate');
		$this->report->user = $user;
		$this->report->date = date( 'Y-m-d H:i:s');
		$clipId = $_POST["clipid"];
		$clip = null;
		if ($clipId != null && ctype_digit($clipId)) {
			$clip = Clip::findById($clipId);	
		}
		if ($clip == NULL)
			return;
		$this->report->clip = $clip;
		if (array_key_exists("flagged", $_POST)) {
			$this->report->flagged = $_POST["flagged"] ? 1 : 0;
		}
		else {
			$this->report->flagged = 0;
		}
		
		// If previd was passed, put this into the chain.
		if (array_key_exists("previd", $_POST)) {
			$this->chainReport ($_POST["previd"]);
		}

		$tracktype = trim(strip_tags($_POST['tracktype']));
		$GLOBALS["logger"]->debug('tracktype = ' . $tracktype);
		switch ($tracktype) {
			case "performance":
				$this->doPerformance();
				break;
			case "talk":
				$this->doTalk();
				break;
			case "noise":
				$this->doNoise();
				break;
			default:
				throw new Exception (IDFORM_NO_CLIPTYPE);
		}
		$GLOBALS["logger"]->debug ($this->report->song ? "There is a song" : "There is no song");
		// Can I make this all more consistent?
		$GLOBALS["logger"]->debug('calling setSoundType');
		$this->report->setSoundType($tracktype);
	}
	
	/* Set the user in the report to the specified User */
	public function setUser($usr) {
		$this->report->user = $usr;
	}
	
	
	/* Fill out the report if the user selected Performance */	
	private function doPerformance() {
		// setSoundSubtype eliminates need to sanitize
		$this->report->setSoundSubtype($_POST["performancetype"]);
		
		if (array_key_exists("canidsong", $_POST) && array_key_exists ("songtitle", $_POST)) {
			$title = trim(strip_tags($_POST["songtitle"]));
			if (strlen($title) > 0) {
				// strlen returns 0 for null object.
				$songs = Song::findByTitle ($title);
				// How do we deal with 2 songs by the same title?
				// TODO If we detect an ambiguity, set isSongAmbiguous to true
				// and keep going as far as we can.
				// As a TEMPORARY measure, pick the first song.
				$song = null;
				if (sizeof($songs) > 0)
					$song = $songs[0];
				if (is_null($song)) {
					$song = new Song ();
					$GLOBALS["logger"]->debug("Creating new Song with title " . $title);
					$song->title = $title;
					$song->insert();
				}
				$this->report->song = $song;
				$GLOBALS["logger"]->debug("A song was just added by doPerformance");
				if (is_null($song))
					$GLOBALS["logger"]->debug ("But it's null");
			}
		}
		$this->calcPerformerType();
		$this->calcPerformers();
		$this->calcInstruments();
		if (array_key_exists("singalong", $_POST) && $_POST["singalong"]) {
			$this->report->singalong = true;
		}
}

	/* Fill out the report if the user selected talk */
	function doTalk() {
		$this->report->setSoundSubtype($_POST["talktype"]);
		$this->report->performerType = 0;
		$this->calcTalkers();
	}

	/* Fill out the report if the user selected Other/Noise/Silence */
	function doNoise() {
		$this->report->setSoundSubtype($_POST["noisetype"]);
		$this->report->performerType = 0;
	}
	
	/* Insert the object into the database. */
	public function insert () {
		if (!$this->report->insert ()) {
			throw new Exception (DB_INSERT_FAILURE);
		}
	}
	
	/* Figure out the performer type. */
	private function calcPerformerType () {
		$GLOBALS["logger"]->debug('ReportBuilder:calcPerformerTYpe');
		$performerType = $_POST["performertype"];
		$perfType = Report::PERFORMER_TYPE_UNSPEC;		// value to put in database
		switch ($performerType) {
			case "solo":
				switch ($_POST["singleperformersex"]) {
					case "male":
						$perfType = Report::PERFORMER_TYPE_SINGLE_MALE;
						break;
					case "female":
						$perfType = Report::PERFORMER_TYPE_SINGLE_FEMALE;
						break;
					case "unknown":
					default:
						$perfType = Report::PERFORMER_TYPE_SINGLE_UNSPEC;
						break;
				}
				
				break;
			case "group":
				switch ($_POST["groupperformersex"]) {
					case "male":
						$perfType = Report::PERFORMER_TYPE_GROUP_MALE;
						break;
					case "female":
						$perfType = Report::PERFORMER_TYPE_GROUP_FEMALE;
						break;
					case "mixed":
						$perfType = Report::PERFORMER_TYPE_GROUP_MIXED;
						break;
					case "unknown":
					default:
						$perfType = Report::PERFORMER_TYPE_GROUP_UNSPEC;
						break;
				}
			default:
				break;			// already set to default
			
		}
		$this->report->performerType = $perfType;
	}
	
	/* Put the performers into the report */
	private function calcPerformers () {
		$GLOBALS["logger"]->debug('ReportBuilder:calcPerformers');
		if (array_key_exists("canidperformer", $_POST) && $_POST["canidperformer"]) {
			$performerNames = array_key_exists("performernames", $_POST) ? $_POST["performernames"] : NULL;
			if ($performerNames != NULL) {
				$performers = array();
				foreach ($performerNames as $performerName)  {
					$GLOBALS["logger"]->debug ("performer name: " . $performerName);
					$performerName = trim(strip_tags($performerName));
					$actor = Actor::findByName ($performerName);
					if (!is_null($actor)) {
						// name belongs to an Actor
						$GLOBALS["logger"]->debug ("Got an Actor matching performer " . $performerName);
						$performers[] = $actor;
					}
					else {
						// No match for name, create an Actor
						$actor = new Actor();
						$actor->name = $performerName;
						// TODO for now, assume all performers are individuals (I'm not!)
						$actor->typeId = Actor::TYPE_INDIVIDUAL;
						$GLOBALS["logger"]->debug ("Inserting actor with name " . $performerName);
						$actor->insert();
						$performers[] = $actor;
					}
				}
				$this->report->performers = $performers;
			}
		}
	}
	
	private function calcInstruments () {
		if ($_POST["instrumentspresent"]) {
			$instruments = array();
			// First we get the list of possible instrument IDs
			$instids = $_POST["instrumentids"];
			// Then we look for the ones that are present
			foreach ($instids as $instid) {
				$checkedinst = array_key_exists($instid, $_POST) ? $_POST[$instid] : NULL;
				if ($checkedinst != NULL) {
					// The ID is passed as instxx. Strip it down to the actual ID.
					$instid = substr($instid, 4);
					$inst = Instrument::findById($instid);
					if ($inst != NULL) {
						$instruments[] = $inst;
					}
				}
			}
			$this->report->instruments = $instruments;
		}
	}
	
	/* Put the talkers into the report. Very similar to calcPerformer, but used if
	   the track is identified as talk. */
	private function calcTalkers () {
		if ($_POST["canidtalk"]) {
			$talkerNames = $_POST["peopletalking"];
			if ($talkerNames != NULL) {
				$talkers = array();
				foreach ($talkerNames as $talkerName)  {
					$talkerName = trim(strip_tags($talkerName));
					if ($talkerName == NULL)
						continue;
					$actor = Actor::findByName ($talkerName);
					if (!is_null($actor)) {
						// name belongs to an Actor
						$talkers[] = $actor;
					}
					else {
						// No match for name, create an Actor
						$actor = new Actor();
						$actor->name = $talkerName;
						// All talkers are individuals. Assuming no group chanting.
						$actor->typeId = Actor::TYPE_INDIVIDUAL;
						$actor->insert();
						$talkers[] = $actor;
					}
				}
				// Use the performers variable in report for both performers and speakers
				$this->report->performers = $talkers;
			}
		}
	}
	
	/* Chain this report to the previous one, if prevId specifies a previous report
	   and some sanity checks are passed. */
	private function chainReport($prevId) {
		if ($prevId && ctype_digit($prevId)) {
			$prevReport = Report::findById($prevId); 
			if ($prevReport->user->id != $this->report->user->id ||
					$prevReport->clip->id != $this->report->clip->id) {
				return;
			}
			$this->report->addToChain ($prevReport);
			dumpVar($this->report);
			// Because of the way the chaining is structured, we don't
			// need to save any changes to prevReport.
		}
	}
}
?>