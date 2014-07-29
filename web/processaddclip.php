<?php
/*	processaddclip.php
	
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
if (!sessioncheck())
	return;


/* Open the database */
$mysqli = opendb();

$clip = new Clip;
$clip->description = strip_tags($mysqli->real_escape_string($_POST["clipdesc"]));
$clip->url = strip_tags($mysqli->real_escape_string($_POST["clipurl"]));
$clipId = $clip->insert($mysqli);
error_log("Clip added");
header ("Location: addclipok.php?id=$clipId", true, 302);
?>