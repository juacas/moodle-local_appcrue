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

namespace local_appcrue\event;

/**
 * Event unauthorized_ip_event
 *
 * @package    local_appcrue
 * @copyright  2026 Juan Pablo de Castro <juan.pablo.de.castro@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unauthorized_ip_failed extends \core\event\base {
    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        // System context since this is not related to a specific user or course.
        $this->context = \context_system::instance();
    }
    /**
     * Returns a description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $other = (array)$this->other;
        return get_string('event_unauthorized_ip_failed_desc', 'local_appcrue', [
            'ipaddress' => $other['ipaddress'] ?? '',
        ]);
    }
    /**
     * Returns the name of the event.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_unauthorized_ip_failed_name', 'local_appcrue');
    }
    /**
     * No other mappings.
     * @return bool
     */
    public static function get_other_mapping() {
        return false;
    }
}
