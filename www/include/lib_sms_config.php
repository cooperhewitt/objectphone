<?php

	#########################################################################

	function sms_config_init(){

		# Load methods
		foreach ($GLOBALS['cfg']['sms_method_definitions'] as $def){
			try {
				$path = FLAMEWORK_INCLUDE_DIR . "config_sms_{$def}.php";
				include_once($path);
			}
			catch (Exception $e){
				_sms_config_freakout_and_die();
			}
		}
		
	}

	#########################################################################

	function _sms_config_freakout_and_die(){
		return "shit";
	}