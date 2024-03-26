<?php

use core_payment\helper;

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();

require_login();

$inv_id    = required_param('InvId', PARAM_ALPHANUMEXT);
$out_summ  = required_param('OutSum', PARAM_RAW);
$signature = required_param('SignatureValue', PARAM_ALPHANUMEXT);

$signature = strtoupper($signature);  // force uppercase

if (!$robokassatx = $DB->get_record('paygw_robokassa', array('id' => $inv_id))) {
    die('FAIL. Not a valid transaction id');
}

$paymentarea = $robokassatx->paymentarea;
$component   = $robokassatx->component;
$itemid      = $robokassatx->itemid;


// check test-mode
if($config->istestmode){
    $mrh_pass1 = $config->test_password1; // merchant test_pass2 here
} else {
    $mrh_pass1 = $config->password1;      // merchant pass2 here
}

// build own CRC
$crc =  strtoupper(md5("$out_summ:$inv_id:$mrh_pass1"));

if ($signature !== $crc) {
    die("FAIL");
}

$url = helper::get_success_url($component, $paymentarea, $itemid);
redirect($url, get_string('paymentsuccessful', 'paygw_robokassa'), 0, 'success');
