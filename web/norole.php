<?php
/*
   norole.php
   
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
header("Content-type: text/html; charset=utf-8");

?>

<html>
<head>
	<meta charset="utf-8" />
	<link href="css/styles.css" rel="stylesheet">
	<title>You can't do that, Dave</title>
</head>
<body>
<?php
$mysqli = opendb();
include ('menubar.php');
?>
<p>You aren't allowed to do that from your account.</p>


</body>
</html>
