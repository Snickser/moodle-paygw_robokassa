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
            $payable = helper::get_payable($component, $paymentarea, $itemid);
            $surcharge = helper::get_gateway_surcharge('robokassa');// In case user uses surcharge.
            $cost = helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

            $user = \core_user::get_user($userid);

            // Your registration data.
            $mrhlogin = $config->merchant_login;  // Your login here.
            $mrhpass1 = $config->password1;      // Merchant pass1 here.
            $mrhpass2 = $config->password2;

                echo serialize($payable) . "\n";

            // Save payment.
            $newpaymentid = helper::save_payment(
                $userid,
                $component,
                $paymentarea,
                $itemid,
                $userid,
                $cost,
                $payable->get_currency(),
                'robokassa'
            );

            // Make new transaction.
            $newtx = new \stdClass();
            $newtx->paymentid = $newpaymentid;
            $newtx->invoiceid = 'recurrent';
            $newtx->courseid = $data->courseid;
            $newtx->groupnames = $data->groupnames;
            $newtx->timecreated = time();
            $invid = $DB->insert_record('paygw_robokassa', $newtx);
            $newtx->id = $invid;

            // Build CRC value.
            $crc = strtoupper(md5("$mrhlogin:$cost:$invid:$mrhpass1"));

            // Params.
            $request = "MerchantLogin=$mrhlogin" .
              "&InvoiceID=" . $invid .
              "&PreviousInvoiceID=" . $data->paymentid .
              "&Description=" . urlencode("Recurrent payment " . $data->paymentid) .
              "&SignatureValue=$crc" .
              "&OutSum=" . $cost;

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
                // $data->recurrent = 0;
            } else {
                mtrace("$data->paymentid order paid successfully");

                // Deliver order.
                helper::deliver_order($component, $paymentarea, $itemid, $newpaymentid, $userid);

                // Notify user.
                notifications::notify(
                    $userid,
                    $cost,
                    $payment->currency,
                    $newpaymentid,
                    'Recurrent completed'
                );
                $newtx->success = 1;
                $data->recurrent = time() + 86400 * $config->recurrentperiod;
            }
            // Write status.
            $DB->update_record('paygw_robokassa', $data);
            $DB->update_record('paygw_robokassa', $newtx);
        }
        mtrace('End.');
    }
}
