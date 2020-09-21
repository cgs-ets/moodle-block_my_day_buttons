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
 *  External Web Service Template
 *
 * @package   my_day_buttons
 * @category
 * @copyright 2020 Veronica Bermegui, Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/** Include required files */
require_once($CFG->libdir.'/filelib.php');

use block_my_day_buttons\utils;

/**
 * Initial timetable
 */
function myday_init_timetable($instanceid) {
    global $COURSE, $DB, $USER, $PAGE, $OUTPUT;

    $data = null;

    $context = CONTEXT_COURSE::instance($COURSE->id);
    $config = get_config('block_my_day_buttons');

    // Initialise some defaults.
    $timetableuser = $USER->username;
    $timetablerole = 'student';
    profile_load_custom_fields($USER);
    $userroles = array_map('trim', explode(',', $USER->profile['CampusRoles']));

    // Load in some config.
    $studentroles = array_map('trim', explode(',', $config->studentroles));
    $staffroles = array_map('trim', explode(',', $config->staffroles));

    // Determine if user is viewing this block on a profile page.
    if ( $PAGE->url->get_path() == '/user/profile.php' ) {
        // Get the profile user.
        $profileuser = $DB->get_record('user', ['id' => $PAGE->url->get_param('id')]);
        $timetableuser = $profileuser->username;
        // Load the user's custom profile fields.
        profile_load_custom_fields($profileuser);
        $profileroles = explode(',', $profileuser->profile['CampusRoles']);
        // Check whether the timetable should be displayed for this profile user.
        // E.g. Primary student.
        if (array_intersect($profileroles, $studentroles)) {
            $timetablerole = 'student';
        } else {
            return null;
        }

        // Determine who is allowed to view this timetable.
        $allowed = false;

        // Staff are always allowed to view timetables in profiles.
        if (array_intersect($userroles, $staffroles)) {
            $allowed = true;
        }

        // Students are allowed to see timetables in their own profiles.
        if ($profileuser->username == $USER->username) {
            $allowed = true;
        }

        // Parents are allowed to view timetables in their mentee profiles.
        $mentorrole = $DB->get_record('role', array('shortname' => 'parent'));
        $sql = "SELECT ra.*, r.name, r.shortname
                FROM {role_assignments} ra
                INNER JOIN {role} r ON ra.roleid = r.id
                INNER JOIN {user} u ON ra.userid = u.id
                WHERE ra.userid = ?
                AND ra.roleid = ?
                AND ra.contextid IN (SELECT c.id
                    FROM {context} c
                    WHERE c.contextlevel = ?
                    AND c.instanceid = ?)";
        $params = array(
            $USER->id, //Where current user
            $mentorrole->id, // is a mentor
            CONTEXT_USER,
            $profileuser->id, // of the prfile user
        );
        $mentor = $DB->get_records_sql($sql, $params);

        if ( !empty($mentor) ) {
            $allowed = true;
        }

        if ( !$allowed ) {
            return null;
        }

    } else {
        // Check whether the timetable should be displayed for this user.
        if (array_intersect($userroles, $studentroles)) {
            $timetablerole = 'student';
        } else {
            return null;
        }
    }

    //Get the day depending on the time. End of day, End of week or current day.
    $date = date('Y-m-d', time());
    // Check if it is the end of the day.
    $endofday = new DateTime($config->endofday);
    $current_time = new DateTime('now');
    if ($current_time > $endofday) {
        $date = utils::get_next_day($date);
    }

    // Generate the new timetable.
    $nav = -1;
    list($props, $relateds) = myday_navigate_timetable($timetableuser, $nav, $date, $instanceid);

    if (!empty($props)) {
        $timetable = new block_my_day_buttons\external\myday_exporter($props, $relateds);
        $data = $timetable->export($OUTPUT);
    }

    return $data;
}

/**
 * Navigate timetable.
 *
 */
function myday_navigate_timetable($timetableuser, $nav, $date, $instanceid) {
    global $DB;

    switch($nav) {
       case 0: //backwards
            $date = utils::get_prev_day($date);
            break;
       case 1: //Forward
            $date = utils::get_next_day($date);
            break;
    }

    try {
        $timetableuser = $DB->get_record('user', ['username' => $timetableuser]);

        //Get  config of this block.
        $config = get_config('block_my_day_buttons');

        // Get our prefered database driver.
        // Last parameter (external = true) means we are not connecting to a Moodle database.
        $externalDB = moodle_database::get_driver_instance($config->dbtype, 'native', true);
        // Connect to external DB
        $externalDB->connect($config->dbhost, $config->dbuser, $config->dbpass, $config->dbname, '');

        $sql = 'EXEC ' . $config->dbstudentproc . ' :id , :date';

        $params = array(
            'id' => $timetableuser->username,
            'date' => $date,
        );

        $timetabledata = $externalDB->get_records_sql($sql, $params);

        // If data is empty and attempting to navigate cal, look for next available timetable day.
        if ($nav == 0 || $nav == 1) {
            $days = 0;
            while(empty($timetabledata) && $days <= 30) { // Look ahead a max of 30 days.
                $days++;
                if ($nav == 1) {
                    $date = utils::get_next_day($date);
                } else {
                    $date = utils::get_prev_day($date);
                }
                $params = array(
                    'id' => $timetableuser->username,
                    'date' => $date,
                );
                $timetabledata = $externalDB->get_records_sql($sql, $params);
            }
        }

        if (empty($timetabledata)) {
            return;
        }

        // Get the moodle courses.
        $coursefields = array('id', 'idnumber', 'startdate', 'enddate', 'category','shortname', 'fullname', 'visible');
        $coursesdata = enrol_get_users_courses($timetableuser->id, true, $coursefields);

        if ($config->coursecategories) {
            $allowedcategories = explode(',', $config->coursecategories);
            list($insql, $inparams) = $DB->get_in_or_equal($allowedcategories);
            $sql = "SELECT id FROM {course_categories} WHERE idnumber $insql";
            $catids = array_keys($DB->get_records_sql($sql, $inparams));
            $coursesdata = array_filter($coursesdata, function($course) use ($catids) {
                return in_array($course->category, $catids);
            });
        }

        $props = (object) [
            'instanceid' => $instanceid,
            'periodstitle' => $config->periodsectiontitle,
            'username' => $timetableuser->username,
            'day' => date('l, j F Y', strtotime($date)), //Show Day, Number Month Year.
            'date' => $date,
            'fromws' => ($nav == -1) ? false : true, // To remove the loading class when the tt is render.
            'noactivitytitle' => $config->noactivitytitle,

        ];
        $relateds = [
            'coursesdata' => $coursesdata,
            'timetabledata' => $timetabledata,
            'validperiodnames' => $config->periodnames,
            'excludeclassnames' => $config->excludeclasses,
        ];

        $timetabledata = array($props, $relateds);
    } catch (Exception $ex) {
        //echo $ex->getMessage(); exit;
        throw new Exception($ex->getMessage());
    }
    return $timetabledata;
}

