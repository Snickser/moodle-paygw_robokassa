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
 * Strings for component 'paygw_robokassa', language 'en'
 *
 * @package    paygw_robokassa
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Robokassa payment';
$string['pluginname_desc'] = 'The Robokassa plugin allows you to receive payments via Robokassa.';
$string['gatewaydescription'] = 'Robokassa is an authorised payment gateway provider for processing credit card transactions.';
$string['gatewayname'] = 'Robokassa';
$string['callback_url'] = 'ResultURL:';
$string['callback_help'] = 'Copy this and put it in URLs at your Robokassa account.';
$string['return_url'] = 'SuccessURL and FailURL:';
$string['payment_success'] = 'Payment Successful';
$string['payment_error'] = 'Payment Error';
$string['password_success'] = 'Paymemt password accepted';
$string['password_error'] = 'Invalid payment password';
$string['paymore'] = 'If you want to donate more, simply enter your amount instead of the indicated amount.';
$string['sendpaymentbutton'] = 'Send payment via Robokassa!';
$string['skippaymentbutton'] = 'Skip payment :(';
$string['abouttopay'] = 'You are about to pay for';
$string['payment'] = 'Donation';
$string['password'] = 'Password';
$string['passwordmode'] = 'Password';
$string['password_help'] = 'Using this password you can bypass the payback process. It can be useful when it is not possible to make a payment.';
$string['password_text'] = 'If you are unable to make a payment, then ask your curator for a password and enter it.';
$string['password1'] = 'Password1';
$string['password2'] = 'Password2';
$string['merchant_login'] = 'Merchant login';
$string['istestmode'] = 'Test mode';
$string['istestmode_help'] = 'There are no recurring payments in test mode.';
$string['suggest'] = 'Suggested cost';
$string['maxcost'] = 'Maximium cost';
$string['skipmode'] = 'Can skip payment';
$string['skipmode_help'] = 'This setting allows a payment bypass button, which can be useful in public courses with optional payment.';
$string['skipmode_text'] = 'If you are not able to make a donation through the payment system, you can click on this button.';
$string['fixdesc'] = 'Fixed payment comment';
$string['fixdesc_help'] = 'This setting sets a fixed comment for all payments.';
$string['showduration'] = 'Show duration of training';
$string['usedetails'] = 'Make it collapsible';
$string['usedetails_help'] = 'Display a button or password in a collapsed block.';
$string['usedetails_text'] = 'Click here if you are unable to donate.';
$string['inccurrlabel'] = 'Payment method';

/* Payment systems */
$string['paymentsystem'] = 'Payment system';
$string['robokassa'] = 'Robokassa';
$string['plastic'] = 'VISA, MasterCard, MIR';
$string['wallet'] = 'Wallet One';
$string['sbp'] = 'SBP';

$string['internalerror'] = 'An internal error has occurred. Please contact us.';
$string['privacy:metadata'] = 'The Robokassa plugin does not store any personal data.';

$string['tax'] = 'VAT rate';
$string['tax_help'] = 'VAT rate according to YooKass documentation.';
$string['sno'] = 'Tax type';
$string['sno_help'] = 'Type of tax system for generating checks:<br>
1 - General taxation system<br>
2 - Simplified (STS, income)<br>
3 - Simplified (STS, income minus expenses)<br>
4 - Unified Agricultural Tax (UST)<br>
5 - Patent taxation system';

$string['privacy:metadata'] = 'The robokassa plugin store some personal data.';
$string['privacy:metadata:paygw_robokassa:paygw_robokassa'] = 'Store some data';
$string['privacy:metadata:paygw_robokassa:shopid'] = 'Shopid';
$string['privacy:metadata:paygw_robokassa:paymentid'] = 'Paymentid';
$string['privacy:metadata:paygw_robokassa:email'] = 'Email';
$string['privacy:metadata:paygw_robokassa:userip'] = 'Userip';
$string['privacy:metadata:paygw_robokassa:robokassa_plus'] = 'Send json data';
$string['privacy:metadata:paygw_robokassa:invoiceid'] = 'Invoice id';
$string['privacy:metadata:paygw_robokassa:courceid'] = 'Cource id';
$string['privacy:metadata:paygw_robokassa:groupnames'] = 'Group names';
$string['privacy:metadata:paygw_robokassa:success'] = 'Status';

$string['messagesubject'] = 'Payment notification ({$a})';

$string['message_success_completed'] = 'Hello {$a->firstname},
You transaction of payment id {$a->orderid} with cost of {$a->localizedcost} is successfully completed.
If the item is not accessable please contact the administrator.';

$string['message_success_completed'] = 'Hello {$a->firstname},
You transaction of payment id {$a->orderid} with cost of {$a->fee} {$a->currency} for "{$a->description}" is successfully completed.
If the item is not accessable please contact the administrator.';

$string['message_recurrent_created'] = 'Hello, {$a->firstname}!
Regular payment No. {$a->orderid} at {$a->localizedcost} is ready for payment.
You can disable regular payments in the Reports (payment) section in your personal profile {$a->$url}/user/profile.php';

$string['message_success_recurrent'] = 'Hello, {$a->firstname}!
Your regular payment transaction No. {$a->orderid} for {$a->localizedcost} has been successfully created. Thank you for your donation.
You can disable regular payments in the Reports (payment) section in your personal profile {$a->$url}/user/profile.php';

$string['message_recurrent_error'] = 'Hello, {$a->firstname}!
Regular payment No. {$a->orderid} for {$a->localizedcost} completed with an error.
The subscription has been disabled, please make a new payment to renew your subscription.';

$string['message_recurrent_notify'] = 'Hello, {$a->name}!
We remind you that the date of regular payment No. {$a->orderid} for {$a->localizedcost} is obtained.
Please ensure that the specified amount is available in your account, otherwise the subscription will not be renewed.
You can disable regular payments in the Reports (payments) section in your personal profile {$a->url}/user/profile.php';

$string['message_invoice_created'] = 'Hello {$a->firstname}!
Your payment link {$a->orderid} to {$a->fee} {$a->currency} has been successfully created.
You can make a payment using this link within 30 minutes.';

$string['messageprovider:payment_receipt'] = 'Payment receipt';

$string['checkinvoice'] = 'Check invoice';
$string['checkinvoice_help'] = 'This option includes additional verification of the invoice in Robokassa when creating and receiving a payment.
When creating, it is checked that such payment does not exist.
And upon receipt of payment, a request is made to verify payment of the invoice.';

$string['sendlinkmsg'] = 'Send payment link by email';
$string['sendlinkmsg_help'] = 'If enabled, a link to the invoice for payment will be sent to the users email.';

$string['savedebugdata'] = 'Save debug log';
$string['savedebugdata_help'] = 'Bank request and response data will be saved in {dataroot}/payment.log';

$string['fixcost'] = 'Fixed price mode';
$string['fixcost_help'] = 'Disables the ability for students to pay with an arbitrary amount.';
$string['maxcosterror'] = 'The maximum price must be higher than the recommended price';

$string['recurrent'] = 'Enable recurring payments';
$string['recurrent_help'] = 'Enable recurring payments';
$string['recurrentperiod'] = 'Recurring payment frequency';
$string['recurrentperiod_help'] = 'It is advisable to specify the frequency no more than once a day. This involves executing the corresponding regular task in the task scheduler. More than once a day - only for tests.';
$string['recurrentperioderror'] = 'Specify the frequency';
$string['recurrentday'] = 'Day of payments';
$string['recurrentday_help'] = 'Sets the day of the month on which the next debits will occur. If not set, payments will be made according to a cyclic schedule';
$string['recurrentdesc'] = 'This payment will be recurring! The amount and date of the next payment may change.<br>
You can disable recurring payments in your Profile in the Reports section "Payment and recurring subscriptions".';

$string['noreportplugin'] = '<font color=red>The report_payments plugin is not installed, you will not be able to cancel recurring payments.</font>';

$string['recurrentcost'] = 'Recurring payment cost';
$string['recurrentcost_help'] = 'Specify what price to charge when making a recurring payment:<br>
Paid - the one that was specified by the user when creating a regular payment.<br>
Item cost - the one specified in the settings of the payment module or course.<br>
Recommended - taken from the settings of this interface.';
$string['recurrentcost1'] = 'Paid';
$string['recurrentcost2'] = 'Item cost';
$string['recurrentcost3'] = 'Recommended price';
$string['suggesterror'] = 'Suggested price must be for recurring payment enabled';

$string['donate'] = '<div>Plugin version: {$a->release} ({$a->versiondisk})<br>
You can find new versions of the plugin at <a href=https://github.com/Snickser/moodle-paygw_robokassa>GitHub.com</a>
<img src="https://img.shields.io/github/v/release/Snickser/moodle-paygw_robokassa.svg"><br>
Please send me some <a href="https://yoomoney.ru/fundraise/143H2JO3LLE.240720">donate</a>😊</div>
<iframe src="https://yoomoney.ru/quickpay/fundraise/button?billNumber=143H2JO3LLE.240720"
width="330" height="50" frameborder="0" allowtransparency="true" scrolling="no"></iframe>';

$string['error_txdatabase'] = 'Error write TX data to database';
$string['error_notvalidtxid'] = 'FAIL. Not a valid transaction id';
$string['error_notvalidpayment'] = 'FAIL. Not a valid payment';
$string['error_notvalidsignature'] = 'FAIL. Not a valid signature';

