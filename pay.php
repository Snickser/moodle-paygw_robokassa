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
 * @package   paygw_robokassa
 * @copyright 2024 Alex Orlov <snickser@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;

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

$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');
$payable = helper::get_payable($component, $paymentarea, $itemid);// Get currency and payment amount.
$currency = $payable->get_currency();
$surcharge = helper::get_gateway_surcharge('robokassa');// In case user uses surcharge.
$cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

// Check self cost.
if (!empty($costself)) {
    $cost = $costself;
}

// Check maxcost.
if ($config->maxcost && $cost > $config->maxcost) {
    $cost = $config->maxcost;
}
$cost = number_format($cost, 2, '.', '');

// Get course and groups for user.
if ($component == "enrol_fee") {
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
$paygwdata->courseid = $courseid;
$paygwdata->groupnames = $groupnames;

if (!$transactionid = $DB->insert_record('paygw_robokassa', $paygwdata)) {
    throw new Error(get_string('error_txdatabase', 'paygw_robokassa'));
}
$paygwdata->id = $transactionid;

// Build redirect.
$url = helper::get_success_url($component, $paymentarea, $itemid);

// Check passwordmode or skipmode.
if (!empty($password) || $skipmode) {
    $success = false;
    if ($config->skipmode) {
        $success = true;
    } else if (isset($cs->password) && !empty($cs->password)) {
        // Check module password.
        if ($password === $cs->password) {
            $success = true;
        }
    } else if ($config->passwordmode && !empty($config->password)) {
        // Check payment password.
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
            'robokassa'
        );
        helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

        // Write to DB.
        $paygwdata->success = 2;
        $paygwdata->paymentid = $paymentid;
        $DB->update_record('paygw_robokassa', $paygwdata);

        redirect($url, get_string('password_success', 'paygw_robokassa'), 0, 'success');
    } else {
        redirect($url, get_string('password_error', 'paygw_robokassa'), 0, 'error');
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
    'robokassa'
);

// Order properties.
$invid    = $paymentid;    // Shop's invoice number.
$invdesc  = $description;  // Invoice desc.
$outsumm  = $cost;         // Invoice summ.

// Your registration data.
$mrhlogin = $config->merchant_login;  // Your login here.

// Check test-mode.
if ($config->istestmode) {
    $mrhpass1 = $config->test_password1; // Merchant test_pass1 here.
    $mrhpass2 = $config->test_password2;
} else {
    $mrhpass1 = $config->password1;      // Merchant pass1 here.
    $mrhpass2 = $config->password2;
}

// Checks if invoiceid already exist.
if ($config->checkinvoice) {
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
    $response = xmlize($xmlresponse, $whitespace = 1, $encoding = 'UTF-8', true);
    $err = $response['OperationStateResponse']['#']['Result'][0]['#']['Code'][0]['#'];
    if ($err != 3) {
        $DB->delete_records('paygw_robokassa', ['id' => $transactionid]);
        throw new Error("Invoice ID check error $err");
    }
}

// For non-RUB pay.
if ($currency != 'RUB') {
    $outsumcurrency = "&OutSumCurrency=$currency";
    $currencyarg = ":$currency";
} else {
    $outsumcurrency = null;
    $currencyarg = null;
}

// Nomenclatura.
$items = new stdClass();
$items->sno = $config->sno;
$items->items = [
    [
    "name" => $description,
    "quantity" => 1,
    "sum" => $cost,
    "tax" => $config->tax,
    ],
];
$receipt = json_encode($items);

// Build CRC value.
$crc = strtoupper(md5("$mrhlogin:$outsumm:$invid" . $currencyarg . ":$receipt:$mrhpass1"));

// Params.
$request = "MerchantLogin=$mrhlogin" .
    "&OutSum=$outsumm" . $outsumcurrency .
    "&InvId=$invid" .
    "&Description=" . urlencode($invdesc) .
    "&SignatureValue=$crc" .
    "&Culture=" . current_language() .
    "&Email=" . urlencode($USER->email) .
    "&IsTest=" . $config->istestmode .
    "&ExpirationDate=" . date(DATE_RFC3339_EXTENDED, time() + 600) .
    "&Receipt=" . urlencode($receipt);

// Make payment.
$location = 'https://auth.robokassa.ru/Merchant/Indexjson.aspx';
$options = [
    'CURLOPT_RETURNTRANSFER' => true,
    'CURLOPT_TIMEOUT' => 30,
    'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
    'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
];
$curl = new curl();
$jsonresponse = $curl->post($location, $request, $options);
$response = json_decode($jsonresponse);

if (!isset($response->errorCode)) {
    $DB->delete_records('paygw_robokassa', ['id' => $transactionid]);
    throw new Error(get_string('payment_error', 'paygw_robokassa') . " (response error)");
}

if ($response->errorCode) {
    $DB->delete_records('paygw_robokassa', ['id' => $transactionid]);
    throw new Error(get_string('payment_error', 'paygw_robokassa') . " (Error code $response->errorCode)");
}

// Write to DB.
$paygwdata->paymentid = $paymentid;
$paygwdata->invoiceid = $response->invoiceID;
$DB->update_record('paygw_robokassa', $paygwdata);

redirect('https://auth.robokassa.ru/Merchant/Index/' . $response->invoiceID);
