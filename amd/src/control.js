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
 * Provides the block_my_day_buttons/control module
 *
 * @package   block_my_day_buttons
 * @category  output
 * @copyright 2019 Michael Vangelovski
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_my_day_buttons/control
 */
define(['jquery', 'core/log', 'core/pubsub', 'core/ajax'], function ($, Log, PubSub, Ajax) {
    'use strict';

    // Constants.
    var LAYOUT = {
        SMALL: 'view-small',
        FULL: 'view-full',
    };

    /**
     * Initializes the block controls.
     *
     * @param {int} root The block instance id.
     */
    function init(instanceid, date, username) {
        Log.debug('block_my_day_buttons/control: initializing controls of the my_day_buttons block instance ' + instanceid);

        var region = $('[data-region="block_my_day_buttons-instance-' + instanceid + '"]').first();

        if (!region.length) {
            Log.debug('block_my_day_buttons/control: wrapping region not found!');
            return;
        }

        var control = new MyDayButtonsControl(region, date, username, instanceid);
        control.main();
    }

    /**
     * Controls a single my_day_buttons block instance contents.
     *
     * @constructor
     * @param {jQuery} region
     */
    function MyDayButtonsControl(region, date, username, instanceid) {
        var self = this;
        self.region = region;
        self.date = date;
        self.instanceid = parseInt(instanceid);
        self.username = region.data("timetableuser");
    }

    /**
     * Run the controller.
     *
     */
    MyDayButtonsControl.prototype.main = function () {
        var self = this;

        // Initialise layout.
        self.refreshButtonLayout();
        self.region.removeClass('isloading');

        // Setup nav events
        self.setupEvents();

        // Watch resize to adjust width.
        $(window).on('resize', function () {
            self.refreshButtonLayout();
        });

        // Subscribe to nav drawer event and resize when it completes.
        PubSub.subscribe('nav-drawer-toggle-end', function (el) {
            self.refreshButtonLayout();
        });
    };

    MyDayButtonsControl.prototype.setupEvents = function () {
        var self = this;
        // Unbind all existing events.
        $('#inst' + self.instanceid + '.block_my_day_buttons').unbind();
        
        // Set up navigation events.
        $('#inst' + self.instanceid + '.block_my_day_buttons').on('click', '.timetable-prev', function () {
            self.navigate(0);
        });
        $('#inst' + self.instanceid + '.block_my_day_buttons').on('click', '.timetable-next', function () {
            self.navigate(1);
        });
    }

    /**
     * Navigate to Next day.
     *
     * @method
     */
    MyDayButtonsControl.prototype.navigate = function (direction) {
        var self = this;
        self.region.addClass('fetchingdata');
        var args = {
            timetableuser: self.username,
            nav: direction,
            date: self.date,
            instanceid: self.instanceid
        };
        Log.debug('block_my_day_buttons/content: Navigate timetable: ' + JSON.stringify(args));
        Ajax.call([{
            methodname: 'block_my_day_buttons_get_timetable_html_for_date',
            args: args,
            done:function(response) {
                Log.debug(('Timetable values retrieved successfuly.'));
                self.refreshLayout(response.html);
            },
           fail: function(reason) {
                Log.error('block_my_day_buttons:navigate: Unable to get timetable.');
                Log.debug(reason);
            }
        }]);
    };

    /**
     * Determine period widths.
     *
     */
    MyDayButtonsControl.prototype.refreshButtonLayout = function () {
        var self = this;
        var layoutWidth = self.region.outerWidth();
        Log.debug('Resizing my_day_buttons layout. Width: ' + layoutWidth);

        self.region.removeClass(LAYOUT.SMALL);
        self.region.removeClass(LAYOUT.FULL);

        if (layoutWidth < 520) {
            self.region.addClass(LAYOUT.SMALL);
        } else {
            self.region.addClass(LAYOUT.FULL);
        }
    };

    /**
     * This refresh is use when navigating the timetable
     *
     * @method
     **/
    MyDayButtonsControl.prototype.refreshLayout = function(htmlResult){
        var self = this;

        self.region.fadeOut(300, function() {
            self.region.replaceWith(htmlResult);
            self.region.show();
            self.region = $('[data-region="block_my_day_buttons-instance-' + self.instanceid + '"]');
            self.date = self.region.data("timetabledate");
            self.region.removeClass('isloading');
            self.region.removeClass('fetchingdata');
            self.refreshButtonLayout();
        });
    };
    return {
        init: init
    };
});