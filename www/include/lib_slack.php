<?php

	loadlib("slack_api");

	########################################################################

	function slack($text, $more=array()){

		$defaults = array(
			'channel' => '#general',
			'username' => 'flamework',
		);

		$more = array_merge($defaults, $more);

		$method = 'chat.postMessage';

		$args = array(
			'channel' => $more['channel'],
			'username' => $more['username'],
			'text' => $text,
		);

		$rsp = slack_api_call($method, $args);
		return $rsp;
	}

	########################################################################

	# the end