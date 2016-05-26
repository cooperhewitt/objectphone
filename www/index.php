<?php
	include('include/init.php');

	#
	# are we signing up?
	#

	if (post_str('signup')){

		$ok = 1;

		$phone	= post_str('phone');

		$smarty->assign('phone', $phone);

		#
		# all fields are in order?
		#

		if (!strlen($phone)){

			$smarty->assign('error_missing', 1);
			$ok = 0;
		}

		#
		# convert to valid phone number
		#

		## remove white space
		$phone = preg_replace('/\s+/', '', $phone);
		## remove special characters
		$phone = preg_replace('/[^0-9]/i', '', $phone);
		## remove the 1
		$phone = substr($phone, 1);


		if (strlen($phone) != 10){

			$smarty->assign('error_missing', 1);
			$ok = 0;
		}

		#
		# already signed up?
		#

		if ($ok && users_is_phone_already_registered($phone)){

			$smarty->assign('phone', '');
			$smarty->assign('error_already_signed_up', 1);
			$ok = 0;
		}

		#
		# sign up
		#

		if ($ok){

			$ret = users_create_user(array(
				'phone'	=> $phone,
			));
			
			if ($ret['ok']){
				users_send_confirmation($ret['user']['phone']);
				exit;
			}

			$smarty->assign('error_failed', 1);
			$ok = 0;
		}
	}


	$smarty->display('page_index.txt');
	exit();


