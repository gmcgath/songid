<?php

/* addclip.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   This page is output as a CSV file of reports.
*/

	require_once ('bin/config.php');
	require_once ('bin/supportfuncs.php');
	require_once ('bin/export.php');
	require_once ('bin/model/clip.php');
	require_once ('bin/model/report.php');
	
	header("Content-type: text/csv; charset=utf-8");
	header('Content-Disposition: attachment; filename=reports.csv');

	$sequence = array (
		Export::EXPORT_CLIP_DESC,
		Export::EXPORT_CLIP_URL,
		Export::EXPORT_CLIP_DATE,
		Export::EXPORT_USER_NAME,
		Export::EXPORT_SOUND_TYPE,
		Export::EXPORT_PERFORMER_TYPE,
		Export::EXPORT_SONG_NAME,
		Export::EXPORT_SONG_NOTE,
		Export::EXPORT_SINGALONG,
		Export::EXPORT_PERFORMERS,
		Export::EXPORT_COMPOSERS,
		Export::EXPORT_INSTRUMENTS);
		
	/* Open the database */
	$mysqli = opendb();
	
	/* Set up the export */
	$reports = Report::getReports ($mysqli, -1, -1);		// All reports

	/* Create a file pointer for the output stream, so we can use fputcsv */
	$fh = fopen( 'php://output', 'w' );
	$exporter = new Export($reports, $sequence, $fh);
	$exporter->writeHeader();
	
	for (;;) {
		$line = $exporter->writeNextLine();
		if (is_null($line))
			break;
	}
?>