<?php

	include("include/init.php");
	loadlib("messages");

	$number = request_str("number");

	$rsp = messages_get_messages_by_number($number);

	
	#
	# output
	#

	$smarty->assign('number', $number);
	$smarty->assign('messages', $rsp['rows']);
	$smarty->display("page_admin_messages.txt");


