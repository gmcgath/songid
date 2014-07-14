<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Reports</title>
	<meta name="generator" content="BBEdit 10.5" />
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>

<?php
	include ('menubar.php');
?>
<h1>Reports</h1>

<?php	
	include_once ('bin/config.php');
	include_once ('bin/supportfuncs.php');
	include_once ('bin/model/clip.php');
	include_once ('bin/model/report.php');

	/* Open the database */
	$mysqli = opendb();

	$reports = Report::getReports ($mysqli, 0, 50);
	
	foreach ($reports as $report) {
		echo ("<ul class='nobullet'>\n");
		$date = $report->date;
		echo ("<li><b>Date of report:</b> $date</li>\n");
		$soundTypeStr = $report->getSoundTypeAsString();
		echo ("<li><b>Type:</b> $soundTypeStr");
		$soundType = $report->soundType;
		if ($soundType == Report::SOUND_TYPE_PERFORMANCE) {
			$title = "(Not given)";
			if (!is_null($report->song)) 
				$title = $report->song->title;
			echo ("<li><b>Title:</b> $title</li>\n");
			$sngalng = $report->singalong ? "Yes" : "No";
			echo ("<li><b>Singalong:</b> $sngalng</li>\n");
		}
		echo ("</ul>\n");
		echo ("<p>&nbsp;</p><hr>\n");
	}
	
?>
</body>
</html>