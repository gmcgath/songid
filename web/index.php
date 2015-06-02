<?php
/*	index.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* This is the home PHP page. It redirects to login or cliplist.  */

require_once ('bin/config.php');
require_once ('bin/supportfuncs.php');

require_once('bin/model/user.php');

/* Open the database */
session_start();
require_once('bin/sessioncheck.php');

if (isSessionActive())
	header ("Location: cliplist.php", true, 302);
else
	header ("Location: login.php", true, 302);