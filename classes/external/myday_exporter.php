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
 * Provides {@link block_my_day_buttons\external\myday_exporter} class.
 *
 * @package   block_my_day_buttons
 * @copyright 2019 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_my_day_buttons\external;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use core\external\exporter;

/**
 * Exporter of the day's periods.
 */
class myday_exporter extends exporter {

    /**
     * Return the list of standard exported properties. The following properties simply pass in and out of the exporter without manipulation.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'instanceid' => [
                'type' => PARAM_INT,
            ],
            'periodstitle' => [
                'type' => PARAM_RAW,
            ],
            'username' => [
                'type' => PARAM_RAW,
            ],
            'day' => [
                'type' => PARAM_RAW,
            ],
            'date' => [
                'type' => PARAM_RAW,
            ],
            'fromws' => [
                'type' => PARAM_BOOL,
            ],
            'noactivitytitle' => [
                'type' => PARAM_RAW,
            ]
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * Data needed to generate "other" properties.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'coursesdata' => 'stdClass[]',
            'timetabledata' => 'stdClass[]',
            'validperiodnames' => 'string',
            'excludeclassnames' => 'string',
        ];
    }

    /**
     * Return the list of additional properties.
     *
     * Calculated values or properties generated on the fly based on standard properties and related data.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'courses' => [
                'type' => course_exporter::read_properties_definition(),
                'multiple' => true,
                'optional' => false,
            ],
            'periods' => [
                'type' => period_exporter::read_properties_definition(),
                'multiple' => true,
                'optional' => false,
            ],
            'numcourses' => [
                'type' => PARAM_INT,
            ],
            'numperiods' => [
                'type' => PARAM_INT,
            ],
            'noperiods' => [
                'type' => PARAM_BOOL,
            ]
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        // Export the courses
        $courses = array();
        foreach ($this->related['coursesdata'] as $i => $coursedata) {
            // Export the period
            $courseexporter = new course_exporter($coursedata);
            $course = $courseexporter->export($output);

            // Add the exported period to the list
            $courses[] = $course;
        }

        // Build a useful and clean array of periods.
        $periods = array();
        foreach ($this->related['timetabledata'] as $ix => $perioddata) {

            if($perioddata->classdescription == null) {
               continue;
            }
            // Only include Sessions & Cocurricular for now.
            $validperiodnames = array_map('trim', explode(',', $this->related['validperiodnames']));
            if (preg_match("/" . implode('|', $validperiodnames) . "/i", $perioddata->perioddescription)) {
                // Exclude things
                $excludeclassnames = array_map('trim', explode(',', $this->related['excludeclassnames']));
                $pattern = implode('|', $excludeclassnames);
                if ($pattern) {
                    if (preg_match("/" . $pattern . "/i", $perioddata->classdescription)) {
                        continue;
                    }
                }

                // If there is a previous period, check for duplicates.
                if ( count($periods) ) {
                    $previous = end($periods);
                    $previousix = $previous->ix;
                    // In primary school students have the same class across multiple sessions.
                    // Check if this class is the same as the last class.
                    if ( $perioddata->classdescription == $this->related['timetabledata'][$previousix]->classdescription ) {
                        // Update the end time of the previous class, re-export it, and skip over this one.
                        // Note, do not re-export exported data as overwriting other properties is not allowed.
                        $this->related['timetabledata'][$previousix]->timetabledatetimeto = $perioddata->timetabledatetimeto;
                        $periodexporter = new period_exporter($this->related['timetabledata'][$previousix]); #$relateds
                        $i = count($periods) - 1;
                        $periods[$i] = $periodexporter->export($output);
                        continue;
                    }
                }

                // Export the period
                $periodexporter = new period_exporter($perioddata);
                $period = $periodexporter->export($output);

                // Add the exported period to the list
                $periods[] = $period;
            }
        }

        return [
            'courses' => $courses,
            'periods' => $periods,
            'numcourses' => count($courses),
            'numperiods' => count($periods),
            'noperiods' =>  (count($periods) == 0) ? true : false,
        ];
    }


}