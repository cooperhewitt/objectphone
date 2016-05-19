<?php

	loadlib("cooperhewitt_api");
	loadlib("zendesk");

	#################################################################

	function sms_objects_object(){
	
		$out = array();

		$body = request_str("Body");
		$body = explode(' ', $body);

		if ( $body[1] == "help"){
			sms_output_help("Type the word object followed by a valid object ID.");
		}

		$object_id = $body[1];
		$extra = $body[2];

		### call getInfo with object ID

		$args = array(
			"object_id" => $object_id,
			"access_token" => $GLOBALS['cfg']['cooperhewitt_api_access_token'],
		);

		$rsp = cooperhewitt_api_call("cooperhewitt.objects.getInfo", $args);

		$data = json_decode($rsp['body'], 'as hash');
		
		if ($data['stat'] != 'ok'){
			sms_output_error(404, "Object not found");
		}

		$out['title'] = $data['object']['title'];

		if ($data['object']['description']){
			$out['object'] = $data['object']['description'];
		}

		if ( $extra ){
			$out['extra'] = $extra;
		}

		sms_output_ok($out);
	}

	#################################################################

	function sms_objects_random(){

		$body = request_str("Body");
		$body = explode(' ', $body);

		if ( $body[1] == "help"){
			sms_output_help("This just returns a random object.");
		}
	
		$out = array();

		$args = array(
			"access_token" => $GLOBALS['cfg']['cooperhewitt_api_access_token'],
			"has_image" => "YES",
		);

		$rsp = cooperhewitt_api_call("cooperhewitt.objects.getRandom", $args);

		$data = json_decode($rsp['body'], 'as hash');
		
		if ($data['stat'] != 'ok'){
			sms_output_error(404, "Cant get no...");
		}

		$out['title'] = $data['object']['title'];

		if ($data['object']['description']){
			$out['object'] = $data['object']['description'];
		}

		sms_output_ok($out);

	}

	#################################################################

	function sms_objects_ask(){

		$body = request_str("Body");
		$commands = explode(' ', $body);

		$question = strpos($body, " ");
		$question = substr($body, $question+1);

		if ( $commands[1] == "help"){
			sms_output_help("This lets you ask us a question. We will reply as soon as we can");
		}

		if ( (string)(int)$commands[1] == $commands[1]){
			$update = 1;

			$comment = strpos($question, " ");
			$comment = substr($question, $comment+1);

			$payload = array(
				"ticket" => array(
					"status" => "open",
					"comment" => array(
						"public" => true,
						"body" => $comment,
					),
				),
			);

			$more = array(
				"request_method" => "PUT",
			);

		} else {
	
			$payload = array(
				"ticket" => array(
					"requester" => array(
						"name" => "Object Phone",
					),
					"submitter_id" => "226246947",
					"subject" => $question,
					"comment" => array(
						"body" => $question,
					),
					"custom_fields" => array(
						"id" => 25536908,
						"value" => request_str("From")
					),
					"tags" => array(
						"objectphone",
					),
				),
			);
			
			$more = array(
				"request_method" => "POST",
			);
			
		}
		
		$json = json_encode($payload);

		$args = array(
			"data" => $json,
		);

		if ($update){
			$args['id'] = $commands[1];
		}
		
		$ticket = zendesk_api_call("tickets", $args, $more);

		$ticket_id = $ticket['response']['ticket']['id'];

		$out = array();

		if ($update){
			$out['reply'] = "Ok, we got it. Ticket " . $ticket_id . " has been updated with your new comment.";
		} else {
			$out['reply'] = "Thanks for asking your question. Our staff will get back to you very soon! To add a comment to this ticket text 'ask ". $ticket_id . "' followed by your comment.";
		}
		sms_output_ok($out);

	}