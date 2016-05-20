<?php

	loadlib("cooperhewitt_api");
	loadlib("deliveries");

	#################################################################

	function objects_send_random_object($user){

		$out = array();

		$args = array(
			"access_token" => $GLOBALS['cfg']['cooperhewitt_api_access_token'],
			"has_image" => "YES",
		);

		$rsp = cooperhewitt_api_call("cooperhewitt.objects.getRandom", $args);

		$data = json_decode($rsp['body'], 'as hash');
		
		if ($data['stat'] != 'ok'){
			#sms_output_error(404, "Cant get no...");
		}

		$out['description'] = $data['object']['title'] . "\n" . $data['object']['url'];

		$more = array();

		$more['To'] = $user['phone'];

		$more['media'] = $data['object']['images'][0]['n']['url'];
		$more['method'] = "random";
		$more['object_id'] = $data['object']['id'];

		#sms_output_ok($out, $more);

		return twilio_send_object($out, $more);

	}