<?php
/*
   formsuccess.php
   
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
	<title>Successful submission</title>
</head>
<body>
<?php
$mysqli = opendb();
include ('menubar.php');
?>
<p>Thank you!</p>


</body>
</html>
