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
require_once($CFG->libdir . '/filelib.php');

defined('MOODLE_INTERNAL') || die();

$invid     = required_param('InvId', PARAM_INT);
$outsumm   = required_param('OutSum', PARAM_TEXT); // TEXT only!
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

// Check crc.
$crc = strtoupper(md5("$outsumm:$invid:$mrhpass2"));
if ($signature !== $crc) {
    die('FAIL. Signature does not match.');
}

// Check invoice.
if ($config->checkinvoice && !$config->istestmode) {
    $mrhlogin = $config->merchant_login;
    $location = 'https://auth.robokassa.ru/Merchant/WebService/Service.asmx/OpStateExt';
    $crc = strtoupper(md5("$mrhlogin:$invid:$mrhpass2"));
    $location .= "?MerchantLogin=$mrhlogin" .
        "&InvoiceID=$invid" .
        "&Signature=$crc";
    $options = [
       'CURLOPT_RETURNTRANSFER' => true,
       'CURLOPT_TIMEOUT' => 30,
       'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
       'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
       ];
    $curl = new curl();
    $xmlresponse = $curl->get($location, $options);
    $response = xmlize($xmlresponse, $whitespace = 1, $encoding = 'UTF-8', false);

    file_put_contents("/tmp/yyyy", serialize($response) . "\n\n", FILE_APPEND | LOCK_EX);

    $err = $response['OperationStateResponse']['#']['Result'][0]['#']['Code'][0]['#'];
    if ($err) {
        die('FAIL. Invoice result error.');
    }
    $err = $response['OperationStateResponse']['#']['State'][0]['#']['Code'][0]['#'];
    if ($err !== '100') {
        die('FAIL. Invoice not paid.');
    }
}

// For currency conversion.
$payment->amount = (float)$outsumm;
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
