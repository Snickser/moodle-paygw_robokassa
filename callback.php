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
use paygw_robokassa\notifications;

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();

$invid     = required_param('InvId', PARAM_INT);
$outsumm   = required_param('OutSum', PARAM_TEXT);
$signature = required_param('SignatureValue', PARAM_ALPHANUMEXT);

if (!$robokassatx = $DB->get_record('paygw_robokassa', ['paymentid' => $invid])) {
    die('FAIL. Not a valid transaction id');
}

if (!$payment = $DB->get_record('payments', ['id' => $robokassatx->paymentid])) {
    die('FAIL. Not a valid payment.');
}
$component   = $payment->component;
$paymentarea = $payment->paymentarea;
$itemid      = $payment->itemid;
$paymentid   = $payment->id;
$userid      = $payment->userid;

// Get config.
$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');

// Check test-mode.
if ($config->istestmode) {
    $mrhpass2 = $config->test_password2; // Merchant test_pass2 here.
    $robokassatx->success = 3;
} else {
    $mrhpass2 = $config->password2;      // Merchant pass2 here.
    $robokassatx->success = 1;
}

if (isset($mrhpass2)) {
    $crc = strtoupper(md5("$outsumm:$invid:$mrhpass2"));
    if ($signature !== $crc) {
        die('FAIL. Signature does not match.');
    }

    // For currency conversion.
    $payment->amount = $outsumm;
    if ($payment->currency !== 'RUB') {
        $payment->currency = 'RUB';
    }

    // Update payment.
    $DB->update_record('payments', $payment);

    // Deliver order.
    helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

    // Notify user.
    notifications::notify(
        $userid,
        $payment->amount,
        $payment->currency,
        $paymentid,
        'Success completed'
    );

    // Update paygw.
    if (!$DB->update_record('paygw_robokassa', $robokassatx)) {
        die('FAIL. Update db error.');
    } else {
        die('OK' . $invid);
    }
} else {
    die('FAIL');
}
