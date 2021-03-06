<?php

	require_once('twilio-php/Services/Twilio.php');

	loadlib("messages");
	loadlib("deliveries");
	loadlib("slack");

	#################################################################

	function sms_output_ok($rsp=array(), $more=array()){
		sms_output_send($rsp, $more);
	}

	#################################################################

	function sms_output_error($code=999, $msg='', $more=array()){

		$out = array('error' => array(
			'code' => $code,
			'error' => $msg,
		));

		$more['is_error'] = 1;

		### send to Slack ... 
		
		if (isset($more['sms'])){
			$slack = array(
				'channel' => '#ask',
				'username' => $more['from']
			);

			$number = substr($more['from'], 2);
			$msg = $more['sms'] . " - http://objectphone.cooperhewitt.org/admin/messages/" . $number;
			slack($msg, $slack);
		}

		sms_output_send($out, $more);
	}

	#################################################################

	function sms_output_help($msg){

		$out = array(
			'message' => $msg,
		);

		sms_output_send($out);
	}

	#################################################################

	function sms_output_send($rsp, $more=array()){
		
		$sid = $GLOBALS['cfg']['twilio_account_sid'];
		$token = $GLOBALS['cfg']['twilio_api_token'];

		$client = new Services_Twilio($sid, $token);

		$from = $GLOBALS['cfg']['twilio_number'];

		if (!isset($more['To'])){
			$to = request_str("From");
		} else {
			$to = $more['To'];
		}

		foreach ($rsp as $item) {
			if (isset($more['media'])){
				$body .= $item;
				$media = array($more['media']);
			} else {
				$body .= $item;
			}
		}

		if (isset($more['is_error'])){
			$body = $rsp['error']['error'];
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

			if (($more['method'] == "object") || ($more['method'] == "random")){
				$log['object_id'] = $more['object_id'];
				deliveries_create_delivery($log);
			}
			
			$msg->sid;
			exit();
		} catch (Services_Twilio_RestException $e) {
			$e->getMessage();
			exit();
		}
	}

	#################################################################

	# the end
