<?php
/*
   editclipok.php
   
   July 22, 2014
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
header("Content-type: text/html; charset=utf-8");

include_once('bin/config.php');
include_once('bin/model/user.php');
include_once('bin/model/clip.php');
include_once('bin/supportfuncs.php');
session_start();
include_once('bin/sessioncheck.php');

$mysqli = opendb();
?>

<html>
<head>
	<meta charset="utf-8" />
	<link href="css/styles.css" rel="stylesheet">
	<title>Clip saved</title>
</head>
<body>
<?php
include ('menubar.php');

$clipid = $_GET[id];
if (!ctype_digit ($clipid))
	$clipid = "";		// defeat dirty tricks
$clip = Clip::findById($mysqli, $clipid);

if (is_null($clip)) {
	echo ("<p class='errormsg'>Clip not found.</p>\n");
	return;
}
?>
<h1>Clip modified</h1>
<ul class="nobullet">
<li><b>Description: </b>
<?php
echo ($clip->description);
?>
</li>
<li><b>URL:</b> 
<?php
echo ($clip->url);
?>
</li>
<li><b>Date:</b> 
<?php
echo ($clip->date);
?>
</li>
</ul>

<button type='button' onclick='location.href="cliplist.php"'>Back to clip list</button>

</body>
</html>

