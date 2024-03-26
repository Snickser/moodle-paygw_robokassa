<?php

use core_payment\helper;

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();


$data = array();
foreach ($_REQUEST as $key => $value) {
	$data[$key] = $value;
}

if (!$robokassatx = $DB->get_record('paygw_robokassa', array('id' => $data['InvId']))) {
	die('FAIL. Not a valid transaction id');
}

if (! $userid = $DB->get_record("user", array("id"=>$robokassatx->userid))) {
	die('FAIL. Not a valid user id.');
}

$component   = $robokassatx->component;
$paymentarea = $robokassatx->paymentarea;
$itemid      = $robokassatx->itemid;
$userid      = $robokassatx->userid;

$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');

// check test-mode
if($config->istestmode){
    $mrh_pass2 = $config->test_password2; // merchant test_pass2 here
} else {
    $mrh_pass2 = $config->password2;      // merchant pass2 here
}

if(isset($data['SignatureValue']) && isset($data['OutSum']) && isset($data['InvId']))
{
	$crc = strtoupper(md5("$data['OutSum']:$data['InvId']:$mrh_pass2"));

	if ($data['SignatureValue'] !== $crc) {
		die('FAIL. Signature does not match.');
	}

	// Check that amount paid is the correct amount
	if ( (float) $robokassatx->cost <= 0 ) {
		$cost = (float) $config->cost;
	} else {
		$cost = (float) $robokassatx->cost;
	}
	// Use the same rounding of floats as on the paygw form.
	$cost = number_format($cost, 2, '.', '');

	if ($data['OutSum'] !== $cost) {
		die('FAIL. Amount does not match.');
	}

	// Deliver course
	$payable = helper::get_payable($component, $paymentarea, $itemid);
	$cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), helper::get_gateway_surcharge('robokassa'));
	$paymentid = helper::save_payment($payable->get_account_id(), $component, $paymentarea, $itemid, $userid, $cost, $payable->get_currency(), 'robokassa');
	helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

	// write to DB
	$robokassatx->success = 1;
	if (!$DB->update_record('paygw_robokassa', $robokassatx)) {
		die('FAIL. Update db error.');
	} else {
		die("OK".$data['InvId');
	}
} else {
	die('FAIL');
}