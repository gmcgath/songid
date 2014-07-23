<?php
/*	processdelreport.php
	
	July 22, 2014
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the "delete report" button is clicked. 
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

$reportId = $_GET["id"];
error_log("Deleting report $reportId");
if ($reportId != null && ctype_digit($reportId)) {
	$report = Report::findById($mysqli, $reportId);	
}
if ($report == NULL) {
	error_log("No such report: $reportId");
	header ("Location: error.php", true, 302);
	return;
}
error_log ("Deleting report");
$report->delete($mysqli);
error_log("Report deleted");
header ("Location: reports.php", true, 302);
?>