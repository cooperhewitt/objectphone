<?php

	#######################################################

	loadlib("sms_config");
	sms_config_init();

	#######################################################

	loadlib("sms_output");
	loadlib("messages");

	#######################################################

	function sms_dispatch($body){

		### log the message in mysql

		$message = array(
			'MessageSid' => request_str("MessageSid"),
			'AccountSid' => request_str("AccountSid"),
			'From' => request_str("From"),
			'To' => request_str("To"),
			'Body' => request_str("Body"),
			'NumMedia' => request_str("NumMedia"),
		);

		messages_create_message($message);

		# this is the part where we parse the body to find what you actually want me to do
		# start by just taking the first word

		$method_delim_pos = strpos($body, ' ');

		if ( $method_delim_pos === false) {
			$method = $body;
		} else {
			$method = substr($body, 0, $method_delim_pos);
		}

		$method = strtolower($method);
		
		$methods = $GLOBALS['cfg']['sms']['methods'];

		if ((! $method) || (! isset($methods[$method]))){
			sms_output_error(404, "Not sure I understand your meaning, but I'll get back to you soon as I can.");
		}

		$method_row = $methods[$method];

		if (! $method_row['enabled']){
			sms_output_error(404, "Not sure I understand your meaning, but I'll get back to you soon as I can.");
		}

		$method_row['name'] = $method;

	    loadlib($method_row['library']);

		$parts = explode(".", $method);
		$method = array_pop($parts);

		$func = "{$method_row['library']}_{$method}";

		if (! function_exists($func)){
			sms_output_error(404, "Not sure I understand your meaning, but I'll get back to you soon as I can.");
		}

		call_user_func($func);

		exit();

	}