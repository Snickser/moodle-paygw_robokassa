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

require("../../../config.php");
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();

require_login();

$invid = required_param('InvId', PARAM_INT);

$outsumm   = optional_param('OutSum', null, PARAM_TEXT);
$signature = optional_param('SignatureValue', null, PARAM_TEXT);

if (!$robokassatx = $DB->get_record('paygw_robokassa', ['paymentid' => $invid])) {
    throw new Error('FAIL. Not a valid transaction id');
}

if (!$payment = $DB->get_record('payments', ['id' => $robokassatx->paymentid])) {
    throw new Error('FAIL. Not a valid payment.');
}

$paymentarea = $payment->paymentarea;
$component   = $payment->component;
$itemid      = $payment->itemid;

// Build redirect.
$url = helper::get_success_url($component, $paymentarea, $itemid);

if (!isset($signature)) {
    redirect($url, '', 0, '');
}

// Get config.
$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');

if ($config->savedebugdata) {
    file_put_contents($CFG->dataroot . '/payment.log', date("Y-m-d H:i:s") . "\n" . serialize($_REQUEST) . "\n\n", FILE_APPEND | LOCK_EX);
}

// Check test-mode.
if ($config->istestmode) {
    $mrhpass1 = $config->test_password1; // Merchant test_pass2 here.
} else {
    $mrhpass1 = $config->password1;      // Merchant pass2 here.
}

$signature = strtoupper($signature);  // Force uppercase.

// Build own CRC.
$crc = strtoupper(md5("$outsumm:$invid:$mrhpass1"));

// Check crc and redirect.
if ($signature != $crc) {
    throw new Error('FAIL. Not a valid signature.');
}

if ($robokassatx->success) {
    redirect($url, get_string('payment_success', 'paygw_robokassa'), 0, 'success');
} else {
    redirect($url, get_string('payment_error', 'paygw_robokassa'), 0, 'error');
}
