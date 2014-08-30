<?php

  // See http://www.twilio.com/docs/php/install
  require_once 'twilio/Services/Twilio.php';

  #################################################################

  function twilio_sms_send($from, $to, $body){

    $AccountSid = $GLOBALS['cfg']['twilio_account_sid'];
    $AuthToken = $GLOBALS['cfg']['twilio_auth_token'];

    $client = new Services_Twilio($AccountSid, $AuthToken);

    $message = $client->account->messages->create(array(
        "From" => $from,
        "To" => $to,
        "Body" => $body,
    ));

    return $message->sid;

  }
