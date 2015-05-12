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

/* Open the database */
$mysqli = opendb();

$clip = new Clip;
$clip->description = strip_tags($mysqli->real_escape_string($_POST["clipdesc"]));
$clip->url = strip_tags($mysqli->real_escape_string($_POST["clipurl"]));
if (!is_null($clip->url)) {
	$clipId = $clip->insert($mysqli);
	$GLOBALS["logger"]->debug("Clip added");
	header ("Location: addclipok.php?id=$clipId", true, 302);
}
else {
	$GLOBALS["logger"]->debug("URL missing, clip not added");
	header ("Location: error.php", true, 302);
}
?>