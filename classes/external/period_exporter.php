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
 * Provides {@link block_my_day_buttons\external\period_exporter} class.
 *
 * @package   block_my_day_buttons
 * @copyright 2019 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_my_day_buttons\external;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use core\external\exporter;
use moodle_url;
use block_my_day_buttons\utils;

/**
 * Exporter of a single period
 */
class period_exporter extends exporter {

    /**
     * Return the list of standard exported properties.
     *
     * These are properties you would read directly from a table row,
     * or data you would save to a table to read from later.
     * These properties are required in order to export the item.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'period' => [
                'type' => PARAM_INT,
            ],
            'sorttime' => [
                'type' => PARAM_RAW,
            ],
            'timetabledatetimeto' => [
                'type' => PARAM_RAW,
            ],
            'perioddescription' => [
                'type' => PARAM_ALPHANUMEXT,
            ],
            'room' => [
                'type' => PARAM_ALPHANUMEXT,
            ],
            'classdescription' => [
                'type' => PARAM_ALPHANUMEXT,
            ],
            'classcode' => [
                'type' => PARAM_ALPHANUMEXT,
            ],
            'staffid' => [
                'type' => PARAM_ALPHANUMEXT,
            ],
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
            'ix' => [
                'type' => PARAM_INT,
            ],
            'teacherphoto' => [
                'type' => PARAM_RAW,
            ],
            'starttime' => [
                'type' => PARAM_RAW,
            ],
            'endtime' => [
                'type' => PARAM_RAW,
            ],
            'url' => [
                'type' => PARAM_RAW,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        global $DB;

        $otherdata = array();

        $otherdata['ix'] = $this->data->timetabletmpseq;

        // Add teacher photo to class
        $otherdata['teacherphoto'] = '';
        if ( $this->data->staffid ) {
            $teacher = $DB->get_record('user', array('username'=>$this->data->staffid));
            $photourl =  new moodle_url('/user/pix.php/'.$teacher->id.'/f2.jpg');
            $otherdata['teacherphoto'] = $photourl->out();
        }

        // Find and add course link based on class code
        $otherdata['url'] = '';

        // Add formatted times
        $otherdata['starttime'] = date('G:ia',strtotime($this->data->sorttime));
        $otherdata['endtime'] = date('G:ia',strtotime($this->data->timetabledatetimeto));

        return $otherdata;
    }

}