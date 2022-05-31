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
 * Strings for block_my_day_buttons
 *
 * @package   block_my_day_buttons
 * @copyright 2019 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
$string['blockname'] = 'My Day (Buttons)';
$string['title'] = 'My Day (Buttons)';
$string['my_day_buttons:addinstance'] = 'Add a "My Day (Buttons)" block';
$string['my_day_buttons:edit'] = 'Edit a "My Day (Buttons)" block';
$string['my_day_buttons:myaddinstance'] = 'Add a "My Day (Buttons)" block to the Dashboard';
$string['pluginname'] = 'My Day (Buttons)';
$string['privacy:metadata'] = 'The "My Day (Buttons)" block does not store any personal data.';
$string['nodbsettings'] = 'You need to configure the DB settings for the "My Day (Buttons)" plugin.';
$string['userprofilenotsetup'] = 'The "My Day (Buttons)" block requires custom user profile field called "CampusRoles" to be configured on your Moodle instance.';
$string['pluginname_desc'] = 'This plugin depends on Synergetic for timetable data.';
$string['settingsheaderdb'] = 'External database connection';
$string['dbtype'] = 'Database driver';
$string['dbtype_desc'] = 'ADOdb database driver name, type of the external database engine.';
$string['dbhost'] = 'Database host';
$string['dbhost_desc'] = 'Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.';
$string['dbname'] = 'Database name';
$string['dbuser'] = 'Database user';
$string['dbpass'] = 'Database password';
$string['dbstudentproc'] = 'Student timetable stored procedure';
$string['dbstudentproc_desc'] = 'Stored procedure name to retrieve student timetable data. This plugin expects that the stored procedure accepts a single parameter for the student id. The return data needs to be in a specific format too.';
$string['staffroles'] = 'Staff CampusRoles (csv)';
$string['staffroles_desc'] = 'Used to determine who is a "Staff" user based on the "CampusRoles" custom profile field.';
$string['studentroles'] = 'Student CampusRoles (csv)';
$string['studentroles_desc'] = 'Used to determine who is a "Student" user based on the "CampusRoles" custom profile field.';
$string['periodnames'] = 'Valid period names (csv)';
$string['excludeclasses'] = 'Classes to exclude (csv)';
$string['periodsectiontitle'] = 'Period section title';
$string['periodsectiontitle_default'] = 'What\'s On';
$string['coursecategories'] = 'Course Categories';
$string['coursecategories_desc'] = 'ID numbers of categories to look at for course buttons (csv).';
$string['excludecourses'] = 'Courses in Moodle to exclude (csv)';
$string['endofday'] = 'End of day';
$string['endofday_desc'] = '(E.g. 1530)';
$string['noactivitytitle'] = 'No specialist activity title';
$string['noactivitytitle_default'] = 'No special activities on this day';
$string['timetableunavailable'] = 'Timetable data unavailable.';