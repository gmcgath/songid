<?php
/* reports.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
?>
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

	$performerTypeText = array (
		NULL,
		"Solo, male",
		"Solo, female",
		"Solo, gender unspecified",
		"Group, male",
		"Group, female",
		"Group, mixed",
		"Group, gender unspecified"
	);
	
	/* Open the database */
	$mysqli = opendb();

	$reports = Report::getReports ($mysqli, 0, 50);
	
	foreach ($reports as $report) {
		echo ("<ul class='nobullet'>\n");
		$date = $report->date;
		echo ("<li><b>Date of report:</b> $date</li>\n");
		$clipDate = $report->clip->date;
		$clipDesc = $report->clip->description;
		echo ("<li><b>Clip:</b> $clipDate $clipDesc\n");
		$soundTypeStr = soundTypeString($report->soundType, $report->soundSubtype);
		echo ("<li><b>Type:</b> $soundTypeStr</li>\n");
		$performerTypeStr = $performerTypeText [$report->performerType];
		if (!is_null($performerTypeStr))
			echo ("<li><b>Performer(s):</b> $performerTypeStr</b></li>\n");
		$soundType = $report->soundType;
		if ($soundType == Report::SOUND_TYPE_PERFORMANCE) {
			$title = "(Not given)";
			if (!is_null($report->song)) 
				$title = $report->song->title;
			echo ("<li><b>Title:</b> $title</li>\n");
			$sngalng = $report->singalong ? "Yes" : "No";
			echo ("<li><b>Singalong:</b> $sngalng</li>\n");
			
			$performers = $report->performers;
			if ($performers != NULL) {
				// that's the "really has something" test
				echo ("<li><b>Performers:</b>");
				echo ("<ul class='nobullet'>\n");
				foreach ($performers as $performer) {
					echo ("<li>{$performer->name}</li>\n");
				}
				echo ("</ul></li>\n");
			}
		}
		echo ("</ul>\n");
		echo ("<p>&nbsp;</p><hr>\n");
	}
	
	/* Generate the sound type string */
		/* Return the sound type as a string. */
	function soundTypeString ($sndType, $sndSubtype) {
		$val = "";
		switch ($sndType) {
			case Report::SOUND_TYPE_PERFORMANCE:
				$val = "performance";
				break;
			case Report::SOUND_TYPE_TALK:
				$val = "talk";
				break;
			case Report::SOUND_TYPE_OTHER:
				$val = "noise or silence";
				break;
		}
		$val2 = NULL;
		switch ($sndSubtype) {
			case Report::SOUND_SUBTYPE_PRF_SONG:
				$val2 = "song";
				break;
			case Report::SOUND_SUBTYPE_PRF_MEDLEY:
				$val2 = "medley";
				break;
			case Report::SOUND_SUBTYPE_PRF_INST:
				$val2 = "instrumental";
				break;
			case Report::SOUND_SUBTYPE_PRF_SPOKEN:
				$val2 = "spoken or shtick";
				break;
			case Report::SOUND_SUBTYPE_PRF_OTHER:
				$val2 = "other";
				break;
			case Report::SOUND_SUBTYPE_TALK_ANNC:
				$val2 = "announcement";
				break;
			case Report::SOUND_SUBTYPE_TALK_CONV:
				$val2 = "conversation";
				break;
			case Report::SOUND_SUBTYPE_TALK_AUCTION:
				$val2 = "auction";
				break;
			case Report::SOUND_SUBTYPE_TALK_SONGID:
				$val2 = "song identification";
				break;
			case Report::SOUND_SUBTYPE_TALK_OTHER:
				$val2 = "other";
				break;
			case Report::SOUND_SUBTYPE_NOISE_SETUP:
				$val2 = "setup";
				break;
			case Report::SOUND_SUBTYPE_NOISE_SILENCE:
				$val2 = "silence";
				break;
			case Report::SOUND_SUBTYPE_NOISE_OTHER:
				$val2 = "other";
				break;
		}
		if (!is_null($val2))
			$val = $val . ": " . $val2;
		return $val;
		return $val;
	}
	
?>
</body>
</html>