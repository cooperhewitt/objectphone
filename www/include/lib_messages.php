<?php

	#######################################################

	function messages_create_message($message){

		$message['created'] = time();
		
		$hash = array();
		
		foreach ($message as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$ret = db_insert('messages', $hash);

		if (!$ret['ok']) return $ret;


		#
		# cache the unescaped version
		#

		$message['id'] = $ret['insert_id'];

		cache_set("MESSAGE-{$message['id']}", $message);

		return array(
			'ok'	=> 1,
			'message'	=> $message,
		);
	}

	#######################################################

	function messages_get_messages_by_number($number){

		$more = array(
			'per_page' => 20,
		);

		$number_with_code = "+1" . $number;

		$enc_number = AddSlashes($number_with_code);

		$rsp = db_fetch_paginated("SELECT * FROM messages WHERE messages.From='{$enc_number}' OR messages.To='{$enc_number}' ORDER BY id DESC", $more);

		return $rsp;

	}
