<?php

	#################################################################

	function sms_users_design(){

		$out = array();

		## just update the user as having been confirmed
		## and send them a message about whats next

		$phone = request_str("From");

		## remove the first two characters... cuz international... 
		$phone = substr($phone, 2);

		$user = users_get_by_phone($phone);

		if ($user['confirmed']){
			sms_output_error(404, "Hey, you've already done that!");
		}

		$rsp = users_confirm_phone($user);

		$out['a'] = "Thanks! You are now ready to receive objects from our collection every day, tailored to your preferences.";
		$out['b'] = "To start we will send you a few random objects."; # Reply LIKE or DISLIKE to help us learn your preferences.";
		$out['c'] = "If you are looking for a specific object, you can simply text us its accession number, and we will look it up for you.";
		$out['d'] = "Got a question? Ask us anything and we will try and find you an answer!";

		sms_output_ok($out);
	}
