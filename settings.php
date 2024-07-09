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
 * Settings for the Robokassa payment gateway
 *
 * @package    paygw_robokassa
 * @copyright  2024 Alex Orlov <snickser@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $header = 'Новые версии плагина вы можете найти на
 <a href=https://github.com/Snickser/moodle-paygw_robokassa>GitHub.com</a>';

    $settings->add(new admin_setting_heading(
        'paygw_robokassa_settings',
        $header,
        get_string('pluginname_desc', 'paygw_robokassa')
    ));
    \core_payment\helper::add_common_gateway_settings($settings, 'paygw_robokassa');
}
