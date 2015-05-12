<?php
/* cliplist.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   Users with the Contributor and Editor roles can view this page.
*/

header("Content-type: text/html; charset=utf-8");
require_once('bin/model/user.php');
session_start();
require_once ('bin/config.php');
require_once ('bin/loggersetup.php');
require_once ('bin/sessioncheck.php');
if (!sessioncheck())
	return;

?>

<html lang="en">
<head>
	<title>List of Clips</title>
	<link href="css/styles.css" rel="stylesheet">
</head>

<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>
<?php
	include_once ('bin/supportfuncs.php');
	include_once ('bin/model/clip.php');
	
	$user = $_SESSION['user'];
	
	/* Open the database */
	$mysqli = opendb();
	$user = $_SESSION['user'];
	if (!($user->hasRole(User::ROLE_CONTRIBUTOR)) && !($user->hasRole(User::ROLE_EDITOR))) {
		header ("Location: norole.php", true, 302);
		return;
	}
	include ('menubar.php');
?>

<h1>Available sound clips</h1>	

<?php
	$onlyUnreported = false;
	if ($_GET["unrep"])
		$onlyUnreported = true;
	
	if ($user->hasRole(User::ROLE_EDITOR)) {
		echo ("<button type='button' onclick='location.href=\"addclip.php\"'>Add new clip</button>\n");
		echo ("<p>&nbsp;</p>\n");
	}
	
	echo ("<div>\n");
	if ($onlyUnreported) {
		echo ("<button type='button' onclick='location.href=\"cliplist.php\"'>Show all clips</button>\n");
	}
	else {
		echo ("<button type='button' onclick='location.href=\"cliplist.php?unrep=1\"'>Show clips without reports</button>\n");
	}
	echo ("</div>\n");
	
	try {
		$clips = Clip::getRows ($mysqli, 0, 100, $onlyUnreported);
		// TODO paginate
		echo ("<table class='cliptable'>\n");
		
		foreach ($clips as $clip) {
			$desc = $clip->description;
			if (strlen($desc) > 60 )
				$desc = substr($desc, 0, 59) . "...";
			echo ("<tr><td style='width:500px;'>");
			echo ($desc);		
			echo ("</td>");

			echo ("<td><a href='idform.php?id=");
			echo ($clip->id);
			echo ("'>");
			echo ("Create report</a>&nbsp;</td>\n");
			
			echo ("<td><a href='reports.php?clip={$clip->id}'>");
			echo ("View reports</a>&nbsp;</td>\n");
			
			if ($user->hasRole(User::ROLE_EDITOR)) {
				echo ("<td><a href='editclip.php?id=");
				echo ($clip->id);
				echo ("'>");
				echo ("Edit&nbsp;");
				echo ("</a></td>");
			}
			echo ("</tr>\n");
		}
		echo ("</table>\n");
	}
	catch (Exception $e) {
		
		$GLOBALS["logger"]->error($e->getMessage());
		echo ("<p>Sorry, an error occurred.</p>");
	}
?>
</body>
</html>