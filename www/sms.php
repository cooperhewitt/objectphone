<?php

	$GLOBALS['this_is_sms'] = 1;

	include("include/init.php");
	loadlib("sms");

	$body = request_str("Body");

	sms_dispatch($body);

	exit();
