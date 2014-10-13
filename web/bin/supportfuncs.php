<?php
/* supportfuncs.php

   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/

require_once (dirname(__FILE__) . '/config.php');
require_once (dirname(__FILE__) . '/model/user.php');

/* This function removes most HTML tags from text while allowing
   some basic formatting.
   To remove all tags, call strip_tags without a second argument.
   caller must include config.php (then we don't have to fuss with path-dependence)
    */
function strip_unsafe_html_tags( $text )
{
    return strip_tags( $text, "<p><b><i><em><strong><a><br>");
}

/* Open the database connection. Returns a mysqli object if
   successful, Outputs a message and returns false if it fails. */
function opendb() {

	global $db_host, $db_user, $db_pw, $db_name;
	global $sqlTimeZone;
	
	/* Open the database */
	$mysqli = new mysqli($db_host, $db_user, $db_pw, $db_name);
	if ($mysqli->connect_errno) {
        echo ("<p>Failed to connect to database: (" . 
        		$mysqli->connect_errno . ") " .
        		$mysqli->connect_error . "</p>");
        return false;
	} 
	
	// Set the time zone. sqlTimeZone is set in config.php. WHY DOESN'T THIS WORK?
	$tzstmt = "SET time_zone = '$sqlTimeZone'";
	$res = $mysqli->query($tzstmt);
	return $mysqli;
}

/* This function can be called immediately after an INSERT to 
   retrieve the ID of the row just created. */
function getInsertId ($mysqli) {
	$selstmt = "SELECT LAST_INSERT_ID()";
	$res = $mysqli->query ($selstmt);
	if ($res) {
		$row = $res->fetch_row();
		if (is_null($row)) {
			return $row[0];
		}
	}
	return NULL;
}

/* Dump a variable to the error log. */
function dumpVar ($v) {
	ob_start();
	var_dump($v);
	$contents = ob_get_contents();
	ob_end_clean();
	error_log($contents);
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