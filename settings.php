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
     * Defines the global settings of the block
     *
     * @package   block_my_day_buttons
     * @copyright 2019 Michael Vangelovski
     * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    defined('MOODLE_INTERNAL') || die();


    if ($ADMIN->fulltree) {

        $settings->add(new admin_setting_heading(
                'block_my_day_buttons_settings',
                '',
                get_string('pluginname_desc', 'block_my_day_buttons')
        ));

        $settings->add(new admin_setting_heading(
                'block_my_day_buttons_exdbheader',
                get_string('settingsheaderdb', 'block_my_day_buttons'),
                ''
        ));

        $options = array('', "mysqli", "oci", "pdo", "pgsql", "sqlite3", "sqlsrv");
        $options = array_combine($options, $options);
        $settings->add(new admin_setting_configselect(
                'block_my_day_buttons/dbtype',
                get_string('dbtype', 'block_my_day_buttons'),
                get_string('dbtype_desc', 'block_my_day_buttons'),
                '',
                $options
        ));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/dbhost', get_string('dbhost', 'block_my_day_buttons'), get_string('dbhost_desc', 'block_my_day_buttons'), 'localhost'));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/dbuser', get_string('dbuser', 'block_my_day_buttons'), '', ''));

        $settings->add(new admin_setting_configpasswordunmask('block_my_day_buttons/dbpass', get_string('dbpass', 'block_my_day_buttons'), '', ''));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/dbname', get_string('dbname', 'block_my_day_buttons'), '', ''));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/dbstudentproc', get_string('dbstudentproc', 'block_my_day_buttons'), get_string('dbstudentproc_desc', 'block_my_day_buttons'), ''));

        // The user's CampusRoles are how this plugin determines which timetable to fetch and show.
        $settings->add(new admin_setting_configtext('block_my_day_buttons/studentroles', get_string('studentroles', 'block_my_day_buttons'), get_string('studentroles_desc', 'block_my_day_buttons'), ''));
        $settings->add(new admin_setting_configtext('block_my_day_buttons/staffroles', get_string('staffroles', 'block_my_day_buttons'), get_string('staffroles_desc', 'block_my_day_buttons'), ''));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/coursecategories', get_string('coursecategories', 'block_my_day_buttons'), get_string('coursecategories_desc', 'block_my_day_buttons'), ''));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/periodsectiontitle', get_string('periodsectiontitle', 'block_my_day_buttons'), '', get_string('periodsectiontitle_default', 'block_my_day_buttons')));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/noactivitytitle', get_string('noactivitytitle', 'block_my_day_buttons'), '', get_string('noactivitytitle_default', 'block_my_day_buttons')));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/periodnames', get_string('periodnames', 'block_my_day_buttons'), '', ''));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/excludeclasses', get_string('excludeclasses', 'block_my_day_buttons'), '', ''));

        $settings->add(new admin_setting_configtext('block_my_day_buttons/endofday', get_string('endofday', 'block_my_day_buttons'), get_string('endofday_desc', 'block_my_day_buttons'), '1530'));
    }
