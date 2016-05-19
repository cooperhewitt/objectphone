<?php

	#################################################################

	function sms_test_echo(){
	
		$out = array();

		$out['body'] = request_str("Body");

		sms_output_ok($out);
	}
		
	#################################################################
	
	function sms_test_error(){
		sms_output_error(500, 'This is the network of our disconnect');
	}

	#################################################################
	# the end