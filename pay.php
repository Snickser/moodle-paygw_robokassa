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
 * Redirects user to the payment page
 *
 * @package   paygw_monobank
 * @copyright 2024 Alex Orlov <snickser@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;
use paygw_monobank\notifications;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/filelib.php');

require_login();
require_sesskey();

global $CFG, $USER, $DB;

$userid = $USER->id;

$component   = required_param('component', PARAM_COMPONENT);
$paymentarea = required_param('paymentarea', PARAM_AREA);
$itemid      = required_param('itemid', PARAM_INT);
$description = required_param('description', PARAM_TEXT);

$password    = optional_param('password', null, PARAM_TEXT);
$skipmode    = optional_param('skipmode', 0, PARAM_INT);
$costself    = optional_param('costself', null, PARAM_TEXT);

$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'monobank');
$payable = helper::get_payable($component, $paymentarea, $itemid);
$currency = $payable->get_currency();
$surcharge = helper::get_gateway_surcharge('monobank');
$cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

// Check self cost.
if (!empty($costself) && !$config->fixcost) {
    $cost = $costself;
}

// Check maxcost.
if ($config->maxcost && $cost > $config->maxcost) {
    $cost = $config->maxcost;
}

$cost = number_format($cost, 2, '.', '');

// Get course and groups for user.
if ($component == "enrol_yafee" || $component == "enrol_fee") {
    $cs = $DB->get_record('enrol', ['id' => $itemid]);
    $cs->course = $cs->courseid;
} else if ($component == "mod_gwpayments") {
    $cs = $DB->get_record('gwpayments', ['id' => $itemid]);
} else if ($paymentarea == "cmfee") {
    $cs = $DB->get_record('course_modules', ['id' => $itemid]);
} else if ($paymentarea == "sectionfee") {
    $cs = $DB->get_record('course_sections', ['id' => $itemid]);
}
$groupnames = '';
if (!empty($cs->course)) {
    $courseid = $cs->course;
    if ($gs = groups_get_user_groups($courseid, $userid, true)) {
        foreach ($gs as $gr) {
            foreach ($gr as $g) {
                $groups[] = groups_get_group_name($g);
            }
        }
        if (isset($groups)) {
            $groupnames = implode(',', $groups);
        }
    }
} else {
    $courseid = '';
}

// Write tx to db.
$paygwdata = new stdClass();
$paygwdata->courseid    = $courseid;
$paygwdata->groupnames  = $groupnames;
$paygwdata->timecreated = time();

if (!$transactionid = $DB->insert_record('paygw_monobank', $paygwdata)) {
    throw new \moodle_exception(get_string('error_txdatabase', 'paygw_monobank'));
}
$paygwdata->id = $transactionid;

// Build redirect.
$url = helper::get_success_url($component, $paymentarea, $itemid);

// Set the context of the page.
$PAGE->set_url($SCRIPT);
$PAGE->set_context(context_system::instance());

// Check passwordmode or skipmode.
if (!empty($password) || $skipmode) {
    $success = false;
    if ($config->skipmode) {
        $success = true;
    } else if (isset($cs->password) && !empty($cs->password)) {
        if ($password === $cs->password) {
            $success = true;
        }
    } else if ($config->passwordmode && !empty($config->password)) {
        if ($password === $config->password) {
            $success = true;
        }
    }

    if ($success) {
        // Make fake payment.
        $paymentid = helper::save_payment(
            $payable->get_account_id(),
            $component,
            $paymentarea,
            $itemid,
            $userid,
            0,
            $payable->get_currency(),
            'monobank'
        );
        helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

        // Write to DB.
        $paygwdata->success = 2;
        $paygwdata->paymentid = $paymentid;
        $DB->update_record('paygw_monobank', $paygwdata);
        redirect($url, get_string('password_success', 'paygw_monobank'), 0, 'success');
    } else {
        $DB->delete_records('paygw_monobank', ['id' => $transactionid]);
        redirect($url, get_string('password_error', 'paygw_monobank'), 0, 'error');
    }
    die; // Never.
}

// Save payment.
$paymentid = helper::save_payment(
    $payable->get_account_id(),
    $component,
    $paymentarea,
    $itemid,
    $userid,
    $cost,
    $payable->get_currency(),
    'monobank'
);

// Currency code mapping (ISO 4217 numeric).
$currencymap = [
    'UAH' => 980,
    'USD' => 840,
    'EUR' => 978,
];
$ccy = isset($currencymap[$currency]) ? $currencymap[$currency] : 980;

// Amount in kopiykas (smallest currency unit).
$amount = (int) round($cost * 100);

// Build MonoBank API request.
$invoicedata = [
    'amount' => $amount,
    'ccy' => $ccy,
    'merchantPaymInfo' => [
        'reference' => (string) $paymentid,
        'destination' => $description,
        'basketOrder' => [
            [
                'name' => $description,
                'qty' => 1,
                'sum' => $amount,
                'code' => (string) $paymentid,
            ],
        ],
    ],
    'redirectUrl' => $CFG->wwwroot . '/payment/gateway/monobank/return.php?id=' . $paymentid,
    'webHookUrl' => $CFG->wwwroot . '/payment/gateway/monobank/callback.php',
    'validity' => 3600,
    'paymentType' => 'debit',
];

// Make invoice via MonoBank API.
$apiurl = 'https://api.monobank.ua/api/merchant/invoice/create';
$curl = new curl();
$curl->setHeader([
    'Content-Type: application/json',
    'X-Token: ' . $config->api_token,
]);
$options = [
    'CURLOPT_RETURNTRANSFER' => true,
    'CURLOPT_TIMEOUT' => 30,
    'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
    'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
];
$jsonresponse = $curl->post($apiurl, json_encode($invoicedata), $options);

if (!empty($curl->errno)) {
    $DB->delete_records('paygw_monobank', ['id' => $transactionid]);
    throw new \moodle_exception(get_string('payment_error', 'paygw_monobank') . " (Error $curl->error)");
}

$response = json_decode($jsonresponse);

if (!isset($response->invoiceId)) {
    $DB->delete_records('paygw_monobank', ['id' => $transactionid]);
    $errmsg = isset($response->errText) ? $response->errText : 'response error';
    throw new \moodle_exception(get_string('payment_error', 'paygw_monobank') . " ($errmsg)");
}

// Write to DB.
$paygwdata->paymentid = $paymentid;
$paygwdata->invoiceid = $response->invoiceId;
$DB->update_record('paygw_monobank', $paygwdata);

$payurl = $response->pageUrl;

// Notify user.
if ($config->sendlinkmsg || is_siteadmin()) {
    notifications::notify(
        $userid,
        $cost,
        $currency,
        $payurl,
        'Invoice created'
    );
}

redirect($payurl);
