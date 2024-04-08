<?php

use core_payment\helper;

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();

require_login();

$inv_id    = required_param('InvId', PARAM_ALPHANUMEXT);

$out_summ  = optional_param('OutSum', null, PARAM_TEXT);
$signature = optional_param('SignatureValue', null, PARAM_TEXT);


if (!$robokassatx = $DB->get_record('paygw_robokassa', array('id' => $inv_id))) {
    die('FAIL. Not a valid transaction id');
}

$paymentarea = $robokassatx->paymentarea;
$component   = $robokassatx->component;
$itemid      = $robokassatx->itemid;

// build redirect
$url = helper::get_success_url($component, $paymentarea, $itemid);

if(!isset($signature)){
    redirect($url, '', 0, '');
    die;
}

// get config
$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');

// check test-mode
if($config->istestmode){
    $mrh_pass1 = $config->test_password1; // merchant test_pass2 here
} else {
    $mrh_pass1 = $config->password1;      // merchant pass2 here
}

$signature = strtoupper($signature);  // force uppercase

// build own CRC
$crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1"));

// check crc and redirect
if ($signature == $crc && $robokassatx->success) {
    redirect($url, get_string('payment_success', 'paygw_robokassa'), 0, 'success');
} else {
    redirect($url, get_string('payment_error', 'paygw_robokassa'), 0, 'error');
}
