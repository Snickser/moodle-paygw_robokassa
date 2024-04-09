<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     paygw_robokassa
 * @copyright   2024 Alex Orlov <snickser@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();


$inv_id    = required_param('InvId', PARAM_ALPHANUMEXT);
$out_summ  = required_param('OutSum', PARAM_RAW);
$signature = required_param('SignatureValue', PARAM_ALPHANUMEXT);


if (!$robokassatx = $DB->get_record('paygw_robokassa', array('id' => $inv_id))) {
	die('FAIL. Not a valid transaction id');
}

if (! $userid = $DB->get_record("user", array("id"=>$robokassatx->userid))) {
	die('FAIL. Not a valid user id.');
}

$component   = $robokassatx->component;
$paymentarea = $robokassatx->paymentarea;
$itemid      = $robokassatx->itemid;
$userid      = $robokassatx->userid;

// get config
$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');
$payable = helper::get_payable($component, $paymentarea, $itemid);

// check test-mode
if($config->istestmode){
    $mrh_pass2 = $config->test_password2; // merchant test_pass2 here
} else {
    $mrh_pass2 = $config->password2;      // merchant pass2 here
}

if(isset($mrh_pass2))
{
	$crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));
	if ($signature !== $crc) {
		die('FAIL. Signature does not match.');
	}

	// Check that amount paid is the correct amount
	if ( (float) $robokassatx->cost <= 0 ) {
		$cost = (float) $payable->get_amount();
	} else {
		$cost = (float) $robokassatx->cost;
	}
	// Use the same rounding of floats as on the paygw form.
	$cost = number_format($cost, 2, '.', '');
	$out_summ = number_format($out_summ, 2, '.', '');

	if ($out_summ !== $cost) {
		die('FAIL. Amount does not match.');
	}

	// Deliver course
	//$fee = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), helper::get_gateway_surcharge('robokassa'));
	$paymentid = helper::save_payment($payable->get_account_id(), $component, $paymentarea, $itemid, $userid, $cost, $payable->get_currency(), 'robokassa');
	helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

	// write to DB
	$robokassatx->success = 1;
	if (!$DB->update_record('paygw_robokassa', $robokassatx)) {
		die('FAIL. Update db error.');
	} else {
		die("OK".$inv_id);
	}
} else {
	die('FAIL');
}