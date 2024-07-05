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
 * Contains class for robokassa payment gateway.
 *
 * @package    paygw_robokassa
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_robokassa;

/**
 * The gateway class for robokassa payment gateway.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_payment\gateway {
    /**
     * Configuration form for currency
     */
    public static function get_supported_currencies(): array {
        // 3-character ISO-4217: https://en.wikipedia.org/wiki/ISO_4217#Active_codes.
        return [
            'RUB', 'USD', 'EUR', 'KZT',
        ];
    }

    /**
     * Configuration form for the gateway instance
     *
     * Use $form->get_mform() to access the \MoodleQuickForm instance
     *
     * @param \core_payment\form\account_gateway $form
     */
    public static function add_configuration_to_gateway_form(\core_payment\form\account_gateway $form): void {
        $mform = $form->get_mform();

        $mform->addElement('text', 'merchant_login', get_string('merchant_login', 'paygw_robokassa'));
        $mform->setType('merchant_login', PARAM_TEXT);

        $mform->addElement('text', 'password1', get_string('password1', 'paygw_robokassa'), ['size' => 24]);
        $mform->setType('password1', PARAM_TEXT);
        $mform->disabledIf('password1', 'istestmode', "neq", 0);

        $mform->addElement('text', 'password2', get_string('password2', 'paygw_robokassa'), ['size' => 24]);
        $mform->setType('password2', PARAM_TEXT);
        $mform->disabledIf('password2', 'istestmode', "neq", 0);

        $options = [
        'osn' => 1,
        'usn_income' => 2,
        'usn_income_outcome' => 3,
        'esn' => 4,
        'patent' => 5,
        ];
        $mform->addElement(
            'select',
            'sno',
            get_string('sno', 'paygw_robokassa'),
            $options
        );
        $mform->setType('sno', PARAM_TEXT);
        $mform->addHelpButton('sno', 'sno', 'paygw_robokassa');

        $options = [
        'none' => get_string('no'),
        'vat0' => "0%",
        'vat10' => "10%",
        'vat110' => "10/110",
        'vat20' => "20%",
        'vat220' => "20/120",
        ];
        $mform->addElement(
            'select',
            'tax',
            get_string('tax', 'paygw_robokassa'),
            $options,
        );
        $mform->setType('tax', PARAM_TEXT);
        $mform->addHelpButton('tax', 'tax', 'paygw_robokassa');

        $mform->addElement(
            'advcheckbox',
            'recurrent',
            get_string('recurrent', 'paygw_robokassa'),
            get_string('recurrent', 'paygw_robokassa')
        );
        $mform->setType('recurrent', PARAM_INT);
        $mform->addHelpButton('recurrent', 'recurrent', 'paygw_robokassa');
        $mform->disabledIf('recurrent', 'istestmode', "neq", 0);

        $mform->addElement('duration','recurrentperiod',get_string('recurrentperiod', 'paygw_robokassa'));
        $mform->setType('recurrentperiod', PARAM_TEXT);
        $mform->hideIf('recurrentperiod', 'recurrent', "neq", 1);
        $mform->disabledIf('recurrentperiod', 'istestmode', "neq", 0);

        $mform->addElement(
            'advcheckbox',
            'istestmode',
            get_string('istestmode', 'paygw_robokassa'),
            get_string('istestmode', 'paygw_robokassa')
        );
        $mform->setType('istestmode', PARAM_INT);

        $mform->addElement('text', 'test_password1', get_string('password1', 'paygw_robokassa'), ['size' => 24]);
        $mform->setType('test_password1', PARAM_TEXT);
        $mform->disabledIf('test_password1', 'istestmode');

        $mform->addElement('text', 'test_password2', get_string('password2', 'paygw_robokassa'), ['size' => 24]);
        $mform->setType('test_password2', PARAM_TEXT);
        $mform->disabledIf('test_password2', 'istestmode');

        $mform->addElement(
            'advcheckbox',
            'checkinvoice',
            get_string('checkinvoice', 'paygw_robokassa'),
            get_string('checkinvoice', 'paygw_robokassa')
        );
        $mform->setType('checkinvoice', PARAM_INT);
        $mform->addHelpButton('checkinvoice', 'checkinvoice', 'paygw_robokassa');

        $mform->addElement(
            'advcheckbox',
            'sendlinkmsg',
            get_string('sendlinkmsg', 'paygw_robokassa'),
            get_string('sendlinkmsg', 'paygw_robokassa')
        );
        $mform->setType('sendlinkmsg', PARAM_INT);
        $mform->addHelpButton('sendlinkmsg', 'sendlinkmsg', 'paygw_robokassa');

        $mform->addElement(
            'advcheckbox',
            'savedebugdata',
            get_string('savedebugdata', 'paygw_robokassa'),
            get_string('savedebugdata', 'paygw_robokassa')
        );
        $mform->setType('savedebugdata', PARAM_INT);
        $mform->addHelpButton('savedebugdata', 'savedebugdata', 'paygw_robokassa');

        $mform->addElement('text', 'fixdesc', get_string('fixdesc', 'paygw_robokassa'), ['size' => 50]);
        $mform->setType('fixdesc', PARAM_TEXT);
        $mform->addRule('fixdesc', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('fixdesc', 'fixdesc', 'paygw_robokassa');

        $mform->addElement('static');

        $mform->addElement(
            'advcheckbox',
            'skipmode',
            get_string('skipmode', 'paygw_robokassa'),
            get_string('skipmode', 'paygw_robokassa')
        );
        $mform->setType('skipmode', PARAM_INT);
        $mform->addHelpButton('skipmode', 'skipmode', 'paygw_robokassa');

        $mform->addElement(
            'advcheckbox',
            'passwordmode',
            get_string('passwordmode', 'paygw_robokassa'),
            get_string('passwordmode', 'paygw_robokassa')
        );
        $mform->setType('passwordmode', PARAM_INT);
        $mform->disabledIf('passwordmode', 'skipmode', "neq", 0);

        $mform->addElement('text', 'password', get_string('password', 'paygw_robokassa'), ['size' => 20]);
        $mform->setType('password', PARAM_TEXT);
        $mform->disabledIf('password', 'passwordmode');
        $mform->disabledIf('password', 'skipmode', "neq", 0);
        $mform->addHelpButton('password', 'password', 'paygw_robokassa');

        $mform->addElement(
            'advcheckbox',
            'usedetails',
            get_string('usedetails', 'paygw_robokassa'),
            get_string('usedetails', 'paygw_robokassa')
        );
        $mform->setType('usedetails', PARAM_INT);
        $mform->addHelpButton('usedetails', 'usedetails', 'paygw_robokassa');

        $mform->addElement(
            'advcheckbox',
            'showduration',
            get_string('showduration', 'paygw_robokassa'),
            get_string('showduration', 'paygw_robokassa')
        );
        $mform->setType('showduration', PARAM_INT);

        $mform->addElement(
            'advcheckbox',
            'fixcost',
            get_string('fixcost', 'paygw_robokassa'),
            get_string('fixcost', 'paygw_robokassa')
        );
        $mform->setType('fixcost', PARAM_INT);
        $mform->addHelpButton('fixcost', 'fixcost', 'paygw_robokassa');

        $mform->addElement('text', 'suggest', get_string('suggest', 'paygw_robokassa'), ['size' => 10]);
        $mform->setType('suggest', PARAM_TEXT);
        $mform->disabledIf('suggest', 'fixcost', "neq", 0);

        $mform->addElement('text', 'maxcost', get_string('maxcost', 'paygw_robokassa'), ['size' => 10]);
        $mform->setType('maxcost', PARAM_TEXT);
        $mform->disabledIf('maxcost', 'fixcost', "neq", 0);

        global $CFG;
        $mform->addElement('html', '<div class="label-callback" style="background: #F2EFE6; padding: 15px;">' .
                                    get_string('callback_url', 'paygw_robokassa') . '</span><br>');
        $mform->addElement('html', $CFG->wwwroot . '/payment/gateway/robokassa/callback.php<br>');
        $mform->addElement('html', get_string('return_url', 'paygw_robokassa') . '<br>');
        $mform->addElement('html', $CFG->wwwroot . '/payment/gateway/robokassa/return.php<br>');
        $mform->addElement('html', get_string('callback_help', 'paygw_robokassa') . '</div><br>');

        $header = 'Новые версии плагина вы можете найти на
 <a href=https://github.com/Snickser/moodle-paygw_robokassa>https://github.com/Snickser/moodle-paygw_robokassa</a><br>
Пожалуйста, отправьте мне немного доната<br>
-> https://www.paypal.com/paypalme/snickser :)
';
        $mform->addElement('html', $header);
    }

    /**
     * Validates the gateway configuration form.
     *
     * @param \core_payment\form\account_gateway $form
     * @param \stdClass $data
     * @param array $files
     * @param array $errors form errors (passed by reference)
     */
    public static function validate_gateway_form(
        \core_payment\form\account_gateway $form,
        \stdClass $data,
        array $files,
        array &$errors
    ): void {
        if ($data->enabled && empty($data->merchant_login)) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
        if ($data->maxcost && $data->maxcost < $data->suggest) {
            $errors['maxcost'] = get_string('maxcosterror', 'paygw_robokassa');
        }
    }
}
