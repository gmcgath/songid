<?php
/*	idform.php
	
   Copyright 2014 by Gary McGath.
   This code is made available under the MIT license.
   See README.txt in the source distribution.
*/
header("Content-type: text/html; charset=utf-8");

include_once ('bin/config.php');
include_once ('bin/supportfuncs.php');
include_once ('bin/model/clip.php');
include_once('bin/model/user.php');
include_once('bin/model/instrument.php');
include_once('bin/orchestra.php');

session_start();
include('bin/sessioncheck.php');

/* This function fills in the instrument portion of the form */
function doInstruments () {
	global $mysqli;
	echo ('<ul id="instrumentnames" class="hidden nobullet">' . "\n");
	// Get all the instruments from the database
	$orch = new Orchestra ($mysqli);
	$orch->assemble();
	
	foreach ($orch->sections as $section ) {
		// Start section
		error_log ("Starting a section");
		echo ("<li>\n");
			$sectionName = $section->category->name;
			// id and name attrs are section name, minus white space,
			// plus "present"
			$sectionLabel = preg_replace('/\s+/', '', $sectionName) . "present";
			$sectionListLabel = preg_replace('/\s+/', '', $sectionName) . "list";
			echo ("<input type='hidden' name='sectionids[]' value='$sectionLabel'>");
			echo ("<input type='checkbox' id='$sectionLabel' name='$sectionLabel' " .
				"class='sectioncheckbox' " .
				"value='yes' onclick='trackTypeUpdate();'>\n");
			echo ("<label for='$sectionLabel'>$sectionName</label>\n");
			
			// List of instruments
			echo ("<ul id='$sectionListLabel' class='hidden instlist'>\n");
			foreach ($section->instruments as $instrument) {
				// to submit instrument names in the form, we have a hidden
				// field, with the repeatable name of "instrumentids[]" and
				// the value "inst" concatenated with the instrument id.
				// The form processor will collect all the possible instrument
				// names, and then look for the subset which are names passed by
				// checkboxes.
				$instname = "inst" . $instrument->id;
				echo ("<li>\n");
				echo ("<input type='hidden' name='instrumentids[]' value='$instname'>\n");
				echo ("<input type='checkbox' id='$instname' name='$instname' value='yes'>\n");
				echo ("<label for='$instname'>{$instrument->name}</label>\n");
				echo ("</li>\n");
			}
			echo ("</ul>");		// end list of instruments
		echo ("</li>\n");		// End of section
		echo ('<li style="clear:both"> </li>' . "\n");		// Force next item to new line
	}
	
	echo ("</ul>\n");
}
?>

<html lang="en">
<head>
	<title>Identify Clip</title>
	<link href="css/styles.css" rel="stylesheet">
	
</head>
<body>
<noscript><strong>Sorry, JavaScript is required.</strong>
</noscript>

<?php
	/* Open the database */
	$mysqli = opendb();

	
	$id = $_GET["id"];
	if (!ctype_digit ($id))
		$id = "";		// defeat dirty tricks
	$clip = Clip::findById($mysqli, $id);
	if (is_null($clip)) {
		echo ("<p class='bg-warning'>Clip not found.</p>\n");
		return;
	}
		
	include ('menubar.php');
?>
<audio controls>
	<source src=
<?php
	echo ('"' . $clip->url . '"');
?>

		type="audio/mpeg">
<p>Sorry, your browser does not support the audio element.</p>
</audio>
<p class="audiocaption">
<?php
	echo $clip->description;
?>
</p>
<form action="processform.php" method="post" accept-charset="UTF-8">
<h1>What can you hear?</h1>
<input type="hidden" name="clipid" value=
<?php
	echo ("'" . $clip->id . "'>");
?>
<ul class="nobullet" title="Select the broad category the clip falls into">
<li><input type="radio" id="trackperformance" 
		name="tracktype" value="performance"
		onclick="trackTypeUpdate();">
	<label for="trackperformance">Performance</label></li>
<li><input type="radio" id="tracktalk" 
		name="tracktype" value="talk"
		onclick="trackTypeUpdate();">
	<label for="tracktalk">Speech</label></li>
<li><input type="radio" id="tracknoise" 
		name="tracktype" value="noise" 
		onclick="trackTypeUpdate();">
	<label for="tracknoise">Other</label></li>
</ul>

<div id="talk" class="hidden">
<h4>Talk</h4>
<ul class="nobullet" title="Select the talk type">
<li><input type="radio" name="talktype" id="talktype_annc" value="talktype_annc">
	<label for="talktype_annc">Announcement</label></li>
<li><input type="radio" name="talktype" id="talktype_conv" value="talktype_conv">
	<label for="talktype_conv">Conversation</label></li>
<li><input type="radio" name="talktype" id="talktype_auction" value="talktype_auction">
	<label for="talktype_auction">Auction</label></li>
<li><input type="radio" name="talktype" id="talktype_songid" value="talktype_songid">
	<label for="talktype_songid">Song identification</label></li>
<li><input type="radio" name="talktype" id="talktype_other" value="talktype_other">
	<label for="talktype_other">Other</label></li>
</ul>


<ul class="nobullet">
<li>&nbsp;</li>
<li><input type="checkbox" id="canidtalk" name="canidtalk" value="yes"
		onclick="trackTypeUpdate();">
	<label for="canidtalk">I can identify the people talking</label></li>
<li class="hidden" id="lipeopletalking">
	Name(s): 
	<ul class="nobullet">
		<li>
		<input class="textbox" type="text" name="peopletalking[]" >
		<button type="button" onclick="addnameinput(this);">+</button>
		<button type="button" onclick="removenameinput(this);">-</button>
		</li>
		<li>&nbsp;</li>	
	</ul>
</li>
</ul>

</div>	<!-- talk -->



<div id="performance" class="hidden">
<h4>Performance</h4>
<ul class="nobullet" title="Select the performance type">
<li><input type="radio" name="performancetype" id="perftype_song" value="perftype_song">
	<label for="perftype_song">Song</label></li>
<li><input type="radio" name="performancetype" id="perftype_medley" value="perftype_medley">
	<label for="perftype_medley">Medley</label></li>
<li><input type="radio" name="performancetype" id="perftype_instrumental" value="perftype_instrumental">
	<label for="perftype_instrumental">Instrumental</label></li>
<li><input type="radio" name="performancetype" id="perftype_spoken" value="perftype_spoken">
	<label for="perftype_spoken">Spoken word or shtick</label></li>
<li><input type="radio" name="performancetype" id="perftype_other" value="perftype_other">
	<label for="perftype_other">Other</label></li>
</ul>

<ul class="nobullet">
<li><input type="radio" name="performertype" id="perf_single" value="solo"
		onclick="trackTypeUpdate();">
	<label for="perf_single">Solo performer</label>
<div id="singleperformertype" class="hidden">
<ul class="nobullet" title="Select the performer's gender">
<li><input type="radio" id="singleperformermale" name="singleperformersex" value="male">
<label for="singleperformermale">Male</label></li>
<li><input type="radio" id="singleperformerfemale" name="singleperformersex" value="female">
<label for="singleperformerfemale">Female</label></li>
<li><input type="radio" id="singleperformerunknown" name="singleperformersex" value="unknown">
<label for="singleperformerunknown">Can't tell gender or N/A</label></li>
</ul>
&nbsp;<br>
</div>	<!-- singleperformertype -->
</li>
<li><input type="radio" name="performertype" id="perf_group" value="group"
	onclick="trackTypeUpdate();">
	<label for="perf_group">Group performance</label>
<div id="groupperformertype" class="hidden">
<ul class="nobullet" title="Select the group type">
<li><input type="radio" id="groupperformermale" name="groupperformersex" value="male">
<label for="groupperformermale">Male performers</label></li>
<li><input type="radio" id="groupperformerfemale" name="groupperformersex" value="female">
<label for="groupperformerfemale">Female performers</label></li>
<li><input type="radio" id="groupperformermixed" name="groupperformersex" value="mixed">
<label for="groupperformermixed">Mixed group</label></li>
<li><input type="radio" id="groupperformerunknown" name="groupperformersex" value="unknown">
<label for="groupperformerunknown">Can't tell group type</label></li>
</ul>
&nbsp;<br>
</div>	<!-- groupperformertype -->
</li>

<li><input type="radio" id="performertypeunknown" name="performertype" value="unknown"
		onclick="trackTypeUpdate();">
	<label for="performertypeunknown">Can't tell if solo or group</label></li>
<li>&nbsp;</li>
<li><input type="checkbox" id="canidsong" name="canidsong" value="yes"
		onclick="trackTypeUpdate();">
	<label for="canidsong">I can identify this song</label></li>
<li class="hidden" id="lisongtitle">
	Title:<br>
	<input class="textbox" type="text" name="songtitle"></li>
	
<li><input type="checkbox" id="canidperformer" name="canidperformer" value="yes"
		onclick="trackTypeUpdate();">
	<label for="canidperformer">I can identify the performer(s)</label></li>
<li class="hidden" id="liperformername">
	Name(s): 
	<ul class="nobullet">
		<li class="performernameitem">
		<input class="textbox" type="text" name="performernames[]">
		<button type="button" onclick="addnameinput(this);">+</button>
		<button type="button" onclick="removenameinput(this);">-</button>
		</li>
		<li>&nbsp;</li>
	</ul>
</li>	<!-- idperformername -->

<li><input type="checkbox" id="instrumentspresent" name="instrumentspresent" value="yes"
		onclick="trackTypeUpdate();">
	<label for="instrumentspresent">One or more instruments are present</label></li>
<?php
	// Fill in the instrument part of the form
	doInstruments();
?>

</li>	<!-- instrumentspresent -->
</div> <!-- performance -->

<div id="noise" class="hidden">
<h4>Other (noise, silence, etc.)</h4>
<ul class="nobullet" title="Select the sound type">
<li><input type="radio" name="noisetype" id="noisetype_setup" value="noisetype_setup">
	<label for="noisetype_setup">Setup</label></li>
<li><input type="radio" name="noisetype" id="noisetype_silence" value="noisetype_silence">
	<label for="noisetype_silence">Silence</label></li>
<li><input type="radio" name="noisetype" id="noisetype_other" value="noisetype_other">
	<label for="noisetype_other">Other</label></li>
</ul>

</div>	<!-- noise -->


<li><input type="submit" class="submitbutton" value="Submit"></li>

</ul>


</form>



<!-- Put scripts at end for faster load -->

<script type="text/JavaScript"
src="http://code.jquery.com/jquery-1.11.1.js">
</script>
<script type="text/JavaScript">
$(document).ready(
	function () {
		trackTypeUpdate();
	});

function trackTypeUpdate() {
	if ($('#trackperformance').is(':checked')) {
		$('#performance').show();
		if ($('#perf_single').is(':checked')) 
			$('#singleperformertype').show();
		else
			$('#singleperformertype').hide();

		if ($('#perf_group').is(':checked')) 
			$('#groupperformertype').show();
		else
			$('#groupperformertype').hide();
		
		if ($('#canidperformer').is(':checked'))
			$('#liperformername').show();
		else
			$('#liperformername').hide();

		if ($('#canidsong').is(':checked'))
			$('#lisongtitle').show();
		else {
			$('#lisongtitle').hide();
			$('#lisongtitle').val("");
		}
		
		if ($('#instrumentspresent').is(':checked')) {
			$('#instrumentnames').show();
			$('.sectioncheckbox').each (function () {
				if ($(this).is(':checked'))
					$(this).parent().find('.instlist').show();
				else
					$(this).parent().find('.instlist').hide();
			});
		}
		else
			$('#instrumentnames').hide();
	}
	else 
		$('#performance').hide();

	if ($('#tracktalk').is(':checked')) {
		$('#talk').show();
		if ($('#canidtalk').is(':checked')) 
			$('#lipeopletalking').show();
		else
			$('#lipeopletalking').hide();
	}
	else 
		$('#talk').hide();

	if ($('#tracknoise').is(':checked')) {
		$('#noise').show();
	}
}

/* Add a text input for people talking */
function addnameinput (buttn) {
	/* The argument is the button whose parent needs to be cloned */
	var litem = $(buttn).parent();
	var newitem = litem.clone();
	newitem.find("input").val("");
	litem.after(newitem);
}

/* Remove a text input for people talking */
function removenameinput (buttn) {
	var litem = $(buttn).parent();
	var list = litem.parent();
	// Don't delete last item!
	if (list.find(".performernameitem").length > 1)
		litem.remove();
	
}

</script>


</body>
</html>
