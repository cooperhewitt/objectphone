<?php

	include("init_local.php");

	loadlib("cli");

	### get list of all confirmed users phones

	loadlib("users");

	$users = users_get_confirmed_users();

	### send them each a random object

	loadlib("objects");

	foreach($users['rows'] as $user){

		$rsp = objects_send_random_object($user);
		dumper($rsp);
		#echo "Sent random object to: {$user['phone']}\n"; 
	}

	exit();