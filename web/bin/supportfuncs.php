<?php


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

	global $host, $usr, $pw, $db;
	
	/* Open the database */
	$mysqli = new mysqli($host, $usr, $pw, $db);
	if ($mysqli->connect_errno) {
        echo ("<p>Failed to connect to database: (" . 
        		$mysqli->connect_errno . ") " .
        		$mysqli->connect_error . "</p>");
        return false;
	} 
	return $mysqli;
}


?>