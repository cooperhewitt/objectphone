<?php

	########################################################################

	$GLOBALS['cfg']['sms']['methods'] = array_merge(array(

		"menu" => array(
			"description" => "Get a list of all the methods",
			"documented" => 1,
			"enabled" => 1,
			"library" => "sms_spec"
		),

		"echo" => array(
			"description" => "A testing method which echo's all parameters back in the response.",
			"documented" => 1,
			"enabled" => 1,
			"library" => "sms_test"
		),

		"error" => array(
			"description" => "Return a test error from the SMS API",
			"documented" => 1,
			"enabled" => 1,
			"library" => "sms_test"
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

		"ask" => array(
			"description" => "Ask a question",
			"documented" => 1,
			"enabled" => 1,
			"library" => "sms_objects"
		),

	), $GLOBALS['cfg']['sms']['methods']);

	########################################################################
	# the end