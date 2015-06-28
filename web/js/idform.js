/**
 * 
 * 
 * idform.js 
 * 
 *  Copyright 2014 by Gary McGath.
 *  This code is made available under the MIT license.
 *  See README.txt in the source distribution.
 */

$(document).ready(
	function () {
		trackTypeUpdate();
		/* Display notification if the audio fails to load.
		   Attach this to both audio and source for best compatibility. */
		$("#audio, #audiosrc").on("error", function () {
			$("#audioerror").css("display", "block");
		});
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
	} else {
		$('#noise').hide();
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

/* Set the value of the dochain field when the submit and continue button
   is clicked */
function chainOn () {
	$('#dochain').attr("value", "yes");
}

/* Clear the value of the dochain field when the plain submit button
   is clicked */
function chainOff () {
	$('#dochain').attr("value", "");
}
