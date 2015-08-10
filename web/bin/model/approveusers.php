<?php
/* approveusers.php

   Copyright 2015 by Gary McGath.
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
if (!sessioncheck()) {
	return;
}

$user = $_SESSION['user'];
if (!($user->hasRole(User::ROLE_SUPERUSER))) {
	header ("Location: norole.php", true, 302);
	return;
}

include ('menubar.php');

$users = User::getRolelessUsers();

?>

<html lang="en">
<head>
	<title>User Management</title>
	<link href="css/styles.css" rel="stylesheet">
</head>
<body>
<p>The following users are pending approval.</p>
<form action="updateapprovedusers.php" method="post">

<table>
<?php
foreach ($users as $user) {
?>
<tr>

</tr>
<?php
}
?>
</table>