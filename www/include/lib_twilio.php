<?php

	require_once('twilio-php/Services/Twilio.php');

	loadlib("messages");
	loadlib("deliveries");

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

			
			### log the message in mysql
			
			$log = array(
				'MessageSid' => $msg->sid,
				'AccountSid' => $sid,
				'From' => $from, 
				'To' => $to,
				'Body' => $body
			);
			
			messages_create_message($log);

			return $msg->sid;
		} catch (Services_Twilio_RestException $e) {
			return $e->getMessage();
		}

	}

	#########################################################

	function twilio_send_object($out, $more){
		
		$sid = $GLOBALS['cfg']['twilio_account_sid'];
		$token = $GLOBALS['cfg']['twilio_api_token'];

		$client = new Services_Twilio($sid, $token);

		$from = $GLOBALS['cfg']['twilio_number'];
		$to = $more['To'];
		$body = $out['description'];
		
		$media = array();
		
		if (isset($more['media'])){
				$media = array($more['media']);
		}

		try {
			$msg = $client->account->messages->sendMessage( $from, $to, $body, $media);

			
			### log the message in mysql
			
			$log = array(
				'MessageSid' => $msg->sid,
				'AccountSid' => $sid,
				'From' => $from, 
				'To' => $to,
				'Body' => $body
			);
			
			messages_create_message($log);

			$log['object_id'] = $more['object_id'];
			
			deliveries_create_delivery($log);

			return $msg->sid;
		} catch (Services_Twilio_RestException $e) {
			return $e->getMessage();
		}

	}
