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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_robokassa;

/**
 * The gateway class for robokassa payment gateway.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_payment\gateway {
    public static function get_supported_currencies(): array {
        // 3-character ISO-4217: https://en.wikipedia.org/wiki/ISO_4217#Active_codes.
        return [
            'RUB', 'USD', 'EUR', 'KZT'
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

        $mform->addElement('text', 'password1', get_string('password1', 'paygw_robokassa'));
        $mform->setType('password1', PARAM_TEXT);
        $mform->disabledIf('password1', 'istestmode', "neq", 0);

        $mform->addElement('text', 'password2', get_string('password2', 'paygw_robokassa'));
        $mform->setType('password2', PARAM_TEXT);
        $mform->disabledIf('password2', 'istestmode', "neq", 0);

        $mform->addElement('advcheckbox', 'istestmode', get_string('istestmode', 'paygw_robokassa'), '0');
        $mform->setType('istestmode', PARAM_TEXT);

        $mform->addElement('text', 'test_password1', get_string('password1', 'paygw_robokassa'));
        $mform->setType('test_password1', PARAM_TEXT);
        $mform->disabledIf('test_password1', 'istestmode');

        $mform->addElement('text', 'test_password2', get_string('password2', 'paygw_robokassa'));
        $mform->setType('test_password2', PARAM_TEXT);
        $mform->disabledIf('test_password2', 'istestmode');

        $mform->addElement('advcheckbox', 'passwordmode', get_string('passwordmode', 'paygw_robokassa'), '0');
        $mform->setType('passwordmode', PARAM_TEXT);

        $mform->addElement('text', 'password', get_string('password', 'paygw_robokassa'));
        $mform->setType('password', PARAM_TEXT);
        $mform->disabledIf('password', 'passwordmode');
        $mform->addHelpButton('password', 'password', 'paygw_robokassa');

        $mform->addElement('float', 'suggest', get_string('suggest', 'paygw_robokassa'));
        $mform->setType('suggest', PARAM_FLOAT);

        global $CFG;
        $mform->addElement('html', '<span class="label-callback">'.get_string('callback_url', 'paygw_robokassa').'</span><br>');
        $mform->addElement('html', '<span class="callback_url">'.$CFG->wwwroot.'/payment/gateway/robokassa/callback.php</span><br>');
        $mform->addElement('html', '<span class="label-return">'.get_string('return_url', 'paygw_robokassa').'</span><br>');
        $mform->addElement('html', '<span class="return_url">'.$CFG->wwwroot.'/payment/gateway/robokassa/return.php</span><br>');
        $mform->addElement('html', '<span class="label-callback">'.get_string('callback_help', 'paygw_robokassa').'</span><br><br>');

    }

    /**
     * Validates the gateway configuration form.
     *
     * @param \core_payment\form\account_gateway $form
     * @param \stdClass $data
     * @param array $files
     * @param array $errors form errors (passed by reference)
     */
    public static function validate_gateway_form(\core_payment\form\account_gateway $form,
                                                 \stdClass $data, array $files, array &$errors): void {
        if ($data->enabled &&
                (empty($data->merchant_login) || empty($data->password1) || empty($data->password2))) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
    }
}
