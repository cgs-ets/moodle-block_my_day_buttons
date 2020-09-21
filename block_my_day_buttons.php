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
 * This block generates a daily timetable based on external user class data.
 *
 * @package   block_my_day_buttons
 * @copyright 2019 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/blocks/my_day_buttons/lib.php');
use block_my_day_buttons\utils;

class block_my_day_buttons extends block_base {

    /**
     * Core function used to initialize the block.
     */
    public function init() {
        $this->title = get_string('title', 'block_my_day_buttons');
    }

    /**
    * We have global config/settings data.
    * @return bool
    */
    public function has_config() {
        return true;
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Controls whether the block is configurable
     *
     * @return bool
     */
    public function instance_allow_config() {
        return false;
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'user' => true,
            'site' => true,
            'my' => true,
        );
    }

    /**
     * Used to generate the content for the block.
     * @return object
     */
    public function get_content() {
        global $COURSE, $DB, $USER, $PAGE, $OUTPUT;

        // If content has already been generated, don't waste time generating it again.
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $context = CONTEXT_COURSE::instance($COURSE->id);
        $config = get_config('block_my_day_buttons');

        // Check DB settings are available
        if( empty($config->dbtype) ||
                empty($config->dbhost) ||
                empty($config->dbuser) ||
                empty($config->dbpass) ||
                empty($config->dbname) ||
                empty($config->dbstudentproc)  ) {
            $notification = new \core\output\notification(get_string('nodbsettings', 'block_my_day_buttons'),
                                                          \core\output\notification::NOTIFY_ERROR);
            $notification->set_show_closebutton(false);
            $this->content->text = $OUTPUT->render($notification);
            return $this->content;
        }

        // CampusRoles profile field is required by this plugin.
        profile_load_custom_fields($USER);
        if(!isset($USER->profile['CampusRoles'])) {
            $notification = new \core\output\notification(get_string('userprofilenotsetup', 'block_my_day_buttons'),
                                                          \core\output\notification::NOTIFY_ERROR);
            $notification->set_show_closebutton(false);
            $this->content->text = $OUTPUT->render($notification);
            return $this->content;
        }

        try {
            $data = myday_init_timetable($this->instance->id);
            if ($data) {
                $this->content->text = $OUTPUT->render_from_template('block_my_day_buttons/content', $data);
            }
        } catch (Exception $e) {
            $this->content->text = '<h5>' . get_string('timetableunavailable', 'block_my_day_buttons') . '</h5>';
        }

        return $this->content;
    }

    /**
     * Gets Javascript required for the widget functionality.
     */
    public function get_required_javascript() {
        global $USER;
        parent::get_required_javascript();
        $this->page->requires->js_call_amd('block_my_day_buttons/control', 'init', [
            'instanceid' => $this->instance->id,
            'date' => date('Y-m-d', time()),
            'username' => $USER->username,
        ]);
    }
}
