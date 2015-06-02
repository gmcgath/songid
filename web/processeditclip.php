<?php
/*	processeditclip.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the clip editing form is submitted. 
   It has no HTML and always redirects.  */

require_once ('bin/config.php');
require_once ('bin/supportfuncs.php');
require_once ('bin/model/report.php');
require_once ('bin/reportbuilder.php');
require_once ('bin/loggersetup.php');
require_once ('bin/model/user.php');

session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;


$GLOBALS["logger"]->debug("Getting clip");
$clipId = $_POST["id"];
if ($clipId != null && ctype_digit($clipId)) {
	$clip = Clip::findById($clipId);	
}
if ($clip == NULL) {
	#logger->info("Couldn't get clip $clipId");
	header ("Location: error.php", true, 302);
	return;
}
$clip->description = strip_tags($_POST["clipdesc"]);
$clip->url = strip_tags($_POST["clipurl"]);
$clip->update();
header ("Location: editclipok.php?id=$clipId", true, 302);
?>