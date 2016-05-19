<?php

	require_once('twilio-php/Services/Twilio.php');

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

		$ok = ($more['is_error']) ? 0 : 1;

		# make some twiml

		$twiml = new Services_Twilio_Twiml();

		if (isset($more['is_error'])){
			$twiml->message($rsp['error']['error']);
			print $twiml;
			exit();
		}

		foreach ($rsp as $item) {
			$twiml->message($item);
		}

		print $twiml;
		exit();
	}

	#################################################################

	# the end
