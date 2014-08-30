<?php


  #################################################################

  function multifactor_auth_generate_code(&$user){

    $auth_code = mt_rand(100000, 999999);

    $rsp = users_update_user($user, array(
      'login_code' => AddSlashes($auth_code),
    ));

    if ($rsp['ok']){
      return $auth_code;
    }

    return 0;

  }

  function multifactor_auth_validate_code(&$user, $code){

    users_reload_user($user);

    if ( $user['login_code'] == $code){
      return 1;
    }

    return 0;

  }

  function multifactor_auth_exprie_code(&$user){

    $rsp = users_update_user($user, array(
      'login_code' => 0,
    ));

    if ($rsp['ok']){
      return 1;
    }

    return 0;

  }
