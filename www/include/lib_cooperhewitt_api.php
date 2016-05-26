<?php

	loadlib("http");

	########################################################################

	function cooperhewitt_api_authenticate_user_url(){

		$args = array(
			'client_id' => $GLOBALS['cfg']['cooperhewitt_api_key'],
			'redirect_uri' => $GLOBALS['cfg']['abs_root_url'] . 'cooperhewitt/auth/',
			'scope' => 'write',
			'response_type' => 'code',
		);

		$query = http_build_query($args);

		$url = $GLOBALS['cfg']['cooperhewitt_grant_endpoint'] . '?'. $query;
		return $url;
	}

	########################################################################

	function cooperhewitt_api_get_access_token($grant_token){

		$args = array(
			'client_id' => $GLOBALS['cfg']['cooperhewitt_api_key'],
			'redirect_uri' => $GLOBALS['cfg']['abs_root_url'] . 'cooperhewitt/auth/',
			'grant_type' => 'authorization_code',
			'response_type' => 'code',
			'code' => $grant_token,
		);

		$query = http_build_query($args);

		$url = $GLOBALS['cfg']['cooperhewitt_token_endpoint'] . '?'. $query;
		$rsp = http_get($url);

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = json_decode($rsp['body'], 'as hash');
		$rsp['data'] = $data;

		return $rsp;
	}

	########################################################################

	function cooperhewitt_api_call($method, $args=array(), $more=array()){

		$args['method'] = $method;

		$url = $GLOBALS['cfg']['cooperhewitt_api_endpoint'];

		$headers = array();

		$rsp = http_post($url, $args, $headers, $more);

		if (! $rsp['ok']){
			return $rsp;
		}

		$data = json_decode($rsp['body'], 'as hash');

		if (! $data){
			$rsp['ok'] = 0;
			$rsp['error'] = 'Failed to parse JSON';
			return $rsp;
		}

		$rsp['data'] = $data;
		return $rsp;
	}

	########################################################################

	# the end
