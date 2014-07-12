<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Select Track</title>
	<meta name="generator" content="BBEdit 10.5" />
	<link href="css/styles.css" rel="stylesheet">
<?php
	include ('bootstrap/bootstraphead.php');
?>
</head>

<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>
<?php
	include_once ('bin/config.php');
	include_once ('bin/supportfuncs.php');
	include_once ('bin/model/clip.php');
	/* Open the database */
	$mysqli = opendb();
	if ($mysqli) {
        echo ("<p>Successful database connection</p>\n");
	}
	try {
		$clips = Clip::getRows ($mysqli, 0, 100);
		echo ("<table>\n");
		
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
		echo ("<p>" . $e->getMessage() . "</p>");
	}
?>

<?php
	include ('bootstrap/bootstraptail.php');
?>
</body>
</html>