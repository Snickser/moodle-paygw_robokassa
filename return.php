<?php

use core_payment\helper;

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();

require_login();

$id = required_param('InvId', PARAM_INT);

if (!$robokassatx = $DB->get_record('paygw_robokassa', array('id' => $id))) {
    die('FAIL. Not a valid transaction id');
}

$paymentarea = $robokassatx->paymentarea;
$component   = $robokassatx->component;
$itemid      = $robokassatx->itemid;

$url = helper::get_success_url($component, $paymentarea, $itemid);
redirect($url, get_string('paymentsuccessful', 'paygw_robokassa'), 0, 'success');
