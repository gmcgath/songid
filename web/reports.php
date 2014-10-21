<?php
/* reports.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   Users with the Contributor and Editor roles can view this page.
   
*/

require_once('bin/model/user.php');
session_start();
require_once('bin/sessioncheck.php');
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
	<script type="text/JavaScript"
		src="http://code.jquery.com/jquery-1.11.1.js">
	</script>
	<script type="text/JavaScript"
		src="//code.jquery.com/ui/1.11.1/jquery-ui.js">
	</script>
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>

<?php
	require_once ('bin/config.php');
	require_once ('bin/supportfuncs.php');
	require_once ('bin/model/clip.php');
	require_once ('bin/model/report.php');

	/* Open the database */
	$mysqli = opendb();
	$user = $_SESSION['user'];
	if (!($user->hasRole(User::ROLE_CONTRIBUTOR)) && !($user->hasRole(User::ROLE_EDITOR))) {
		header ("Location: norole.php", true, 302);
		return;
	}

	/* Determine the range of reports to present */	
	// param m is start index, default 0
	$start = (int)$_GET["m"];
	if (is_null($start)) {
		$start = 0;
		if ($start < 0)
			$start = 0;
	}

	$hideprev = ($start == 0);
	
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
	if ( !ctype_digit($clipid) ) {
		$clipid = NULL;
	}
	
	// Get parameters for report date range. Format is yyyymmdd
	$stdt = $_GET["stdt"];
	$endt = $_GET["endt"];
	$startDate = new ShortDate("20140701");		// The beginning of the world to us
	if ($stdt) {
		try {
			$startDate = new ShortDate($stdt);
		} catch (Exception $ex) {
		}
	}
	$endDate = new ShortDate(null);
	if ($endt) {
		try {
			$endDate = new ShortDate($endt);
		} catch (Exception $ex) {
		}
	}
		
	include ('menubar.php');
?>	

<h1>Reports</h1>

<?php

	// Get one extra report so we know if there are more to come
	if ($clipid)
		$reports = Report::getReportsForClip ($mysqli, $clipid, $start,
				$itemsPerPage + 1, 
				$startDate,
				$endDate);
	else
		$reports = Report::getReports ($mysqli, $start, 
				$itemsPerPage + 1,
				$startDate,
				$endDate);
	if (count($reports) <= $itemsPerPage)
		$hidenext = true;

	/* Calculate previous n */
	if (!$hideprev) {
		$prevm = $start - $itemsPerPage;
		if ($prevm < 0)
			$prevm = 0;
	}

	/* Calculate next n */
	if (!$hidenext) {
		$nextm = $start + $itemsPerPage;
	}
?>

<form action="reports.php" method="get" accept-charset="UTF-8">
<div style="background-color:#D0D0E0">
<table><tr class="datepicker">
<td>Start date:
<span id="startpicker" class="ui-datepicker ui-datepicker-inline"></span></td>
<td>End date:
<span id="endpicker" class="ui-datepicker ui-datepicker-inline"></span></td>

<input type="hidden" id="stdt" name="stdt">
<input type="hidden" id="endt" name="endt">
<input type="hidden" id="mvalue" name="m">
</tr>
</table>
<table>
<td class="reportnavtd">
<?php
if (! $hideprev ) {
?>
<input type="submit" class="submitbutton" value="Previous" onclick="submitPrev();">
<?php
}
?>
</td>
<td class="reportnavtd">
<input type="submit" class="submitbutton" value="Refresh">
</td>
<td class="reportnavtd">
<?php
if ( !$hidenext ) {
?>
	<input type="submit" class="submitbutton" value="Next" onclick="submitNext();">
<?php
}
?>
</td>
</table>
</form>
</div>
<div style="font-size:20pt;">&nbsp;</div>

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
<script type="text/JavaScript">
$( "#startpicker" ).datepicker({dateFormat: 'yymmdd', 
	inline: true,
	onSelect: function(dateText, inst) {
		$('#stdt').val(dateText);
	}
	});

/* Set the calendar widget dates. Datepicker months are zero-based, 
   so we have to adjust for that. */
$( "#startpicker" ).datepicker("setDate", new Date(
<?php
	echo ("'" . $startDate->year . "'");
?>
	,
<?php
	echo ("'" . ($startDate->month - 1) . "'");
?>
	,
<?php
	echo ("'" . $startDate->day . "'");
?>
	));

$( "#endpicker" ).datepicker({dateFormat: 'yymmdd', 
	inline: true,
	onSelect: function(dateText, inst) {
		$('#endt').val(dateText);
	}
	});
$( "#endpicker" ).datepicker("setDate", new Date(
<?php
	echo ("'" . $endDate->year . "'");
?>
	,
<?php
	echo ("'" . ($endDate->month - 1) . "'");
?>
	,
<?php
	echo ("'" . $endDate->day . "'");
?>
	));

/* The "Next" button calls this as an onclick function to fill the
   appropriate hidden fields. */
function submitPrev() {
	$('#mvalue').val(
<?php
		echo ('"' . $prevm . '"');
?>
	);
}

/* The "Previous" button calls this as an onclick function to fill the
   appropriate hidden fields. */
function submitNext() {
	$('#mvalue').val(
<?php
		echo ('"' . $nextm . '"');
?>
	);
}

/* Initialize values for hidden date fields to the values we received */
$('#stdt').val(
<?php
	echo ("'" . $startDate->toShortString() . "'");
?>
);
$('#endt').val(
<?php
	echo ("'" . $endDate->toShortString() . "'");
?>
);

</script>

</body>
</html>