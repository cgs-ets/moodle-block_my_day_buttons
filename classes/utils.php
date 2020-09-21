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
 * Plugin utils.
 *
 * @package   block_my_day_buttons
 * @copyright 2019 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_my_day_buttons;

defined('MOODLE_INTERNAL') || die();

/**
 * Provides utility functions for this plugin.
 *
 * @package   block_my_day_buttons
 * @copyright 2019 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

class utils {

    /**
     * iterate through an array until one of the array keys is found in a string and return the corresponding value.
     *
     * @param string $haystack The array to match against a string
     * @param string $string The string that you want to search against
     * @return mixed Returns the value for the key found to be contained in the string, FALSE otherwise.
     */
    public static function array_get_by_key_in_string($haystack, $string) {
        foreach ($haystack as $key => $value) {
            if (stripos($string, $key) !== false) {
                return $value;
            }
        }
        return false;
    }

    /**
     * Get the previous day.
     *
     * @param string $date. Date in Y-m-d.
     * @return string. Date in Y-m-d.
     */
    public static function get_prev_day($date) {
        $dayofweek = date('D', strtotime($date));
        $date = new \DateTime($date);
        if ($dayofweek == 'Mon') {
            $date->modify('-3 day');
        } else {
            $date->modify('-1 day');
        }
        return date('Y-m-d', $date->getTimestamp());
    }

    /**
     * Get the next day.
     *
     * @param string $date. Date in Y-m-d.
     * @return string. Date in Y-m-d.
     */
    public static function get_next_day($date) {
        $dayofweek = date('D', strtotime($date));
        $date = new \DateTime($date);
        if ($dayofweek == 'Fri') {
            $date->modify('+3 day');
        } else {
            $date->modify('+1 day');
        }
        return date('Y-m-d', $date->getTimestamp());
    }


}