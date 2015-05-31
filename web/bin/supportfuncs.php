<?php
/* supportfuncs.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/model/user.php');
require_once (dirname(__FILE__) . '/loggersetup.php');
require_once (dirname(__FILE__) . '/lib/idiorm.php');

/* This function removes most HTML tags from text while allowing
   some basic formatting.
   To remove all tags, call strip_tags without a second argument.
   caller must include config.php (then we don't have to fuss with path-dependence)
    */
function strip_unsafe_html_tags( $text )
{
    return strip_tags( $text, "<p><b><i><em><strong><a><br>");
}

/* Open the database connection.  */
function opendb() {

	global $db_host, $db_user, $db_pw, $db_name;

	ORM::configure('mysql:host=' . $db_host . ';dbname=' . $db_name);
	ORM::configure('username', $db_user);
	ORM::configure('password', $db_pw);
	ORM::configure('return_result_sets', true); // returns result sets
	ORM::configure('logging', true);		// for debugging
}

/* This function (DELETED) can be called immediately after an INSERT to 
   retrieve the ID of the row just created. */
/* With Idiorm, you can call the id() method on the object just saved. */

/* Dump a variable to the PHP log. */
function dumpVar ($v) {
	ob_start();
	var_dump($v);
	$contents = ob_get_contents();
	ob_end_clean();
	$GLOBALS["logger"]->debug($contents);
}

/* Prepare a string (that's already sanitized) for SQL by replacing null
   with "NULL" and putting non-null arguments in single quotes. */
function sqlPrep ($s) {
	if (is_null($s)) {
		return 'NULL';
	} else if (is_bool($s)) {
		if ($s)
			return 'TRUE';
		else
			return 'FALSE';
	} else {
		return "'" . $s . "'";
	}
}



?>