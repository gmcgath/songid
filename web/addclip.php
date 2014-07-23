<?php
/* addclip.php

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
$user = $_SESSION['user'];
if (!($user->hasRole($mysqli, User::ROLE_EDITOR))) {
	header ("Location: norole.php", true, 302);
	return;
}
?>

<html lang="en">
<head>
	<title>Add clip</title>
	<link href="css/styles.css" rel="stylesheet">
</head>
<body>
<?php
	include ('menubar.php');
?>
<h1>Add clip</h1>
<form action="processaddclip.php" method="post" accept-charset="UTF-8">
<table class="editcliptable">
<tr>
<td class="formlabel;">Description:</td>
<td>
	<input type="text" style="width:550px;" name="clipdesc" required autofocus
	title="Any descriptive text, maximum 256 characters">
</td>
</tr>

<tr>
<td class="formlabel;">URL:</td>
<td>
	<input type="url" style="width:550px;" name="clipurl" required
	title="The URL of the audio resource">
</td>
</tr>

<tr>
<td><input type="submit" class="submitbutton" value="Submit clip"></td>
<td>&nbsp;</td>
</tr>
</table>
</form>

	