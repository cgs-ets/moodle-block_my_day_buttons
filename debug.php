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
 * Debugger
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Set context.
$context = context_system::instance();

// Set up page parameters.
$PAGE->set_context($context);
$pageurl = new moodle_url('/block/my_day_buttons/debug.php');
$PAGE->set_url($pageurl);
$title = "Debugger";
$PAGE->set_heading($title);
$PAGE->set_title($SITE->fullname . ': ' . $title);

// Ensure user is logged in and has capability to update course.
require_login();
//require_capability('moodle/site:config', $context, $USER->id); 

//echo "<pre>";
//$api = new block_my_day_buttons\external\api;
//$out = $api->get_timetable_html_for_date();
//var_export($out);
//exit;

echo "<pre>";
$OUTPUT = $PAGE->get_renderer('core');
$data = myday_init_timetable(480);
var_export($data);
exit;
