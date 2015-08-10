<?php
/* admin.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
   
   Page available only to superusers.
*/

header("Content-type: text/html; charset=utf-8");

include_once('bin/config.php');
include_once('bin/model/user.php');
include_once('bin/supportfuncs.php');
session_start();
include_once('bin/sessioncheck.php');
if (!sessioncheck())
	return;

$selfUser = $_SESSION['user'];
if (!($selfUser->hasRole(User::ROLE_SUPERUSER))) {
	header ("Location: norole.php", true, 302);
	return;
}

	include ('menubar.php');

/*  List all the users. Allow the admin to change their role or boot them out.
	Booting out consists of deleting the user from the users table.
	
	Should admins be immune to booting and/or demotion by other admins?
	Admin should at least not be able to shoot self.
*/


	$users = User::getAllUsers();
?>
<html lang="en">
<head>
	<title>User Management</title>
	<link href="css/styles.css" rel="stylesheet">
</head>
<body>
<h1>Inactive users</h1>
<form action="updateusers.php" method="post">
<table class="editusertable">
<tr><th>Login name</th><th>Name</th><th>Activate</th><th>Info</th></tr>

<?php
	/* Do two loops, so that inactive users come before everyone else.
	   If there are no inactive users, say so */
	$inactiveCount = 0;
	foreach ($users as $u) {
		if ( $u->activated ) {
			continue;
		}
		$inactiveCount++;
		echo("<input type='hidden' name='user{$u->id}' value='x'>");
		echo("<tr><td> {$u->loginId} </td><td> {$u->name} </td>");
		echo("<td><input type='checkbox' name='actv{$u->id}' </td>");
		echo("<td>{$u->selfInfo}</td></tr>");
	}
	if ( $inactiveCount == 0) {
		echo("<td colspan='4'>No inactive users</td>\n");
	}
?>
</table>
<p style="padding:20px;">&nbsp;</p>
<h1>Active users</h1>
<table class="editusertable">
<tr><th>Login name</th><th>Name</th><th>Contrib</th><th>Admin</th><th>Stakeh.</th><th>Super</th></tr>
<?php
	/* Active users loop */
	foreach ($users as $u) {
		if ( !$u->activated ) {
			continue;
		}
		$hasSuper = $u->hasRole(User::ROLE_SUPERUSER);
		$hasAdmin = $u->hasRole(User::ROLE_ADMIN);
		$hasStake = $u->hasRole(User::ROLE_STAKEHOLDER);
		$hasContributor = $u->hasRole(User::ROLE_CONTRIBUTOR);
		echo("<input type='hidden' name='user{$u->id}' value='x'>");
		echo("<tr><td> {$u->loginId} </td><td> {$u->name} </td>");
		echo("<td style=;width:11%;'><input type='checkbox' name='cont{$u->id}'"); 
		if ($hasContributor)
			echo (" checked");
		echo("></td>");
		echo("<td style='width:11%;'><input type='checkbox' name='admi{$u->id}'"); 		
		if ($hasAdmin)
			echo (" checked");
		echo("></td>");
		echo("<td style='width:11%;'><input type='checkbox' name='stak{$u->id}'"); 		
		if ($hasStake)
			echo (" checked");
		echo("></td>");
		echo("<td style='width:11%;'><input type='checkbox' name='supr{$u->id}'"); 
		if ($hasSuper) {
			echo (" checked");
			if ($selfUser->id == $u->id) {
				echo (" disabled");			// Don't allow user to take away own admin role
			}
		}
		echo("></td></tr>\n");

		// We need a hidden field for our own super status, since
		// the disabled checkbox won't be passed.
		if ($selfUser->id == $u->id) {
			echo("<input type='hidden' name='supr{$u->id}' value='on'>");
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