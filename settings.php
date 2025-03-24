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
    $plugininfo = \core_plugin_manager::instance()->get_plugin_info('paygw_robokassa');
    $donate = get_string('donate', 'paygw_robokassa', $plugininfo);

    $settings->add(new admin_setting_heading(
        'paygw_robokassa_settings',
        get_string('pluginname_desc', 'paygw_robokassa'),
        $donate,
    ));
    \core_payment\helper::add_common_gateway_settings($settings, 'paygw_robokassa');
}
