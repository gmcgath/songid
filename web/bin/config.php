<?php
/* config.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
	/* Set the directory to the location of your
	   database config file. It needs to define the
	   variables $db_user, $db_pw, $db_name, and $db_host to 
	   the appropriate parameters for your MySQL
	   database. Keeping them out of your web directory
	   provides extra security.
	*/
	include ("/home/songid/dbconfig.php");
	
	/* Set your default time zone. */
	date_default_timezone_set("America/New_York");		// PHP style
	sqlTimeZone = "US/Eastern";							// sensible SQL style
?>