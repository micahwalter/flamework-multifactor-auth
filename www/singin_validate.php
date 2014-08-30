<?php
  include("include/init.php");

  features_ensure_enabled("signin");

  login_ensure_loggedout();

  loadlib('multifactor_auth');

  #
  # pass through
  #

  $redir = request_str('redir');
  $email = request_str('email');

  $smarty->assign('email', $email);
  $smarty->assign('redir', $redir);

  # validate the code

  if (post_str('validate')){

    $code		= post_str('code');

    $smarty->assign('code', $code);

    $ok = 1;


    #
    # required fields?
    #

    if ((!strlen($code))){

      $smarty->assign('error_missing', 1);
      $ok = 0;
    }

    # check user exists

    if ($ok){
      $user = users_get_by_email($email);

      if (!$user['id']){

        $smarty->assign('error_nouser', 1);
        $ok = 0;
      }
    }

    # check the validation code

    if ($ok){
      if (! multifactor_auth_validate_code($user, $code)){
        $smarty->assign('error_invalid', 1);
        $ok = 0;
      }
    }

    #
    # it's all good - sign me in
    #

    if ($ok){

      multifactor_auth_exprie_code($user);

      $redir = ($redir) ? $redir : '/';

      login_do_login($user, $redir);
      exit;
    }
  }


  #
  # output
  #

  $smarty->display('page_signin_multifactor.txt');
