<?php

	include("include/init.php");
	loadlib("messages");
	loadlib("twilio");

	$number = request_str("number");

	if (post_str('send')){

		$msg = post_str('msg');
		twilio_send_sms($number, $msg);
	}

	$rsp = messages_get_messages_by_number($number);
	
	#
	# output
	#

	$smarty->assign('number', $number);
	$smarty->assign('messages', $rsp['rows']);
	$smarty->display("page_admin_messages.txt");


