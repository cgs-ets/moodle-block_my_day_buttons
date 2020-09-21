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

namespace block_my_day_buttons\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot . '/blocks/my_day_buttons/lib.php');

/**
 * Trait implementing the external function block_my_day_buttons_get_timetable_data_for_date
 */
trait get_timetable_data_for_date {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
    */

    public static  function get_timetable_data_for_date_parameters(){
        return new external_function_parameters(
            array(
                  'timetableuser' => new external_value(PARAM_ALPHANUMEXT, 'Username'),
                  'nav' =>  new external_value(PARAM_INT, 'Nav direction'),
                  'date' =>  new external_value(PARAM_RAW, 'Date'),
                  'instanceid' =>  new external_value(PARAM_INT, 'Instance ID')
            )
        );
    }

    /**
     * Navigate the timetable.
     * @param  string $timetableuser represents a user.
     *         int $date represents the date in timestamp format.
     *         int $nav represents a nav direction, 0: Backward, 1: Forward.
     * @return a timetable for a user.
     */
    public static function get_timetable_data_for_date($timetableuser, $nav, $date, $instanceid) {
        global $USER, $PAGE;

        $context = \context_user::instance($USER->id);
        self::validate_context($context);
        //Parameters validation
        $params = array(
              'timetableuser' => $timetableuser,
              'nav'=> $nav,
              'date'=> $date,
              'instanceid'=> $instanceid,
        );
        self::validate_parameters(self::get_timetable_data_for_date_parameters(), $params);

        // Generate the new timetable.
        list($props, $relateds) = myday_navigate_timetable($timetableuser, $nav, $date, $instanceid);

        $exporter = new \block_my_day_buttons\external\myday_exporter($props, $relateds);
        
        $output = $PAGE->get_renderer('core');
        $data = $exporter->export($output);

        return $data;
    }

    /**
     * Describes the structure of the function return value.
     *
     * @return external_single_structure
     *
     */
    public static function get_timetable_data_for_date_returns(){
        return new external_single_structure(
            array(
                'instanceid' => new external_value(PARAM_INT,'Block instance id'),
                'periodstitle' => new external_value(PARAM_RAW,'Title'),
                'username' => new external_value(PARAM_RAW,'Username'),
                'day' => new external_value(PARAM_RAW,'Current day'),
                'date' => new external_value(PARAM_RAW,'Current date'),
                'fromws' => new external_value(PARAM_BOOL,'From WS'),
                'noactivitytitle' => new external_value(PARAM_RAW,'No avtivity title'),
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT,'Course id'),
                            'fullname' => new external_value(PARAM_RAW,'Course fullname'),
                            'shortname' => new external_value(PARAM_RAW,'Course shortname'),
                            'idnumber' => new external_value(PARAM_RAW,'Course idnumber'),
                            'category' => new external_value(PARAM_INT,'Course category num'),
                            'startdate' => new external_value(PARAM_INT,'Course start date'),
                            'enddate' => new external_value(PARAM_INT,'Course end date'),
                            'visible' => new external_value(PARAM_BOOL,'Course visibility flag'),
                            'viewurl' => new external_value(PARAM_URL,'Course URL'),
                            'courseimageurl' => new external_value(PARAM_RAW,'Course image'),
                            'courseimagetokenised' => new external_value(PARAM_RAW,'Course image'),
                            'coursecategory' => new external_value(PARAM_RAW,'Course category name'),
                        )
                    )
                ),
                'periods' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'period' => new external_value(PARAM_RAW, 'Period number'),
                            'sorttime' => new external_value(PARAM_RAW, 'Start datetime'),
                            'timetabledatetimeto' => new external_value(PARAM_RAW, 'End datetime'),
                            'perioddescription' => new external_value(PARAM_RAW, 'Period description'),
                            'room' => new external_value(PARAM_RAW, 'Period room'),
                            'classdescription' => new external_value(PARAM_RAW, 'Class description'),
                            'classcode' => new external_value(PARAM_RAW, 'Class code'),
                            'staffid' => new external_value(PARAM_RAW, 'Teacher username'),
                            'ix' => new external_value(PARAM_INT, 'Period seq'),
                            'teacherphoto' => new external_value(PARAM_RAW, 'Teacher profile photo'),
                            'starttime' => new external_value(PARAM_RAW, 'Start time'),
                            'endtime' => new external_value(PARAM_RAW, 'End time'),
                            'url' => new external_value(PARAM_RAW, 'Course url'),
                        )
                    )
                ),
                'numcourses' => new external_value(PARAM_INT,'Number of courses'),
                'numperiods' => new external_value(PARAM_INT,'Number of periods'),
                'noperiods' => new external_value(PARAM_BOOL,'No periods flag'),
            )
        );
    }
}