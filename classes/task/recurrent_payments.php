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
 * recurrent payments
 *
 * @package    paygw_robokassa
 * @copyright  2024 Alex Orlov <snicker@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_robokassa\task;

defined('MOODLE_INTERNAL') || die();

use core_payment\helper;
use paygw_robokassa\notifications;

require_once($CFG->libdir . '/filelib.php');

/**
 * Default tasks.
 *
 * @package    paygw_robokassa
 * @copyright  2024 Alex Orlov <snicker@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recurrent_payments extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'paygw_robokassa');
    }
    /**
     * Execute.
     */
    public function execute() {
        global $DB, $CFG;
        mtrace('Start');

        $ctime = strtotime("today");

        $robokassatx = $DB->get_records_sql('SELECT * FROM {paygw_robokassa} WHERE success=1 ' .
                  'AND recurrent>0 AND recurrent < ?', [ $ctime ]);

        foreach ($robokassatx as $data) {
            // To avoid abuse.
            sleep(1);

            // Get payment data.
            if (!$payment = $DB->get_record('payments', ['id' => $data->paymentid])) {
                continue;
            }

            $component   = $payment->component;
            $paymentarea = $payment->paymentarea;
            $itemid      = $payment->itemid;
            $paymentid   = $payment->id;
            $userid      = $payment->userid;

            // Get config.
            $config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');

            $user = \core_user::get_user($userid);

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

                echo serialize($data) . "\n";


// Make new transaction.
$data->invoiceid = 'recurrent';
//$invid = $DB->insert_record('paygw_robokassa', $data);

// Build CRC value.
//$crc = strtoupper(md5("$mrhlogin:$payment->amount:$invid:$mrhpass1"));

// Params.
$request = "MerchantLogin=$mrhlogin" .
    "&InvoiceID=" . $invid .
    "&PreviousInvoiceID=" . $data->paymentid .
    "&Description=" . urlencode("Recurrent payment " . $data->paymentid) .
    "&SignatureValue=$crc" .
    "&OutSum=" . $payment->amount;

// Make invoice.
$location = 'https://auth.robokassa.ru/Merchant/Recurring';
$options = [
    'CURLOPT_RETURNTRANSFER' => true,
    'CURLOPT_TIMEOUT' => 30,
    'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
    'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
];
$curl = new \curl();
$response = $curl->post($location, $request, $options);

            if (($response !== "OK$invid")) {
                echo serialize($response) . "\n";
                mtrace("$data->paymentid is not valid");
                $data->recurrent = 0;
                $DB->update_record('paygw_robokassa', $data);
            } else {
                mtrace("$data->paymentid order paid successfully");




            }
        }
        mtrace('End.');
    }
}
