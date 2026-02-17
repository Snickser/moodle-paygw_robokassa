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
 * Strings for component 'paygw_monobank', language 'en'
 *
 * @package    paygw_monobank
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['abouttopay'] = 'You are about to pay for';
$string['amount'] = 'Amount:';
$string['api_token'] = 'API Token (X-Token)';
$string['api_token_help'] = 'The API token from your MonoBank merchant account. You can get it at https://web.monobank.ua/ or use a test token from https://api.monobank.ua/';
$string['callback_help'] = 'This URL is automatically sent to MonoBank when creating an invoice. No manual setup is required.';
$string['callback_url'] = 'WebHook URL:';
$string['donate'] = '<div>Plugin version: {$a->release} ({$a->versiondisk})</div>';
$string['enrolperiod'] = 'Enrolment duration ({$a->desc}): {$a->count}';
$string['error_notvalidpayment'] = 'FAIL. Not a valid payment';
$string['error_notvalidtxid'] = 'FAIL. Not a valid transaction id';
$string['error_txdatabase'] = 'Error write TX data to database';
$string['fixcost'] = 'Fixed price mode';
$string['fixcost_help'] = 'Disables the ability for students to pay with an arbitrary amount.';
$string['fixdesc'] = 'Fixed payment comment';
$string['fixdesc_help'] = 'This setting sets a fixed comment for all payments.';
$string['gatewaydescription'] = 'MonoBank is a Ukrainian payment gateway provider for processing card transactions.';
$string['gatewayname'] = 'MonoBank';
$string['internalerror'] = 'An internal error has occurred. Please contact us.';
$string['istestmode'] = 'Test mode';
$string['istestmode_help'] = 'In test mode, any card number valid according to the Luhn algorithm can be used for payment.';
$string['maxcost'] = 'Maximum cost';
$string['maxcosterror'] = 'The maximum price must be higher than the recommended price';
$string['message_invoice_created'] = 'Hello {$a->firstname}!
Your payment link {$a->orderid} for {$a->localizedcost} has been successfully created.';
$string['message_success_completed'] = 'Hello {$a->firstname},
Your payment (id {$a->orderid}) of {$a->localizedcost} has been successfully completed.
If the item is not accessible, please contact the administrator.';
$string['messageprovider:payment_receipt'] = 'Payment receipt';
$string['messagesubject'] = 'Payment notification ({$a})';
$string['password'] = 'Password';
$string['password_error'] = 'Invalid payment password';
$string['password_help'] = 'Using this password you can bypass the payment process. It can be useful when it is not possible to make a payment.';
$string['password_success'] = 'Payment password accepted';
$string['password_text'] = 'If you are unable to make a payment, ask your curator for a password and enter it.';
$string['passwordmode'] = 'Password';
$string['payment'] = 'Payment';
$string['payment_error'] = 'Payment Error';
$string['payment_success'] = 'Payment Successful';
$string['paymore'] = 'If you want to pay more, simply enter your amount instead of the indicated amount.';
$string['pluginname'] = 'MonoBank payment';
$string['pluginname_desc'] = 'The MonoBank plugin allows you to receive payments via MonoBank acquiring.';
$string['privacy:metadata'] = 'The MonoBank plugin stores some personal data.';
$string['privacy:metadata:paygw_monobank:courseid'] = 'Course id';
$string['privacy:metadata:paygw_monobank:email'] = 'Email';
$string['privacy:metadata:paygw_monobank:groupnames'] = 'Group names';
$string['privacy:metadata:paygw_monobank:invoiceid'] = 'Invoice id';
$string['privacy:metadata:paygw_monobank:monobank_ua'] = 'Send data to MonoBank for payment processing';
$string['privacy:metadata:paygw_monobank:paygw_monobank'] = 'Stores transaction data';
$string['privacy:metadata:paygw_monobank:paymentid'] = 'Payment id';
$string['privacy:metadata:paygw_monobank:success'] = 'Status';
$string['sendlinkmsg'] = 'Send payment link by email';
$string['sendlinkmsg_help'] = 'If enabled, a link to the invoice for payment will be sent to the user\'s email.';
$string['sendpaymentbutton'] = 'Pay via MonoBank!';
$string['showduration'] = 'Show duration of training';
$string['skipmode'] = 'Can skip payment';
$string['skipmode_help'] = 'This setting allows a payment bypass button, which can be useful in public courses with optional payment.';
$string['skipmode_text'] = 'If you are not able to make a payment through the payment system, you can click on this button.';
$string['skippaymentbutton'] = 'Skip payment :(';
$string['suggest'] = 'Suggested cost';
$string['usedetails'] = 'Make it collapsible';
$string['usedetails_help'] = 'Display a button or password in a collapsed block.';
$string['usedetails_text'] = 'Click here if you are unable to pay.';
