<?php

	$GLOBALS['slack_api_host'] = 'slack.com';
	$GLOBALS['slack_api_endpoint'] = '/api';

	########################################################################

	function slack_api_call($method, $args=array()){

		if (! isset($args['token'])){
			$args['token'] = $GLOBALS['cfg']['slack_api_access_token'];
		}

		if (! isset($args['username'])){
			$args['username'] = 'flamework';
		}

		$headers = array();
		$more = array();

		$url = "https://" . $GLOBALS['slack_api_host'] . $GLOBALS['slack_api_endpoint'] . "/" . $method;
		$rsp = http_post($url, $args, $headers, $more);

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = json_decode($rsp['body'], 'as hash');

		if (! $data){
			return array('ok' => 0, 'error' => 'failed to parse response', 'details' => $rsp);
		}

		return $data;
	}

	########################################################################

	# the end	