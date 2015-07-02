<?php
/*	processaddclip.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the clip editing form is submitted. 
   It has no HTML and always redirects.  */

require_once ('bin/config.php');
require_once ('bin/supportfuncs.php');
require_once ('bin/model/report.php');
require_once ('bin/model/clip.php');
require_once ('bin/reportbuilder.php');
require_once ('bin/loggersetup.php');

require_once('bin/model/user.php');
session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header ("Location: 405.html", true, 405);
	return;
}

/* Maximum upload size. The limit in php.ini should be at least as big. */
$MAX_FILE_SIZE = 40 * 1024 * 1024;

$ALLOWED_TYPES = array("mp3");

if (!isset($AUDIO_DIR)) {
	$GLOBALS["logger"]->error("AUDIO_DIR is not defined");
	header ("Location: addclipok.php?id=$clipId", true, 500);
}

$URL_BASE = "audio/";

$err = -1;

$clip = new Clip;
$clip->description = array_key_exists('clipdesc', $_POST) ? strip_tags($_POST["clipdesc"]) : NULL;
$clip->performer = array_key_exists('performer', $_POST) ? strip_tags($_POST["performer"]) : NULL;
if (is_string ($clip->performer) && strlen($clip->performer) == 0) {
	$clip->performer = NULL;
}
$clip->event = array_key_exists('event', $_POST) ? strip_tags($_POST["event"]) : NULL;
if (is_string ($clip->event) && strlen($clip->event) == 0) {
	$clip->event = NULL;
}
$year = array_key_exists('year', $_POST) ? strip_tags($_POST["year"]) : NULL;
$clip->year = NULL;
if ($year) {
	if (!ctype_digit($year)) {
		$err = 8;
	}
	else {
		$clip->year = intval($year);
	}
}
$GLOBALS["logger"]->debug ("year = " . $clip->year);
$clip->url = array_key_exists('clipurl', $_POST) ? strip_tags($_POST["clipurl"]) : NULL;
$file = array_key_exists('clipupload', $_FILES) ? $_FILES['clipupload'] : NULL;
if (strlen($clip->url) == 0)		// empty(str) has a stupid and unreliable definition
	$clip->url = NULL;
	
$GLOBALS["logger"]->debug ("clipurl = " . $clip->url);
if (is_null($file)) {
	$GLOBALS["logger"]->debug("file is null");
}
else {
	if (!$file["name"])
		$file = null;		// File object may be passed even if there's no file
}

/* Get the subdirectory for the user, creating it if necessary. */
function getUserSubdirectory () {
	global $AUDIO_DIR;
	$usr = $_SESSION['user'];
	$path = $AUDIO_DIR . $usr->id . '/';
	if (!file_exists($path) && !is_dir($path)) {
		mkdir ($path);
		chmod ($path, 0777);		// For some reason putting the mode in mkdir doesn't work
	}
	return $path;
}

/* Returns an error code if the request can't be accepted, otherwise -1 */
function processFile ($fil) {
	global $MAX_FILE_SIZE;
	global $ALLOWED_TYPES;
	if ($fil["size"] > $MAX_FILE_SIZE) {
		return 4;			// file too large
	}
	$targetBasename = basename($fil["name"]);
	if (preg_match("/[^\w _.]/", $targetBasename) ||
		preg_match("/^\./", $targetBasename))
		return 5;				// disallowed characters in name
	$targetFile = getUserSubdirectory() . $targetBasename;
	$GLOBALS["logger"]->debug("targetFile is " . $targetFile);
	if (file_exists($targetFile)) {
		return 6;				// file with same name exists
	}
	$audioFileType = pathinfo($targetFile,PATHINFO_EXTENSION);
	$typeOK = false;
	foreach ($ALLOWED_TYPES as $typ) {
		if ($typ == $audioFileType)
			$typeOK = true;
	}
	if (!$typeOK) {
		return 3;			// Wrong file type
	}
	if (!move_uploaded_file($fil["tmp_name"], $targetFile))
		return 7;			// Mysterious upload error
	
	return -1;				// OK
}

if ((is_null($clip->url) && is_null($file)) ||
			(!is_null($clip->url) && !is_null($file))) {
	$err = 2;
}
else if (is_null($clip->description)) {
	$err = 1;
}

if ($err <= 0 && $file) {
	$err = processFile($file);
	$usr = $_SESSION['user'];
	$clip->url = $URL_BASE . $usr->id . '/' . basename($file["name"]);
	$GLOBALS["logger"]->debug("clip URL is " . $clip->url);
}

if ($err > 0) {
	$GLOBALS["logger"]->debug ("User error adding clip: " . $err);
	header ("Location: addclip.php?err=" . $err, true, 302);
}
else {
	$clipId = $clip->insert();	
	$GLOBALS["logger"]->debug("Clip added");
	header ("Location: addclipok.php?id=$clipId", true, 302);
}
?>