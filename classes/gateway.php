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
 * Contains class for monobank payment gateway.
 *
 * @package    paygw_monobank
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_monobank;

/**
 * The gateway class for monobank payment gateway.
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
            'UAH', 'USD', 'EUR',
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

        $mform->addElement('text', 'api_token', get_string('api_token', 'paygw_monobank'), ['size' => 60]);
        $mform->setType('api_token', PARAM_TEXT);
        $mform->addRule('api_token', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('api_token', 'api_token', 'paygw_monobank');

        $mform->addElement(
            'advcheckbox',
            'istestmode',
            get_string('istestmode', 'paygw_monobank')
        );
        $mform->setType('istestmode', PARAM_INT);
        $mform->addHelpButton('istestmode', 'istestmode', 'paygw_monobank');

        $mform->addElement(
            'advcheckbox',
            'sendlinkmsg',
            get_string('sendlinkmsg', 'paygw_monobank')
        );
        $mform->setType('sendlinkmsg', PARAM_INT);
        $mform->addHelpButton('sendlinkmsg', 'sendlinkmsg', 'paygw_monobank');
        $mform->setDefault('sendlinkmsg', 1);

        $mform->addElement('text', 'fixdesc', get_string('fixdesc', 'paygw_monobank'), ['size' => 50]);
        $mform->setType('fixdesc', PARAM_TEXT);
        $mform->addRule('fixdesc', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('fixdesc', 'fixdesc', 'paygw_monobank');

        $mform->addElement('static');

        $mform->addElement(
            'advcheckbox',
            'skipmode',
            get_string('skipmode', 'paygw_monobank')
        );
        $mform->setType('skipmode', PARAM_INT);
        $mform->addHelpButton('skipmode', 'skipmode', 'paygw_monobank');

        $mform->addElement(
            'advcheckbox',
            'passwordmode',
            get_string('passwordmode', 'paygw_monobank')
        );
        $mform->setType('passwordmode', PARAM_INT);
        $mform->DisabledIf('passwordmode', 'skipmode', "neq", 0);

        $mform->addElement('passwordunmask', 'password', get_string('password', 'paygw_monobank'), ['size' => 20]);
        $mform->setType('password', PARAM_TEXT);
        $mform->addHelpButton('password', 'password', 'paygw_monobank');

        $mform->addElement(
            'advcheckbox',
            'usedetails',
            get_string('usedetails', 'paygw_monobank')
        );
        $mform->setType('usedetails', PARAM_INT);
        $mform->addHelpButton('usedetails', 'usedetails', 'paygw_monobank');

        $mform->addElement(
            'advcheckbox',
            'showduration',
            get_string('showduration', 'paygw_monobank')
        );
        $mform->setType('showduration', PARAM_INT);

        $mform->addElement(
            'advcheckbox',
            'fixcost',
            get_string('fixcost', 'paygw_monobank')
        );
        $mform->setType('fixcost', PARAM_INT);
        $mform->addHelpButton('fixcost', 'fixcost', 'paygw_monobank');

        $mform->addElement('text', 'suggest', get_string('suggest', 'paygw_monobank'), ['size' => 10]);
        $mform->setType('suggest', PARAM_TEXT);
        $mform->disabledIf('suggest', 'fixcost', "neq", 0);

        $mform->addElement('text', 'maxcost', get_string('maxcost', 'paygw_monobank'), ['size' => 10]);
        $mform->setType('maxcost', PARAM_TEXT);
        $mform->disabledIf('maxcost', 'fixcost', "neq", 0);

        global $CFG;
        $mform->addElement('html', '<div class="label-callback" style="background: #d4edda; padding: 15px;">' .
                                    get_string('callback_url', 'paygw_monobank') . '<br>');
        $mform->addElement('html', $CFG->wwwroot . '/payment/gateway/monobank/callback.php<br>');
        $mform->addElement('html', get_string('callback_help', 'paygw_monobank') . '</div><br>');

        $plugininfo = \core_plugin_manager::instance()->get_plugin_info('paygw_monobank');
        $donate = get_string('donate', 'paygw_monobank', $plugininfo);
        $mform->addElement('html', $donate);
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
        if ($data->enabled && empty($data->api_token)) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
        if ($data->maxcost && $data->suggest && $data->maxcost < $data->suggest) {
            $errors['maxcost'] = get_string('maxcosterror', 'paygw_monobank');
        }
    }
}
