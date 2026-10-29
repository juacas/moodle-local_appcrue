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
 * Successful access to an AppCRUE LMS data endpoint.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_accessed extends \core\event\base {
    /**
     * Set basic event properties.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = null;
        $this->context = \context_system::instance();
    }

    /**
     * Describe the successful endpoint access without recording credentials.
     *
     * @return string
     */
    public function get_description() {
        return get_string('event_api_accessed_desc', 'local_appcrue', [
            'userid' => $this->userid,
            'endpoint' => $this->other['endpoint'] ?? '',
        ]);
    }

    /**
     * Return the event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_api_accessed_name', 'local_appcrue');
    }

    /**
     * No auxiliary values are mapped to backup data.
     *
     * @return bool
     */
    public static function get_other_mapping() {
        return false;
    }
}
