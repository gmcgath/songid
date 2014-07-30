<?php
/*	processform.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This PHP page is called when the identification form is submitted. 
   It has no HTML and always redirects.  */

require_once ('bin/config.php');
require_once ('bin/supportfuncs.php');
require_once ('bin/model/report.php');
require_once ('bin/reportbuilder.php');

include_once('bin/model/user.php');
session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;


/* Open the database */
$mysqli = opendb();
	
$reportBuilder = new ReportBuilder($mysqli);
try {
	$reportBuilder->populate($_POST, $_SESSION['user']);

	$_SESSION['report'] = $reportBuilder;

	/* TODO Put disambiguation checks here. If they're needed, redirect
	   appropriately and exit. */

	$reportBuilder->insert();
	$dochain = $_POST["dochain"];
	if ($dochain)
		header ("Location:idform.php?id={$_POST['clipid']}&previd={$reportBuilder->report->id}", true, 302);
	else
		header ("Location: formsuccess.php", true, 302);
} catch (Exception $e) {
	// Only our own exceptions, with numeric codes, will turn into useful messages.
	$newloc = "idform.php?id=" . $_POST["clipid"] . "&err=" . $e->getMessage();
	header ("Location: $newloc", true, 302);
	return;
}

?>
