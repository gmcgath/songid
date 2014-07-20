<?php
/* cliplist.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include('bin/model/user.php');
session_start();
include('bin/sessioncheck.php');
?>

<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Select Track</title>
	<meta name="generator" content="BBEdit 10.5" />
	<link href="css/styles.css" rel="stylesheet">
</head>

<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>
<?php
	include ('menubar.php');
?>

<h1>Available sound clips</h1>	

<?php
	include_once ('bin/config.php');
	include_once ('bin/supportfuncs.php');
	include_once ('bin/model/clip.php');
	/* Open the database */
	$mysqli = opendb();
	try {
		$clips = Clip::getRows ($mysqli, 0, 100);
		echo ("<table class='cliptable'>\n");
		
		foreach ($clips as $clip) {
			echo ("<tr>");
			echo ("<td><a href='idform.php?id=");
			echo ($clip->id);
			echo ("'>");
			echo ($clip->description);			
			echo ("</a></td>");
			echo ("</tr>\n");
		}
		echo ("</table>\n");
	}
	catch (Exception $e) {
		echo ("<p>There was a problem.</p>\n");
		error_log ($e->getMessage());
	}
?>
</body>
</html>