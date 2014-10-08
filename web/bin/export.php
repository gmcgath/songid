<?php
/* export.php 

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   This file contains functions for exporting the database to a CSV file.
   
   This can be complicated. Separate it into two parts; the selection of a set of
   Reports and the exporting of them. This file is just the latter.
   
   We don't know what fields the caller will want. Define constants for possible 
   fields and let the caller set an array that specifies the sequence of fields
   to export.
      
*/

require_once (dirname(__FILE__) . '/globalconstants.php');
require_once (dirname(__FILE__) . '/model/actor.php');
require_once (dirname(__FILE__) . '/model/clip.php');
require_once (dirname(__FILE__) . '/model/report.php');
require_once (dirname(__FILE__) . '/model/song.php');
require_once (dirname(__FILE__) . '/model/instrument.php');
require_once (dirname(__FILE__) . '/model/instrumentcategory.php');
require_once (dirname(__FILE__) . '/supportfuncs.php');

const EXPORT_CLIP_DESC = 1;
const EXPORT_CLIP_URL = 2;
const EXPORT_CLIP_DATE = 3;
const EXPORT_USER_NAME = 4;
const EXPORT_SOUND_TYPE = 5;
//const EXPORT_SOUND_SUBTYPE = 6;
const EXPORT_PERFORMER_TYPE = 7;
const EXPORT_SONG_NAME = 8;
const EXPORT_SONG_NOTE = 9;
const EXPORT_SINGALONG = 10;
const EXPORT_PERFORMERS = 11;
const EXPORT_COMPOSERS = 12;
const EXPORT_INSTRUMENTS = 13;

class Export {

	var $reports;
	var $index;
	var $sequence;
	
	/* Constructor. Creates an Export object.
	   Arguments:
	      $reportArray   An array of the Reports to export.
	      $sequenceArray An array of export constants defining the output.
    */
	public function Export ($reportArray, $sequenceArray) {
		$this->reports = $reportArray;
		$this->sequence = $sequenceArray;
		$index = 0;
	}
	
	/* Export one report as a line of CSV and advance the index to 
	   the next Report. If there are no reports left,
	   return null.
	*/
	public function nextLine () {
		if ($index > count($reports))
			return null;
		$report = $this->reports[$this->index++];
		$line = "";
		for ($i = 0; $i < count($this->sequence); $i++) {
			$field = null;
			switch ($this->sequence[$i]) {
				case Export::EXPORT_CLIP_DESC:
					$field = self::getClipDesc($report);
					break;
				case Export::EXPORT_CLIP_URL:
					$field = self::getClipUrl($report);
					break;
				case Export::EXPORT_CLIP_DATE:
					$field = self::getClipDate($report);
					break;
				case Export::EXPORT_USER_NAME:
					$field = self::getUserName($report);
					break;
				case Export::EXPORT_SOUND_TYPE:
					$field = self::getSoundType($report);
					break;
				case Export::EXPORT_PERFORMER_TYPE:
					$field = self::getPerformerType($report);
					break;
				case Export::EXPORT_SONG_NAME:
					$field = self::getSongTitle($report);
					break;
				case Export::EXPORT_SONG_NOTE:
					$field = self::getSongNote($report);
					break;
				case Export::EXPORT_SINGALONG:
					$field = self::getSingalong($report);
					break;
				case Export::EXPORT_PERFORMERS:
					$field = self::getPerformers($report);
					break;
				case Export::EXPORT_COMPOSERS:
					$field = self::getComposers($report);
					break;
				case Export::EXPORT_INSTRUMENTS:
					$field = self::getInstruments($report);
					break;
				default:
					return null;	// Or throw an exception?
			}
			$isLast = ($i + 1 == count($this->sequence));
			$line = $line . self::csvify($field, isLast);
		}
		return $line;
	}
	
	private static function getClipDesc($report) {
		$desc = "";
		$clip = $report->clip;
		if ($clip) {
			$desc = $clip->description;
		}
		return $desc;
	}
	
	private static function getClipUrl($report) {
		$url = "";
		$clip = $report->clip;
		if ($clip) {
			$url = $clip->url;
		}
		return $url;
	}
	
	private static function getClipDate($report) {
		$date = "";
		$clip = $report->clip;
		if ($clip) {
			$rawdate = new DateTime($clip->date);
			$date = $rawdate->format('Y-m-d');
		}
		return $date;
	}
	
	private static function getUserName($report) {
		$uname = "";
		$user = $report->user;
		if ($user) {
			$uname = $user->name;
		}
		return $uname;
	}
	
	/* This actually gets the sound subtype, which is a finer-grained version
	   of the sound type */
	private static function getSoundType($report) {
		$typetext = Report::soundSubtypeToString($report->soundSubtype);
		if (isNull($typetext))
			$typetext = "";
		return $typetext;
	}
	
	private static function getPerformerType($report) {
		$typetext = Report::performerTypeToString($report->performerType);
		if (isNull($typetext))
			$typetext = "";
		return $typetext;
	}

	private static function getSongTitle($report) {
		$val = "";
		$song = $report->song;
		if ($song) {
			$val = $song->title;
		}
		return $val;
	}
	
	private static function getSongNote($report) {
		$val = "";
		$song = $report->song;
		if ($song) {
			$val = $song->note;
		}
		return $val;
	}
	
	private static function getSingalong($report)   {
		if ($report->singalong)
			$val = "Y";
		else
			$val = "N";
		return $val;
	}
	
	/* Performers are an array, which we turn into a semicolon-separated list */
	private static function getPerformers($report) {
		$perfs = $report->performers;
		$perfStr = "";
		for ($i = 0; $i < count($perfs); $i++) {
			$perfStr .= $perfs[$i]->name;
			if ($i + 1 < count($perfs))
				$perfStr .= ';';
		}
		return $perfStr;
	}
		
	/* Composers are an array, which we turn into a semicolon-separated list */
	private static function getComposers($report) {
		$cmps = $report->composers;
		$cmpStr = "";
		for ($i = 0; $i < count($cmps); $i++) {
			$cmpStr .= $cmps[$i]->name;
			if ($i + 1 < count($cmps))
				$cmpStr .= ';';
		}
		return $cmpStr;
	}
	
	private static function getInstruments($report) {
		$insts = $report->instruments;
		$instStr = "";
		for ($i = 0; $i < count($insts); $i++) {
			$instStr .= $insts[$i]->name;
			if ($i + 1 < count($insts))
				$instStr .= ';';
		}
		return $instStr;
	}
		
	/* This puts the string in quotes if necessary and appends a comma. */
	private static function csvify ($str, $isLast) {
		if ($isLast)
			return $str;			// *** Initial version *** TODO FIX ****
		else
			return $str . ",";
	}
}
?>