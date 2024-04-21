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

// check self cost
if ( !empty($costself) ) {
    $cost = $costself;
}
// check maxcost
if ( $config->maxcost && $cost > $config->maxcost ) {
    $cost = $config->maxcost;
}
$cost = number_format($cost, 2, '.', '');

// get course and groups for user
if ( $component == "enrol_fee" ) {
    $cs = $DB->get_record('enrol', ['id' => $itemid]);
    $cs->course = $cs->courseid;
} else if ( $paymentarea == "cmfee" ) {
    $cs = $DB->get_record('course_modules', ['id' => $itemid]);
} else if ( $paymentarea == "sectionfee" ) {
    $cs = $DB->get_record('course_sections', ['id' => $itemid]);
} else if ( $component == "mod_gwpayments" ) {
    $cs = $DB->get_record('gwpayments', ['id' => $itemid]);
}
$groupnames = '';
$courseid = '';
if ( isset($cs->course) ) {
    $courseid = $cs->course;
    if ($gs = groups_get_user_groups($cs->course, $userid, true)) {
        foreach ($gs as $gr) {
            foreach($gr as $g){
              $groups[] = groups_get_group_name($g);
            }
        }
        $groupnames = implode(',', $groups);
    }
}


// write tx to db
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
    die( get_string('error_txdatabase', 'paygw_robokassa') );
}

// your registration data
$mrhlogin = $config->merchant_login;  // your login here
// check test-mode
if ($config->istestmode) {
    $mrhpass1 = $config->test_password1; // merchant test_pass1 here
} else {
    $mrhpass1 = $config->password1;      // merchant pass1 here
}

// check password mode and skipmode
if ( !empty($password) || !empty($skipmode) ) {
    // build redirect
    $url = helper::get_success_url($component, $paymentarea, $itemid);

    if (isset($skipmode)) {
        $password = $config->password;
    }
    // check password
    if ( $password === $config->password ) {
        // make fake pay
        $cost = 0;
        $paymentid = helper::save_payment($payable->get_account_id(), $component, $paymentarea, $itemid, $userid,
                                          $cost, $payable->get_currency(), 'robokassa');
        helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $userid);

        // write to DB
        $data = new stdClass();
        $data->id = $transactionid;
        $data->success = 2;
        $data->cost = 0;
        $DB->update_record('paygw_robokassa', $data);

        redirect($url, get_string('password_success', 'paygw_robokassa'), 0, 'success');
    } else {
        redirect($url, get_string('password_error', 'paygw_robokassa'), 0, 'error');
    }
    die; // never
}

// order properties
$invid    = $transactionid;          // shop's invoice number
// (unique for shop's lifetime)
$invdesc  = $description;  // invoice desc
$outsumm  = $cost;  // invoice summ


$outsumcurrency = null;
$currencyarg = null;
if ( $currency != 'RUB' ) {
    $outsumcurrency = "OutSumCurrency=$currency&";
    $currencyarg = ":$currency";
}

// Build CRC value
$crc = strtoupper(md5("$mrhlogin:$outsumm:$invid".$currencyarg.":$mrhpass1"));


$paymenturl = "https://auth.robokassa.ru/Merchant/Index.aspx?";

redirect($paymenturl."
MerchantLogin=$mrhlogin&
OutSum=$outsumm&$outsumcurrency
InvId=$invid&
Description=".urlencode($invdesc)."&
SignatureValue=$crc&
Culture=".current_language()."&
Email=".urlencode($USER->email)."&
IsTest=".$config->istestmode."
");
