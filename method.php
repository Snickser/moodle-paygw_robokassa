<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     paygw_robokassa
 * @category    admin
 * @copyright   2023 Mohammad Farouk <phun.for.physics@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_payment\helper;

require_once(__DIR__ . '/../../../config.php');
global $CFG, $USER, $DB;

defined('MOODLE_INTERNAL') || die();

require_login();

$component   = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid      = required_param('itemid', PARAM_INT);
$description = required_param('description', PARAM_TEXT);

$description = json_decode("\"$description\"");

$params = [
    'component'   => $component,
    'paymentarea' => $paymentarea,
    'itemid'      => $itemid,
    'description' => $description,
];

$config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'robokassa');
$payable = helper::get_payable($component, $paymentarea, $itemid);// Get currency and payment amount.
$currency = $payable->get_currency();
$surcharge = helper::get_gateway_surcharge('robokassa');// In case user uses surcharge.
$fee = helper::get_rounded_cost($payable->get_amount(), $currency, $surcharge);

// get course info
$enrolperiod='';
$enrolperiod_desc='';
if($instance = $DB->get_record('enrol', ['id' => $itemid, 'enrol' => $paymentarea])){
    $enrolperiod = $instance->enrolperiod;
    if( $enrolperiod > 0 ){
        if($enrolperiod>=86400){
	    $enrolperiod_desc = get_string('days');
	    $enrolperiod = round($enrolperiod/86400);
	} else if($enrolperiod>=3600) {
	    $enrolperiod_desc = get_string('hours');
	    $enrolperiod = round($enrolperiod/3600);
	} else if($enrolperiod>=60) {
	    $enrolperiod_desc = get_string('minutes');
	    $enrolperiod = round($enrolperiod/60);
	} else {
	    $enrolperiod_desc = get_string('seconds');
	}
    }
}

// Set the context of the page.
$PAGE->set_context(context_system::instance());

$PAGE->set_url('/payment/gateway/robokassa/method.php', $params);
$string = get_string('payment','paygw_robokassa');
$PAGE->set_title(format_string( $string ));
$PAGE->set_heading(format_string( $string ));

// Set the appropriate headers for the page.
$PAGE->set_cacheable(false);
//$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

$templatedata = new stdClass;
$templatedata->component   = $component;
$templatedata->paymentarea = $paymentarea;
$templatedata->itemid      = $itemid;
$templatedata->description = $description;
$templatedata->fee         = $fee;
$templatedata->currency    = $currency;
$templatedata->enrolperiod = $enrolperiod;
$templatedata->enrolperiod_desc = $enrolperiod_desc;
$templatedata->passwordmode = $config->passwordmode;
$templatedata->suggest = $config->suggest;
$templatedata->maxcost = $config->maxcost;
$templatedata->skipmode = $config->skipmode;


$templatedata->image       = $OUTPUT->image_url('img','paygw_robokassa');

echo $OUTPUT->render_from_template('paygw_robokassa/method', $templatedata);

echo $OUTPUT->footer();
