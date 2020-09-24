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
 * Provides {@link block_my_day_buttons\external\course_exporter} class.
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

/**
 * Class for exporting a course from an stdClass.
 *
 * @copyright  2019 Michael Vangelovski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_exporter extends exporter {

    /**
     * Return the list of standard exported properties.
     *
     * These are properties you would read directly from a table row,
     * or data you would save to a table to read from later.
     * These properties are required in order to export the item.
     *
     * @return array
     */
    public static function define_properties() {
        return array(
            'id' => array(
                'type' => PARAM_INT,
            ),
            'fullname' => array(
                'type' => PARAM_RAW,
            ),
            'shortname' => array(
                'type' => PARAM_RAW,
            ),
            'idnumber' => array(
                'type' => PARAM_RAW,
            ),
            'category' => array(
                'type' => PARAM_INT,
            ),
            'startdate' => array(
                'type' => PARAM_INT,
            ),
            'enddate' => array(
                'type' => PARAM_INT,
            ),
            'visible' => array(
                'type' => PARAM_BOOL,
            ),
        );
    }

    public static function define_other_properties() {
        return array(
            'viewurl' => array(
                'type' => PARAM_URL,
            ),
            'courseimageurl' => array(
                'type' => PARAM_RAW,
            ),
            'courseimagetokenised' => array(
                'type' => PARAM_RAW,
            ),
            'coursecategory' => array(
                'type' => PARAM_RAW
            ),
        );
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        list($courseimageurl, $courseimagetokenised) = self::get_course_image($this->data);
        if (!$courseimageurl) {
            $courseimageurl = $courseimagetokenised = $output->get_generated_image_for_id($this->data->id);
        }

        $coursecategory = \core_course_category::get($this->data->category, MUST_EXIST, true);
        return array(
            'viewurl' => (new moodle_url('/course/view.php', array('id' => $this->data->id)))->out(false),
            'courseimageurl' => $courseimageurl,
            'courseimagetokenised' => $courseimagetokenised,
            'coursecategory' => $coursecategory->name
        );
    }

    /**
     * Get the course image if added to course.
     *
     * @param object $course
     * @return string url of course image
     */
    public static function get_course_image($course) {
        $courseinlist = new \core_course_list_element($course);
        foreach ($courseinlist->get_course_overviewfiles() as $file) {
            if ($file->is_valid_image()) {
                $pathcomponents = [
                    '/pluginfile.php',
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename()
                ];
                $path = implode('/', $pathcomponents);
                $imageurl = (new moodle_url($path))->out();

                $imagetokenised = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false, true);
                /*$imagetokenised = file_rewrite_pluginfile_urls(
                    '@@PLUGINFILE@@/' . $file->get_filename(),
                    'pluginfile.php', 
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(), 
                    null,
                    array('includetoken' => true)
                );*/
                return array($imageurl, $imagetokenised);
            }
        }
        return array(false, false);
    }
}