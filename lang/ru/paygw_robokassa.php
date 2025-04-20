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
 * Local language pack from https://study.bhuri.ru
 *
 * @package    paygw_robokassa
 * @subpackage robokassa
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['abouttopay'] = 'You are about to donate to';
$string['amount'] = 'Amount:';
$string['callback_help'] = 'Copy this and put it in the URL addresses of the technical settings of the Robokassa store, sending method - POST.';
$string['callback_url'] = 'ResultURL:';
$string['checkinvoice'] = 'Invoice pre-check';
$string['checkinvoice_help'] = 'This option enables additional invoice verification in Robokassa when creating and receiving a payment. When creating, it checks that such payment does not already exist, and when receiving confirmation from the bank, a request is made to verify the fact of payment of the issued invoice.';
$string['cost'] = 'Enrollment cost';
$string['crypto'] = 'Hash calculation algorithm';
$string['crypto_help'] = 'Method used to calculate the hash of payment parameter checksums, which ensures security during payment processing and data integrity. <b>Make sure this exact algorithm is specified in the store settings, otherwise nothing will work!</b>';
$string['currency'] = 'Currency';
$string['donate'] = '<div>Plugin version: {$a->release} ({$a->versiondisk})<br>
New plugin versions can be found on <a href=https://github.com/Snickser/moodle-paygw_robokassa>GitHub.com</a>
<img src="https://img.shields.io/github/v/release/Snickser/moodle-paygw_robokassa.svg"><br>
Please send me a little <a href="https://yoomoney.ru/fundraise/143H2JO3LLE.240720">donation</a>😊</div>
TRX TRGMc3b63Lus6ehLasbbHxsb2rHky5LbPe<br>
BTC 1GFTTPCgRTC8yYL1gU7wBZRfhRNRBdLZsq<br>
ETH 0x1bce7aadef39d328d262569e6194febe597cb2c9<br>
<iframe src="https://yoomoney.ru/quickpay/fundraise/button?billNumber=143H2JO3LLE.240720"
width="330" height="50" frameborder="0" allowtransparency="true" scrolling="no"></iframe><br>
';
$string['enrolperiod'] = 'Duration of training ({$a->desc}): {$a->count}';
$string['error_txdatabase'] = 'Error write TX data to database';
$string['fixcost'] = 'Fixed price mode';
$string['fixcost_help'] = 'Disables the ability for students to pay with an arbitrary amount.';
$string['fixdesc'] = 'Fixed payment comment';
$string['fixdesc_help'] = 'This setting sets a fixed comment for all payments and disables the display of the comment description on the payment page.';
$string['gatewaydescription'] = 'Robokassa is an authorized payment gateway for processing credit card transactions.';
$string['gatewayname'] = 'Robokassa';
$string['inccurrlabel'] = 'Payment method';
$string['istestmode'] = 'Test mode';
$string['istestmode_help'] = 'There are no recurring payments in test mode.';
$string['maxcost'] = 'Maximum price';
$string['maxcosterror'] = 'Maximum price must be higher than suggested price';
$string['merchant_login'] = 'Store ID';
$string['message_invoice_created'] = 'Hello, {$a->firstname}!
Payment invoice by link {$a->orderid} for {$a->localizedcost} has been successfully created.
You can make a payment using this link within 30 minutes.
If you receive error code 40 during payment, it means the invoice has already been paid, and error code 33 indicates that the payment time has expired.';
$string['message_recurrent_created'] = 'Hello, {$a->firstname}!
Recurring payment № {$a->orderid} for {$a->localizedcost} has been created and sent to the bank.
You can disable recurring payments in the Reports (payment) section in your personal profile {$a->url}/user/profile.php';
$string['message_recurrent_error'] = 'Hello, {$a->firstname}!
Recurring payment № {$a->orderid} for {$a->localizedcost} ended with an error.
The subscription has been disabled; to resume the subscription, make a new payment.';
$string['message_recurrent_notify'] = 'Hello, {$a->firstname}!
We remind you that the date of recurring payment № {$a->orderid} for {$a->localizedcost} is approaching.
Please ensure the specified amount is available in your account, otherwise the subscription will not be renewed.
You can disable recurring payments in the Reports (payment) section in your personal profile {$a->url}/user/profile.php';
$string['message_success_completed'] = 'Hello, {$a->firstname}!
Your payment transaction № {$a->orderid} for {$a->fee} {$a->currency} has been successfully completed. Thank you for your donation.
If the course element is not available, please contact the site support.';
$string['message_success_recurrent'] = 'Hello, {$a->firstname}!
Recurring payment transaction № {$a->orderid} for {$a->localizedcost} has been successfully created. Thank you for your donation.
The next automatic payment is scheduled for {$a->nextpay}.
You can disable recurring payments in the Reports (payment) section in your personal profile {$a->url}/user/profile.php';
$string['messagesubject'] = 'Payment notification ({$a})';
$string['noreportplugin'] = '<font color=red>The report_payments plugin is not installed, you will not be able to cancel recurring payments.</font>';
$string['password'] = 'Backup password';
$string['password1'] = 'Password1';
$string['password2'] = 'Password2';
$string['password_error'] = 'Incorrect payment password entered';
$string['password_help'] = 'This password can bypass the payment process. May be useful when there is no way to make a payment.';
$string['password_mode'] = 'Allow backup password input';
$string['password_success'] = 'Payment password accepted';
$string['password_text'] = 'If you do not have the opportunity to make a donation, ask your curator for the password and enter it.';
$string['payment'] = 'Donation';
$string['payment_error'] = 'Payment creation error, please try again or contact technical support.';
$string['payment_success'] = 'Payment successfully completed';
$string['payment_server'] = 'Payment server URL';
$string['paymentsystem'] = 'Payment system';
$string['paymore'] = 'If you want to donate more, just enter your amount instead of the specified one.';
$string['plastic'] = 'VISA, MasterCard, MIR';
$string['pluginname'] = 'Robokassa payments';
$string['pluginname_desc'] = 'The plugin allows you to receive payments through Robokassa.';
$string['recurrent'] = 'Enable recurring payments';
$string['recurrent_help'] = 'Recurring payments are executed on a timer without student participation, the data of the first payment is stored on the bank side and used repeatedly, with some periodicity.
<br><b>Works only for rubles (without conversion)! Incorrect configuration can lead to emptying of clients\' bank accounts!</b>';
$string['recurrentcost'] = 'Recurring payment cost';
$string['recurrentcost1'] = 'Paid';
$string['recurrentcost2'] = 'Item cost';
$string['recurrentcost3'] = 'Suggested price';
$string['recurrentcost_help'] = 'Specifies which price to take when making a recurring payment:<br>
Paid - the one that was specified by the user when creating the recurring payment.<br>
Item cost - the one specified in the payment module or course settings.<br>
Suggested - taken from the settings of this interface.';
$string['recurrentday'] = 'Day of monthly payment';
$string['recurrentday_help'] = 'Specifies the day of the month on which subsequent debits will occur. If not set, payments will be made on a cyclic schedule';
$string['recurrentdesc'] = 'This payment will be recurring! The amount and date of the next payment may change.<br>
You can disable recurring payments in your Profile in the Reports "Payment and recurring subscriptions" section.';
$string['recurrentperiod'] = 'Recurring payment frequency';
$string['recurrentperiod_help'] = 'It is advisable to specify a frequency of no more than once a day. This is related to the execution of the corresponding scheduled task in the task scheduler. More often than once a day - only for tests.';
$string['recurrentperioderror'] = 'Specify frequency';
$string['return_url'] = 'SuccessURL and FailURL:';
$string['robokassa'] = 'Robokassa';
$string['sbp'] = 'SBP';
$string['sendlinkmsg'] = 'Send payment link to email';
$string['sendlinkmsg_help'] = 'If enabled, the link to the payment invoice will be sent to the user\'s email.';
$string['sendpaymentbutton'] = 'Donate!';
$string['showduration'] = 'Show training duration on page';
$string['skipmode'] = 'Show payment skip button';
$string['skipmode_help'] = 'This setting enables the payment skip button, may be useful in public courses with optional payment.';
$string['skipmode_text'] = 'If you do not have the opportunity to make a donation through the payment system, you can click the button below.';
$string['skippaymentbutton'] = 'I can\'t afford it :(';
$string['sno'] = 'Taxation type';
$string['sno_help'] = 'Type of taxation system for generating receipts:<br>
1 - General taxation system<br>
2 - Simplified (USN, income)<br>
3 - Simplified (USN, income minus expenses)<br>
4 - Unified agricultural tax (UAT)<br>
5 - Patent taxation system';
$string['suggest'] = 'Suggested price';
$string['suggesterror'] = 'Suggested price must be specified for enabled recurring payment';
$string['tax'] = 'VAT rate';
$string['tax_help'] = 'VAT rate according to Robokassa API documentation.';
$string['testpassword1'] = 'Test Password1';
$string['testpassword2'] = 'Test Password2';
$string['uninterrupted_desc'] = 'The price for the course is formed taking into account the missed time of the period not paid by you.';
$string['usedetails'] = 'Show collapsed';
$string['usedetails_help'] = 'Hides the button or password under a collapsible block if they are enabled.';
$string['usedetails_text'] = 'Click here if you don\'t have the opportunity to make a donation';
