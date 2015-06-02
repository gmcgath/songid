<?php
/*	processdelreport.php
	
	July 22, 2014
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the "delete report" button is clicked. 
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


$reportId = $_GET["id"];
$GLOBALS["logger"]->debug("Deleting report $reportId");
if ($reportId != null && ctype_digit($reportId)) {
	$report = Report::findById($reportId);	
}
if ($report == NULL) {
	$GLOBALS["logger"]->info("No such report: $reportId");
	header ("Location: error.php", true, 302);
	return;
}
$report->delete();
$GLOBALS["logger"]->debug ("Report deleted");
header ("Location: reports.php", true, 302);
?>