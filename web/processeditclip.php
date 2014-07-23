<?php
/*	processeditclip.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the clip editing form is submitted. 
   It has no HTML and always redirects.  */

include_once ('bin/config.php');
include_once ('bin/supportfuncs.php');
include_once ('bin/model/report.php');
include_once ('bin/reportbuilder.php');

include_once('bin/model/user.php');
session_start();
include('bin/sessioncheck.php');

/* Open the database */
$mysqli = opendb();

error_log("Getting clip");
$clipId = $_POST["id"];
if ($clipId != null && ctype_digit($clipId)) {
	$clip = Clip::findById($mysqli, $clipId);	
}
if ($clip == NULL) {
	error_log("Couldn't get clip $clipId");
	header ("Location: error.php", true, 302);
	return;
}
error_log ("Filling in clip");
$clip->description = strip_tags($mysqli->real_escape_string($_POST["clipdesc"]));
$clip->url = strip_tags($mysqli->real_escape_string($_POST["clipurl"]));
$clip->update($mysqli);
error_log("Clip updated");
header ("Location: editclipok.php?id=$clipId", true, 302);
?>