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
$MAX_FILE_SIZE = 8 * 1024 * 1024;

if (!isset($AUDIO_DIR)) {
	$GLOBALS["logger"]->error("AUDIO_DIR is not defined");
	header ("Location: addclipok.php?id=$clipId", true, 500);
}

$URL_BASE = "audio/";

$clip = new Clip;
$clip->description = array_key_exists('clipdesc', $_POST) ? strip_tags($_POST["clipdesc"]) : NULL;
$clip->url = array_key_exists('clipurl', $_POST) ? strip_tags($_POST["clipurl"]) : NULL;
$file = array_key_exists('clipupload', $_FILES) ? $_FILES['clipupload'] : NULL;
if (strlen($clip->url) == 0)		// empty(str) has a stupid and unreliable definition
	$clip->url = NULL;
	
$err = -1;

$GLOBALS["logger"]->debug ("clipurl = " . $clip->url);
if (is_null($file)) {
	$GLOBALS["logger"]->debug("file is null");
}
else {
	$GLOBALS["logger"]->debug ("Dumping file");
	dumpVar($file);
}

/* Returns an error code if the request can't be accepted, otherwise -1 */
function processFile ($file) {
	global $MAX_FILE_SIZE;
	global $AUDIO_DIR;
	if ($file["size"] > $MAX_FILE_SIZE) {
		return 4;			// file too large
	}
	$target_basename = basename($file["name"]);
	$GLOBALS["logger"]->debug ("basename = " . $target_basename);
	if (preg_match("/[^\w _.]/", $target_basename) ||
		preg_match("/^\./", $target_basename))
		return 5;				// disallowed characters in name
	$target_file = $AUDIO_DIR . $target_basename;
	if (file_exists($target_file)) {
		return 6;				// file with same name exists
	}
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$GLOBALS["logger"]->debug ("imageFileType is " . $imageFileType);
	if ($imageFileType != "mp3") {
		return 3;			// Wrong file type
	}
	if (!move_uploaded_file($file["tmp_name"], $target_file))
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
	$clip->url = $URL_BASE . basename($file["name"]);
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