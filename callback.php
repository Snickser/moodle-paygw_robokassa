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
 * MonoBank webhook handler.
 *
 * @package     paygw_monobank
 * @copyright   2024 Alex Orlov <snickser@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;
use paygw_monobank\notifications;

require("../../../config.php");
global $CFG, $DB;
require_once($CFG->libdir . '/filelib.php');

// Read the webhook payload.
$input = file_get_contents('php://input');
$data = json_decode($input);

if (empty($data) || !isset($data->invoiceId) || !isset($data->status)) {
    http_response_code(400);
    die('FAIL. Invalid webhook data.');
}

$invoiceid = $data->invoiceId;
$status = $data->status;
$reference = isset($data->reference) ? $data->reference : null;

// Only process successful payments.
if ($status !== 'success') {
    http_response_code(200);
    die('OK');
}

// Find payment by invoiceid.
if (!$monobanktx = $DB->get_record('paygw_monobank', ['invoiceid' => $invoiceid])) {
    http_response_code(404);
    die('FAIL. Not a valid transaction id.');
}

// Already processed.
if ($monobanktx->success > 0) {
    http_response_code(200);
    die('OK. Already processed.');
}

if (!$payment = $DB->get_record('payments', ['id' => $monobanktx->paymentid])) {
    http_response_code(404);
    die('FAIL. Not a valid payment.');
}
$component   = $payment->component;
$paymentarea = $payment->paymentarea;
$itemid      = $payment->itemid;
$paymentid   = $payment->id;
$userid      = $payment->userid;

// Get config.
$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'monobank');

// Optionally verify the payment status via API.
$apiurl = 'https://api.monobank.ua/api/merchant/invoice/status?invoiceId=' . urlencode($invoiceid);
$curl = new curl();
$curl->setHeader([
    'X-Token: ' . $config->api_token,
]);
$options = [
    'CURLOPT_RETURNTRANSFER' => true,
    'CURLOPT_TIMEOUT' => 30,
    'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
    'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
];
$jsonresponse = $curl->get($apiurl, $options);
$statusresponse = json_decode($jsonresponse);

if (empty($statusresponse) || !isset($statusresponse->status) || $statusresponse->status !== 'success') {
    http_response_code(400);
    die('FAIL. Payment not confirmed by API.');
}

// Update payment amount from actual data.
if (isset($statusresponse->amount)) {
    $payment->amount = $statusresponse->amount / 100;
}
$payment->timemodified = time();
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
$monobanktx->success = $config->istestmode ? 3 : 1;
if (!$DB->update_record('paygw_monobank', $monobanktx)) {
    http_response_code(500);
    die('FAIL. Update db error.');
}

http_response_code(200);
die('OK');
