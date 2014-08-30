<?php
	include("include/init.php");

	features_ensure_enabled("signin");

	login_ensure_loggedout();


	#
	# pass through
	#

	$redir = request_str('redir');
	$smarty->assign('redir', $redir);


	#
	# try and sign in?
	#

	if (post_str('signin')){

		$email		= post_str('email');
		$password	= post_str('password');

		$smarty->assign('email', $email);

		$ok = 1;


		#
		# required fields?
		#

		if ((!strlen($email)) || (!strlen($password))){

			$smarty->assign('error_missing', 1);
			$ok = 0;
		}


		#
		# user exists?
		#

		if ($ok){
			$user = users_get_by_email($email);

			if (!$user['id']){

				$smarty->assign('error_nouser', 1);
				$ok = 0;
			}
		}


		#
		# users deleted?
		#

		if ($ok && $user['deleted']){

			$smarty->assign('error_deleted', 1);
			$ok = 0;
		}


		#
		# password match
		#

		if ($ok){

			if (! passwords_validate_password_for_user($password, $user)){
				$smarty->assign('error_password', 1);
				$ok = 0;
			}
		}


		#
		# it's all good - take me to the page where I enter my code
		#

		if ($ok){

			if (features_is_enabled('multifactor_auth')){

				loadlib('multifactor_auth');
				loadlib('twilio');

				$to = $user['phone'];
				$body = multifactor_auth_generate_code($user);

				$rsp = twilio_sms_send($GLOBALS['cfg']['twilio_number'], $to, $body );

				$smarty->display('page_signin_multifactor.txt');

			} else {
				$redir = ($redir) ? $redir : '/';
				login_do_login($user, $redir);
			}

			exit;
		}
	}


	#
	# output
	#

	$smarty->display('page_signin.txt');
