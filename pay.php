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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;

require_once(__DIR__ . '/../../../config.php');
require_login();

global $CFG, $USER, $DB;

$component   = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid      = required_param('itemid', PARAM_INT);
$description = required_param('description', PARAM_TEXT);

$description = json_decode("\"$description\"");

if ( isset($_REQUEST['cost_self']) ) {
    $cost = number_format($_REQUEST['cost_self'], 2, '.', '');
}

$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');
$payable = helper::get_payable($component, $paymentarea, $itemid);// Get currency and payment amount.
$currency = $payable->get_currency();
$surcharge = helper::get_gateway_surcharge('robokassa');// In case user uses surcharge.

// TODO: Check if currency is IDR. If not, then something went really wrong in config.
$cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

if ( isset($_REQUEST['cost_self']) ) {
    $cost = $_REQUEST['cost_self'];
}
$cost = number_format($cost, 2, '.', '');

// write tx to db
$paygwdata = new stdClass();
$paygwdata->userid = $USER->id;
$paygwdata->component = $component;
$paygwdata->paymentarea = $paymentarea;
$paygwdata->itemid = $itemid;
$paygwdata->cost = $cost;
$paygwdata->currency = $currency;
$paygwdata->date_created = time();


if (!$transaction_id = $DB->insert_record('paygw_robokassa', $paygwdata)) {
    print_error('error_txdatabase', 'paygw_robokassa');
}
$id = $transaction_id;

// make signature
$mntsignature = md5($config->mntid.$transaction_id.$cost.$currency.$config->mnttestmode.$config->mntdataintegritycode);

$paymenturl = "https://".$config->paymentserver."/assistant.htm?";

$paymentsystem = explode('_', $config->paymentsystem);
$paymentsystemparams = "";
if (!empty($paymentsystem[2]))
{
    $paymentsystemparams .= "paymentSystem.unitId={$paymentsystem[2]}&";
}
if (isset($paymentsystem[3]) && !empty($paymentsystem[3]))
{
    $paymentsystemparams .= "paymentSystem.accountId={$paymentsystem[3]}&";
}

redirect($paymenturl."
	MNT_ID={$config->mntid}&
	MNT_TRANSACTION_ID={$transaction_id}&
	MNT_CURRENCY_CODE={$currency}&
	MNT_AMOUNT={$cost}&
	MNT_SIGNATURE={$mntsignature}&
	MNT_SUCCESS_URL=".urlencode($CFG->wwwroot."/payment/gateway/robokassa/return.php?id=".$id)."&
	MNT_FAIL_URL=".urlencode($CFG->wwwroot."/payment/gateway/robokassa/return.php?id=".$id)."&
	MNT_CUSTOM1=".urlencode($component.":".$paymentarea.":".$itemid)."&
	MNT_CUSTOM2=".urlencode(fullname($USER))."&
	MNT_CUSTOM3=".urlencode($USER->email)."&
	MNT_DESCRIPTION=".get_string('payment','paygw_robokassa')."&
	pawcmstype=moodle&
	moneta.locale=".current_language()."&
	followup=true&
	{$paymentsystemparams}
");
