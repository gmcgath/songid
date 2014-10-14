<?php
/* reports.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once('bin/model/user.php');
session_start();
include('bin/sessioncheck.php');
if (!sessioncheck())
	return;

header("Content-type: text/html; charset=utf-8");


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
	include_once ('bin/config.php');
	include_once ('bin/supportfuncs.php');
	include_once ('bin/model/clip.php');
	include_once ('bin/model/report.php');

	/* Open the database */
	$mysqli = opendb();

	/* Determine the range of reports to present */	
	// param m is start index, default 0
	$start = (int)$_GET["m"];
	if (is_null($start)) {
		$start = 0;
		if ($start < 0)
			$start = 0;
	}
	$hideprev = false;
	if ($start == 0) 
		$hideprev = true;
	
	// param n is end index, default 10
	$nstr = $_GET["n"];
	if (is_null ($nstr)) 
		$itemsPerPage = 10;
	else {
		$itemsPerPage = (int) $nstr;
		if ($itemsPerPage < 1)
			$itemsPerPage = 1;
	}
	$hidenext = false;
	
	// param clip says to just show for one clip, default all clips
	$clipid = $_GET["clip"];
	if (!ctype_digit($clipid))
		$clipid = NULL;
		
?>


<?php	
	include ('menubar.php');
?>	

<h1>Reports</h1>
<?php

	// Get one extra report so we know if there are more to come
	if ($clipid)
		$reports = Report::getReportsForClip ($mysqli, $clipid, $start, $itemsPerPage + 1);
	else
		$reports = Report::getReports ($mysqli, $start, $itemsPerPage + 1);
	if (count($reports) <= $itemsPerPage)
		$hidenext = true;
?>

<table class="reportnav">
<tr>
<td id="reportprev">
<?php
	/* Previous n */
	if (!$hideprev) {
		$prevm = $start - $itemsPerPage;
		if ($prevm < 0)
			$prevm = 0;
		echo ("<a href='reports.php?m=$prevm'>Previous</a>\n");
	}
?>
</td>
<td id="reportnext">
<?php
	/* Next n */
	if (!$hidenext) {
		$nextm = $start + $itemsPerPage;
		echo ("<a href='reports.php?m=$nextm'>Next</a>\n");
	}
?>
</td>
</tr>
</table>

<form action="exportcsv.php" method="post" accept-charset="UTF-8">
<input type='submit' class="submitbutton" value="Export CSV">
</form>


<?php
	if (count($reports) == 0) {
		echo ("<p class='notice'>No reports match your criteria</p>\n");
	}
	$reptidx = 0;
	foreach ($reports as $report) {
		if ($reptidx >= $itemsPerPage)
			break;		// omit last report if it's the extra one
		$reptidx++;
		echo ("<ul class='nobullet'>\n");
		$date = $report->date;
		echo ("<li><b>Date of report:</b> $date</li>\n");
		
		$ruser = $report->user;
		if ($ruser)
			$ruserName = $ruser->name;
		else
			$ruserName = "Unknown";
		echo ("<li><b>Submitted by:</b> $ruserName");
		if ($report->clip) {
			$clipDate = $report->clip->date;
			$clipDesc = $report->clip->description;
		}
		else {
			// Unknown or deleted clip
			$clipDate = "";
			$clipDesc = "Clip unavailable";
		}
		echo ("<li><b>Clip:</b> $clipDate $clipDesc\n");
		
		// The above should be the same in every report in the chain.
		// We loop through chained reports for the rest.
		$rpt = $report;
		while ($rpt != NULL) {
			if ($report->flagged != 0) {
				echo ("<li><span class='flagged'>Clip flagged as inappropriate. Please investigate.</span></li>\n");
			}
			$soundTypeStr = soundTypeString($rpt->soundType, $rpt->soundSubtype);
			echo ("<li><b>Type:</b> $soundTypeStr</li>\n");
			$performerTypeStr = Report::performerTypeToString($rpt->performerType);
			if (!is_null($performerTypeStr))
				echo ("<li><b>Performer type:</b> $performerTypeStr</b></li>\n");
			$soundType = $rpt->soundType;
			switch ($soundType) {
				case Report::SOUND_TYPE_PERFORMANCE:
					doPerformance($rpt);
					break;
				case Report::SOUND_TYPE_TALK:
					doTalk($rpt);
					break;
				case Report::SOUND_TYPE_OTHER:
					doNoise($rpt);
					break;
			}
			$rpt = $rpt->nextReport;
			if ($rpt) 
				echo ("<li>...</li>\n");
		}
		echo ("</ul>\n");
		$user = $_SESSION['user'];
		if ($user->hasRole(User::ROLE_EDITOR)) {
			echo ("<button type='button' onclick='location.href=\"processdelreport.php?id={$report->id}\"'>");
			echo ("Delete report</button>");
			echo ("</td>");
		}
		echo ("<p>&nbsp;</p><hr>\n");
	}
	
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
				$val = "other";
				break;
		}
		$val2 = Report::soundSubtypeToString($sndSubtype);
		if (!is_null($val2))
			$val = $val . ": " . $val2;
		return $val;
	}
	
	function doPerformance ($report) {
		error_log("doPerformance");
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
			
		$instruments = $report->instruments;
		if ($instruments != NULL) {
			echo ("<li><b>Instruments:</b>");
			echo ("<ul class='nobullet'>\n");
			foreach ($instruments as $instrument) {
				echo ("<li>{$instrument->name}</li>\n");
			}
			echo ("</ul></li>\n");
		}
	}
	
	function doTalk ($report) {
		error_log("doTalk");
		$performers = $report->performers;
		if ($performers != NULL) {
			echo ("<li><b>People talking:</b>");
			echo ("<ul class='nobullet'>\n");
			foreach ($performers as $performer) {
				echo ("<li>{$performer->name}</li>\n");
			}
			echo ("</ul></li>\n");
		}
	}
	
	function doNoise ($report) {
	
	}
	
?>

</body>
</html>