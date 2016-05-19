<?php

	#######################################################################
	
	function sms_spec_menu(){
		
		$methods = array(
			'message' => "Here are a list of commands",
			'help' => "Type any command followed by help if you need it",
		);

		foreach ($GLOBALS['cfg']['sms']['methods'] as $name =>$details){
			
			if (! $details['enabled']){
				continue;
			}
			
			if (! $details['documented']){
				continue;
			}

			$methods[] = $name;
		}

		sms_output_ok($methods);


	}