<?php

/* This PHP precedes all HTML, since it may need to redirect.
   In fact, it's probably better to have it be pure HTML and always redirect. */

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
