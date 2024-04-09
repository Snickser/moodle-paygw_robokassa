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

$invid    = required_param('InvId', PARAM_TEXT);

$outsumm  = optional_param('OutSum', null, PARAM_TEXT);
$signature = optional_param('SignatureValue', null, PARAM_TEXT);


if (!$robokassatx = $DB->get_record('paygw_robokassa', ['id' => $invid])) {
    die('FAIL. Not a valid transaction id');
}

$paymentarea = $robokassatx->paymentarea;
$component   = $robokassatx->component;
$itemid      = $robokassatx->itemid;

// Build redirect
$url = helper::get_success_url($component, $paymentarea, $itemid);

if (!isset($signature)) {
    redirect($url, '', 0, '');
    die;
}

// Get config
$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');

// Check test-mode
if ($config->istestmode) {
    $mrhpass1 = $config->test_password1; // Merchant test_pass2 here
} else {
    $mrhpass1 = $config->password1;      // Merchant pass2 here
}

$signature = strtoupper($signature);  // Force uppercase

// Build own CRC
$crc = strtoupper(md5("$outsumm:$invid:$mrhpass1"));

// Check crc and redirect
if ($signature == $crc && $robokassatx->success) {
    redirect($url, get_string('payment_success', 'paygw_robokassa'), 0, 'success');
} else {
    redirect($url, get_string('payment_error', 'paygw_robokassa'), 0, 'error');
}
