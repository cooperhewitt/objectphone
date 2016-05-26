<?php

	########################################################################

	$GLOBALS['cfg']['sms']['methods'] = array_merge(array(

		"design" => array(
			"description" => "Confirm the subscription of a phone",
			"documented" => 1,
			"enabled" => 1,
			"library" => "sms_users"
		),

		"object" => array(
			"description" => "Return info about an object by ID",
			"documented" => 1,
			"enabled" => 1,
			"library" => "sms_objects"
		),

		"random" => array(
			"description" => "Return a random object",
			"documented" => 1,
			"enabled" => 1,
			"library" => "sms_objects"
		),

	), $GLOBALS['cfg']['sms']['methods']);

	########################################################################
	# the end