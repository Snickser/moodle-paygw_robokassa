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
 * URL module upgrade code
 *
 * This file keeps track of upgrades to
 * the resource module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * File         upgrade.php
 * Encoding     UTF-8
 *
 * @package     paygw_robokassa
 * @copyright   2024 Alex Orlov <snickser@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade script for mod_gwpayments
 *
 * @param int $oldversion
 * @return boolean
 */
function xmldb_paygw_robokassa_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024050303) {
        // Define field to be renamed.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('group_names', XMLDB_TYPE_TEXT, null, null, null, null, null, 'courseid');
        // Launch rename field.
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'groupnames');
        }

        // Define field to be dropped from paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('userid');
        // Conditionally launch drop field groupnames.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field to be dropped from paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('itemid');
        // Conditionally launch drop field groupnames.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field to be dropped from paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('component');
        // Conditionally launch drop field groupnames.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field to be dropped from paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('paymentarea');
        // Conditionally launch drop field groupnames.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field to be dropped from paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('currency');
        // Conditionally launch drop field groupnames.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field to be dropped from paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('date_created');
        // Conditionally launch drop field groupnames.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field to be dropped from paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('cost');
        // Conditionally launch drop field groupnames.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('paymentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id');
        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define key paymentid (foreign-unique) to be added to paygw_robokassa.
        $table = new xmldb_table('paygw_robokassa');
        $key = new xmldb_key('paymentid', XMLDB_KEY_FOREIGN_UNIQUE, ['paymentid'], 'payments', ['id']);
        // Launch add key paymentid.
        $dbman->add_key($table, $key);

        $table = new xmldb_table('paygw_robokassa');
        $field = new xmldb_field('invoiceid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'paymentid');
        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Robokassa savepoint reached.
        upgrade_plugin_savepoint(true, 2024050303, 'paygw', 'robokassa');
    }

    return true;
}
