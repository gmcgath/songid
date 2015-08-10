<?php

require_once('config.php');

class Mailer {
	var $sender;
	var $recipient;	
	
	public function __construct () {
		// Copy the "from" address from config.php
		$this->sender = $mailbot_from;
	}
	
	/* Set the recipients name and email address. We only support one recipient.
	   The name may be empty. */
	public function setRecipient ($name, $addr) {
		if (!$name) {
			$recip = $addr;
		}
		else {
			$recip = $name . ' <' . $addr . '>';
		}
		// Sanitize against line breaks and other control characters
		$this->recipient = preg_replace('/[\x00-\x1F\x7F]/', '', $recip);
	}
	
	public function setSubject ($s) {
		$this->subject = $s;
	}
	
	public function send ($msg) {
		$extraHeaders = "From: " . $this->sender;
		mail ($this->recipient, $this->subject, $msg
	}
}