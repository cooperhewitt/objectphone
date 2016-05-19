<?php

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
