<?php
/* This class provides functions for assembing a Report.
	Input sanitization is done here.

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once (dirname(__FILE__) . '/model/actor.php');
include_once (dirname(__FILE__) . '/model/clip.php');
include_once (dirname(__FILE__) . '/model/report.php');
include_once (dirname(__FILE__) . '/model/song.php');
include_once (dirname(__FILE__) . '/model/instrument.php');
include_once (dirname(__FILE__) . '/model/instrumentcategory.php');
include_once (dirname(__FILE__) . '/supportfuncs.php');

class ReportBuilder {
	var $report;
	var $mysqli;
	var $isSongAmbiguous;
	
	/* Constructor. Creates an empty Report object. */
	public function ReportBuilder ($sqli) {
		error_log ("ReportBuilder constructor");
		if (is_null($sqli))
			throw new Exception ("reportbuilder.php: null mysqli object");
		$this->mysqli = $sqli;
		$this->report = new Report();
		$this->isSongAmbiguous = false;
	}

	/* Populate the report from a POST array, as generated by idform.php */
	public function populate ($postarray) {
		$clipId = $_POST["clipid"];
		$clip = null;
		if ($clipId != null && ctype_digit($clipId)) {
			$clip = Clip::findById($this->mysqli, $clipId);	
		}
		if ($clip == NULL)
			return;
			
		$this->report->clip = $clip;
		$tracktype = trim(strip_tags($this->mysqli->real_escape_string($_POST['tracktype'])));
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
				throw new Exception ("Invalid form, wrong tracktype");
		}
		// Can I make this all more consistent?
		$this->report->setSoundType($tracktype);
	}
	
	/* Set the user in the report to the specified User */
	public function setUser($usr) {
		error_log("setUser");
		$this->report->user = $usr;
	}
	
	
	/* Fill out the report if the user selected Performance */	
	private function doPerformance() {
		error_log ("doPerformance");
		// setSoundSubtype eliminates need to sanitize
		$this->report->setSoundSubtype($_POST["performancetype"]);
		
		if ($_POST("canidsong")) {
			$title = trim(strip_tags($this->mysqli->real_escape_string($_POST["songtitle"])));
			if (strlen($title) > 0) {
				// strlen returns 0 for null object.
				$songs = Song::findByTitle ($this->mysqli, $title);
				// How do we deal with 2 songs by the same title?
				// TODO If we detect an ambiguity, set isSongAmbiguous to true
				// and keep going as far as we can.
				// As a TEMPORARY measure, pick the first song.
				$song = null;
				if (sizeof($songs) > 0)
					$song = $songs[0];
				if (is_null($song)) {
					$song = new Song ();
					$song->title = $title;
					$song->insert($this->mysqli);
				}
				$this->report->song = $song;
			}
		}
		$this->calcPerformerType();
		$this->calcPerformers();
		$this->calcInstruments();
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
		if (!$this->report->insert ($this->mysqli)) {
			throw new Exception ("Failed to insert report into database");
		}
	}
	
	/* Figure out the performer type. */
	private function calcPerformerType () {
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
		if ($_POST["canidperformer"]) {
			$performerNames = $_POST["performernames"];
			if ($performerNames != NULL) {
				$performers = array();
				foreach ($performerNames as $performerName)  {
					$performerName = trim(strip_tags($this->mysqli->real_escape_string($performerName)));
					$actor = Actor::findByName ($this->mysqli, $performerName);
					if (!is_null($actor)) {
						// name belongs to an Actor
						error_log("Actor exists");
						$performers[] = $actor;
					}
					else {
						// No match for name, create an Actor
						error_log("Creating new Actor");
						$actor = new Actor();
						$actor->name = $performerName;
						// TODO for now, assume all performers are individuals (I'm not!)
						$actor->typeId = Actor::TYPE_INDIVIDUAL;
						$actor->insert($this->mysqli);
						$performers[] = $actor;
					}
				}
				$this->report->performers = $performers;
			}
		}
	}
	
	private function calcInstruments () {
		error_log("calcInstruments");
		if ($_POST["instrumentspresent"]) {
			$instruments = array();
			// First we get the list of possible instrument IDs
			$instids = $_POST["instrumentids"];
			// Then we look for the ones that are present
			foreach ($instids as $instid) {
				error_log("Looking at instrument $instid");
				$checkedinst = $_POST[$instid];
				if ($checkedinst != NULL) {
					error_log ("$checkedinst is not null");
					// The ID is passed as instxx. Strip it down to the actual ID.
					$instid = substr($instid, 4);
					$inst = Instrument::findById($this->mysqli, $instid);
					if ($inst != NULL) {
						error_log("Adding instrument $inst->name");
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
					$talkerName = trim(strip_tags($this->mysqli->real_escape_string($talkerName)));
					if ($talkerName == NULL)
						continue;
					$actor = Actor::findByName ($this->mysqli, $talkerName);
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
						$actor->insert($this->mysqli);
						$talkers[] = $actor;
					}
				}
				// Use the performers variable in report for both performers and speakers
				$this->report->performers = $talkers;
			}
		}
	}
}
?>