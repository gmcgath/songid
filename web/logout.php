<?php
/*	logout.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

include_once('bin/model/user.php');
session_start();
session_destroy();
?>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Logged out</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>


<h1>Logged out</h1>
<p>
You are now logged out.
<p><a href="login.php">Log in</a></p>

</body>
</html>