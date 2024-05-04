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
 * Privacy Subsystem implementation for paygw_robokassa.
 *
 * @package    paygw_robokassa
 * @category   privacy
 * @copyright  2024 Alex Orlov <snicker@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_robokassa\privacy;

use core_payment\privacy\paygw_provider;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation for paygw_robokassa.
 *
 * @copyright  2024 Alex Orlov <snicker@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\request\data_provider, paygw_provider, \core_privacy\local\metadata\provider {
    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {

        // Data may be exported to an external location.
        $collection->add_external_location_link(
            'robokassa.plus',
            [
                'shopid'    => 'privacy:metadata:paygw_robokassa:shopid',
                'paymentid' => 'privacy:metadata:paygw_robokassa:paymentid',
                'email'     => 'privacy:metadata:paygw_robokassa:email',
                'userip'    => 'privacy:metadata:paygw_robokassa:userip',
            ],
            'privacy:metadata:paygw_robokassa:robokassa_plus'
        );

        // The paygw_robokassa has a database table that contains user data.
        $collection->add_database_table(
            'paygw_robokassa',
            [
                'invoiceid'  => 'privacy:metadata:paygw_robokassa:invoiceid',
                'courceid'   => 'privacy:metadata:paygw_robokassa:courceid',
                'groupnames' => 'privacy:metadata:paygw_robokassa:groupnames',
                'success'    => 'privacy:metadata:paygw_robokassa:success',
            ],
            'privacy:metadata:paygw_robokassa:paygw_robokassa'
        );
        return $collection;
    }


    /**
     * Export all user data for the specified payment record, and the given context.
     *
     * @param \context $context Context
     * @param array $subcontext The location within the current context that the payment data belongs
     * @param \stdClass $payment The payment record
     */
    public static function export_payment_data(\context $context, array $subcontext, \stdClass $payment) {
        global $DB;

        $subcontext[] = get_string('gatewayname', 'paygw_robokassa');
        $record = $DB->get_record('paygw_robokassa', ['paymentid' => $payment->id]);

        $data = (object) [
            'orderid' => $record->invoiceid,
        ];
        writer::with_context($context)->export_data(
            $subcontext,
            $data
        );
    }

    /**
     * Delete all user data related to the given payments.
     *
     * @param string $paymentsql SQL query that selects payment.id field for the payments
     * @param array $paymentparams Array of parameters for $paymentsql
     */
    public static function delete_data_for_payment_sql(string $paymentsql, array $paymentparams) {
        global $DB;

        $DB->delete_records_select('paygw_robokassa', "paymentid IN ({$paymentsql})", $paymentparams);
    }
}
