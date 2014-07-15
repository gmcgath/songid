<?php
/*	processform.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the identification form is submitted. 
   It has no HTML and always redirects.  */

include_once ('bin/config.php');
include_once ('bin/supportfuncs.php');
include_once ('bin/model/report.php');
include_once ('bin/reportbuilder.php');

/* Open the database */
$mysqli = opendb();
	
$reportBuilder = new ReportBuilder($mysqli);
try {
	$reportBuilder->populate($_POST);

	session_start();
	$_SESSION['report'] = $reportBuilder;

	/* TODO Put disambiguation checks here. If they're needed, redirect
	   appropriately and exit. */

	$reportBuilder->insert();
	header ("Location: formsuccess.php", true, 302);
} catch (Exception $e) {
	header ("Location: error.php", true, 302);
	return;
}

?>
