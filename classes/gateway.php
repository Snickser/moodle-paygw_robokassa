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
            'RUB', 'USD', 'EUR'
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

        $options = array('www.robokassa.ru'  => 'www.robokassa.ru',
                         'demo.moneta.ru' => 'demo.moneta.ru');
        $mform->addElement('select', 'paymentserver', get_string('paymentserver', 'paygw_robokassa'), $options);
        $mform->setType('paymentserver', PARAM_TEXT);

	$paymentsystems = array(
			'robokassa_0_0' => get_string('robokassa', 'paygw_robokassa'),
//			'moneta_0_1015' => get_string('moneta', 'paygw_robokassa'),
			'plastic_0_card' => get_string('plastic', 'paygw_robokassa'),
			'sbp_0_12299232' => get_string('sbp', 'paygw_robokassa'),
//			'webmoney_0_1017' => get_string('webmoney', 'paygw_robokassa'),
//			'yandex_0_1020' => get_string('yandex', 'paygw_robokassa'),
//			'moneymail_0_1038' => get_string('moneymail', 'paygw_robokassa'),
//			'wallet_0_310212' => get_string('wallet', 'paygw_robokassa'),
//			'banktransfer_1_705000_75983431' => get_string('banktransfer', 'paygw_robokassa'),
//			'ciberpay_1_489755_19357960' => get_string('ciberpay', 'paygw_robokassa'),
//			'comepay_1_228820_47654606' => get_string('comepay', 'paygw_robokassa'),
//			'contact_1_1028_26' => get_string('contact', 'paygw_robokassa'),
//			'elecsnet_1_232821_10496472' => get_string('elecsnet', 'paygw_robokassa'),
//			'euroset_1_248362_136' => get_string('euroset', 'paygw_robokassa'),
//			'forward_1_83046_116' => get_string('forward', 'paygw_robokassa'),
//			'gorod_1_426904_152' => get_string('gorod', 'paygw_robokassa'),
//			'mcb_1_295339_143' => get_string('mcb', 'paygw_robokassa'),
//			'novoplat_1_281129_80314912' => get_string('novoplat', 'paygw_robokassa'),
//			'platika_1_226272_15662295' => get_string('platika', 'paygw_robokassa'),
//			'post_1_1029_15' => get_string('post', 'paygw_robokassa'),
		);
        $mform->addElement('select', 'paymentsystem', get_string('paymentsystem', 'paygw_robokassa'), $paymentsystems);
        $mform->setDefault('paymentsystem', get_string('paymentsystem', 'paygw_robokassa'));

        $mform->addElement('text', 'mntid', get_string('mntid', 'paygw_robokassa'));
        $mform->setType('mntid', PARAM_TEXT);

        $mform->addElement('text', 'mntdataintegritycode', get_string('mntdataintegritycode', 'paygw_robokassa'));
        $mform->setType('mntdataintegritycode', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'mnttestmode', get_string('mnttestmode', 'paygw_robokassa'), '0');
        $mform->setType('mnttestmode', PARAM_TEXT);

        global $CFG;
        $mform->addElement('html', '<span class="label-callback">'.get_string('callback', 'paygw_robokassa').':</span><br>');
        $mform->addElement('html', '<span class="callback_url">'.$CFG->wwwroot.'/payment/gateway/robokassa/callback.php</span><br>');
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
                (empty($data->mntid) || empty($data->mntdataintegritycode) || empty($data->paymentserver))) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
    }
}
