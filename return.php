<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Handles user return from MonoBank payment page.
 *
 * @package   paygw_monobank
 * @copyright 2024 Alex Orlov <snickser@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();

require_login();

$paymentid = required_param('id', PARAM_INT);

if (!$monobanktx = $DB->get_record('paygw_monobank', ['paymentid' => $paymentid])) {
    throw new \moodle_exception(get_string('error_notvalidtxid', 'paygw_monobank'), 'paygw_monobank');
}

if (!$payment = $DB->get_record('payments', ['id' => $monobanktx->paymentid])) {
    throw new \moodle_exception(get_string('error_notvalidpayment', 'paygw_monobank'), 'paygw_monobank');
}

$paymentarea = $payment->paymentarea;
$component   = $payment->component;
$itemid      = $payment->itemid;

// Build redirect.
$url = helper::get_success_url($component, $paymentarea, $itemid);

if ($monobanktx->success) {
    redirect($url, get_string('payment_success', 'paygw_monobank'), 0, 'success');
} else {
    // Check status via API in case webhook hasn't arrived yet.
    $config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'monobank');
    require_once($CFG->libdir . '/filelib.php');

    $apiurl = 'https://api.monobank.ua/api/merchant/invoice/status?invoiceId=' . urlencode($monobanktx->invoiceid);
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

    if (!empty($statusresponse) && isset($statusresponse->status) && $statusresponse->status === 'success') {
        // Payment is successful but webhook hasn't been processed yet.
        // Process it now.
        if (isset($statusresponse->amount)) {
            $payment->amount = $statusresponse->amount / 100;
        }
        $payment->timemodified = time();
        $DB->update_record('payments', $payment);

        helper::deliver_order($component, $paymentarea, $itemid, $payment->id, $payment->userid);

        $monobanktx->success = $config->istestmode ? 3 : 1;
        $DB->update_record('paygw_monobank', $monobanktx);

        redirect($url, get_string('payment_success', 'paygw_monobank'), 0, 'success');
    } else {
        redirect($url, get_string('payment_error', 'paygw_monobank'), 0, 'error');
    }
}
