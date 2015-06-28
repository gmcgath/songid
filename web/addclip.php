<?php
/* addclip.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

header("Content-type: text/html; charset=utf-8");

require_once('bin/config.php');
require_once('bin/model/user.php');
require_once('bin/model/clip.php');
require_once('bin/loggersetup.php');
require_once('bin/supportfuncs.php');

session_start();
require_once('bin/sessioncheck.php');

if (!sessioncheck())
	return;

$user = $_SESSION['user'];
if (!($user->hasRole(User::ROLE_EDITOR))) {
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
<p class="errormsg">
<?php
	$errcode = array_key_exists('err', $_GET) ? intval($_GET['err']) : -1;
	$errmsg = NULL;
	switch ($errcode) {
		case 1:
		$errmsg = "Please specify a description.";
		break;
		
		case 2:
		$errmsg = "Please specify a URL or upload a file (but not both).";
		break;
		
		case 3:
		$errmsg = "Only MP3 files are allowed.";
		break;
		
		case 4:
		$errmsg = "The file is bigger than the permitted size.";
		break;
		
		case 5:
		$errmsg = "The file name can have only letters, numbers, spaces, periods, and underscores " .
				"and may not start with a period.";
		break;
		
		case 6:
		$errmsg = "That file name is in use already.";
		break;
		
		case 7:
		$errmsg = "Upload error";
		break;
		
		default:
		break;
	}
	if ($errmsg) {
		echo ($errmsg);
	}
?>
</p>
<form action="processaddclip.php" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
<table class="editcliptable">
<tr>
<td class="formlabel;">Description:</td>
<td>
	<input type="text" maxlength="256" style="width:550px;" name="clipdesc" required autofocus
	title="Any descriptive text, maximum 256 characters">
</td>
</tr>

<tr>
<td class="formlabel;">URL:</td>
<td>
	<input type="url" style="width:550px;" name="clipurl" 
	title="The URL of the audio resource">
</td>
<td>
</tr>
<tr><td>
<b>OR</b>
</td>
</tr>
<tr>
<td>
<input type="file" name="clipupload" id="clipupload">
</td>
</tr>

<tr>
<td><input type="submit" id="submitbutton" class="submitbutton" value="Submit clip"></td>
<td>&nbsp;</td>
</tr>
</table>
</form>

	