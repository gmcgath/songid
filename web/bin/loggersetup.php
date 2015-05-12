<?php
/* loggersetup.php

	Configuration for Katzgrau Logger.   

   Copyright 2015 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

/* Require this file in every PHP file that does logging. */

require ('bin/config.php');
require ('vendor/autoload.php');
use Psr\Log\LogLevel;

date_default_timezone_set('America/New_York');

$logger = new Katzgrau\KLogger\Logger($logdir, LogLevel::DEBUG);

function handlePHPErrors ($errno, $errstr) {
	global $logger;
	$logger->error ("Error reported, errno = " . $errno . ", errstr = " . $errstr);
}

set_error_handler ("handlePHPErrors");