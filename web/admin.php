<?php
/* admin.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   Page available only to administrators.
*/

header("Content-type: text/html; charset=utf-8");

include_once('bin/config.php');
include_once('bin/model/user.php');
include_once('bin/supportfuncs.php');
session_start();
include_once('bin/sessioncheck.php');
if (!sessioncheck())
	return;

$mysqli = opendb();
$user = $_SESSION['user'];
if (!($user->hasRole(User::ROLE_ADMINISTRATOR))) {
	header ("Location: norole.php", true, 302);
	return;
}

	include ('menubar.php');

/*  List all the users. Allow the admin to change their role or boot them out.
	Booting out consists of deleting the user from the users table.
	
	Should admins be immune to booting and/or demotion by other admins?
	Admin should at least not be able to shoot self.
*/


	$users = User::getAllUsers($mysqli);
?>
<html lang="en">
<head>
	<title>User administration</title>
	<link href="css/styles.css" rel="stylesheet">
</head>
<body>
<form action="updateusers.php" method="post">
<table class="editusertable">
<tr><th>Login name</th><th>Name</th><th>Contrib</th><th>Editor</th><th>Admin</th></tr>

<?php
	foreach ($users as $u) {
		//$hasAdmin = in_array(User::ROLE_ADMINISTRATOR, $u->roles);
		$hasAdmin = $u->hasRole(User::ROLE_ADMINISTRATOR);
		$hasEditor = $u->hasRole(User::ROLE_EDITOR);
		$hasContributor = $u->hasRole(User::ROLE_CONTRIBUTOR);
		echo("<input type='hidden' name='user{$u->id}' value='x'>");
		echo("<tr><td> {$u->loginId} </td><td> {$u->name} </td>");
		echo("<td><input type='checkbox' name='cont{$u->id}'"); 
		if ($hasContributor)
			echo (" checked");
		echo("></td>");
		echo("<td><input type='checkbox' name='edit{$u->id}'"); 		
		if ($hasEditor)
			echo (" checked");
		echo("></td>");
		echo("<td><input type='checkbox' name='admn{$u->id}'"); 
		if ($hasAdmin) {
			echo (" checked");
			if ($user->id == $u->id) {
				echo (" disabled");			// Don't allow user to take away own admin role
			}
		}
		echo("></td></tr>\n");

		// We need a hidden field for our own administrator status, since
		// the disabled checkbox won't be passed.
		if ($user->id == $u->id) {
			echo("<input type='hidden' name='admn{$u->id}' value='on'>");
		}
	}
?>
</table>
<p>&nbsp;
<p class="notice">Please review your changes carefully before submitting them.
</p>
<input type="submit" class="submitbutton" value="Submit changes">
</form>
</body>
</html>