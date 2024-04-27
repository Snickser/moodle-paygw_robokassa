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

require_login();

global $CFG, $USER, $DB;

$userid = $USER->id;

$component   = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid      = required_param('itemid', PARAM_INT);
$description = required_param('description', PARAM_TEXT);

$password    = optional_param('password', null, PARAM_TEXT);
$skipmode    = optional_param('skipmode', null, PARAM_TEXT);
$costself    = optional_param('costself', null, PARAM_TEXT);

$description = json_decode("\"$description\"");


$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');
$payable = helper::get_payable($component, $paymentarea, $itemid);// Get currency and payment amount.
$currency = $payable->get_currency();
$surcharge = helper::get_gateway_surcharge('robokassa');// In case user uses surcharge.
// TODO: Check if currency is IDR. If not, then something went really wrong in config.
$cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

// Check self cost
if (!empty($costself)) {
    $cost = $costself;
}
// Check maxcost
if ($config->maxcost && $cost > $config->maxcost) {
    $cost = $config->maxcost;
}
$cost = number_format($cost, 2, '.', '');

// Get course and groups for user
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

// Write tx to db
$paygwdata = new stdClass();
$paygwdata->userid = $userid;
$paygwdata->component = $component;
$paygwdata->paymentarea = $paymentarea;
$paygwdata->itemid = $itemid;
$paygwdata->cost = $cost;
$paygwdata->currency = $currency;
$paygwdata->date_created = date("Y-m-d H:i:s");
$paygwdata->courseid = $courseid;
$paygwdata->group_names = $groupnames;

if (!$transactionid = $DB->insert_record('paygw_robokassa', $paygwdata)) {
    die(get_string('error_txdatabase', 'paygw_robokassa'));
}

// Your registration data
$mrhlogin = $config->merchant_login;  // Your login here

// Check test-mode
if ($config->istestmode) {
    $mrhpass1 = $config->test_password1; // Merchant test_pass1 here
} else {
    $mrhpass1 = $config->password1;      // Merchant pass1 here
}

// Build redirect
$url = helper::get_success_url($component, $paymentarea, $itemid);

// Check passwordmode or skipmode
if (!empty($password) || $skipmode) {
    $success = false;
    if ($config->skipmode) {
        $success = true;
    } else if ($config->passwordmode && !empty($config->password)) {
    // Check password
        if ($password === $config->password) {
            $success = true;
        }
    }

    if ($success) {
        // Make fake pay
        $cost = 0;
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
        helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

        // Write to DB
        $data = new stdClass();
        $data->id = $transactionid;
        $data->success = 2;
        $data->cost = 0;
        $DB->update_record('paygw_robokassa', $data);

        redirect($url, get_string('password_success', 'paygw_robokassa'), 0, 'success');
    } else {
        redirect($url, get_string('password_error', 'paygw_robokassa'), 0, 'error');
    }
    die; // Never
}

// Order properties
$invid    = $transactionid;  // Shop's invoice number
$invdesc  = $description;  // Invoice desc
$outsumm  = $cost;  // Invoice summ

// For non-RUB pay
$outsumcurrency = null;
$currencyarg = null;
if ($currency != 'RUB') {
    $outsumcurrency = "&OutSumCurrency=$currency";
    $currencyarg = ":$currency";
}

// Nomenclatura
$items = new stdClass();
$items->sno = $config->sno;
$items->items = [
    [
    "name" => $description,
    "quantity" => 1,
    "sum" => $cost,
    "payment_method" => "full_payment",
    "payment_object" => "payment",
    "tax" => $config->tax,
    ],
];
$receipt = json_encode($items);

// file_put_contents("/tmp/xxxx", serialize($receipt)."\n", FILE_APPEND);

// Build CRC value
$crc = strtoupper(md5("$mrhlogin:$outsumm:$invid" . $currencyarg . ":$receipt:$mrhpass1"));

// redirect(
// "https://auth.robokassa.ru/Merchant/Index.aspx?" .
$request = "MerchantLogin=$mrhlogin" .
    "&OutSum=$outsumm$outsumcurrency" .
    "&InvId=$invid" .
    "&Description=" . urlencode($invdesc) .
    "&SignatureValue=$crc" .
    "&Culture=" . current_language() .
    "&Email=" . urlencode($USER->email) .
    "&IsTest=" . $config->istestmode .
    "&Receipt=" . urlencode($receipt);

$curlhandler = curl_init();
curl_setopt_array($curlhandler, [
     CURLOPT_URL => 'https://auth.robokassa.ru/Merchant/Indexjson.aspx',
     CURLOPT_RETURNTRANSFER => true,
]);
curl_setopt($curlhandler, CURLOPT_POST, true);
curl_setopt($curlhandler, CURLOPT_POSTFIELDS, $request);

$jsonresponse = curl_exec($curlhandler);

$response = json_decode($jsonresponse);

// file_put_contents("/tmp/xxxx", serialize($response) . "\n", FILE_APPEND);

if ($response->errorCode) {
    $error = $response->errorMessage;
    redirect($url, get_string('payment_error', 'paygw_robokassa') . " (Error code $response->errorCode)", 0, 'error');
    die;
}

$data = new stdClass();
$data->id = $transactionid;
$data->invoiceid = $response->invoiceID;
$DB->update_record('paygw_robokassa', $data);

redirect('https://auth.robokassa.ru/Merchant/Index/' . $response->invoiceID);
