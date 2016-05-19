<?php

	require_once('twilio-php/Services/Twilio.php');

	#########################################################

	function twilio_send_sms($number, $message){
		
		$sid = $GLOBALS['cfg']['twilio_account_sid'];
		$token = $GLOBALS['cfg']['twilio_api_token'];

		$client = new Services_Twilio($sid, $token);

		$from = $GLOBALS['cfg']['twilio_number'];
		$to = $number;
		$body = $message;

		try {
			$msg = $client->account->messages->sendMessage( $from, $to, $body);
			return $msg->sid;
		} catch (Services_Twilio_RestException $e) {
			return $e->getMessage();
		}

	}