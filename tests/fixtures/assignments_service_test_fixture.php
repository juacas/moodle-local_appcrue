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
 * Test fixtures for local_appcrue.
 *
 * @package local_appcrue
 * @copyright 2026
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;


/**
 * Request-independent assignments service double.
 */
class assignments_service_test_double extends assignments_service {
    /**
     * Avoid authentication while testing service methods.
     */
    public function __construct() {
    }

    /**
     * Expose request configuration for testing.
     */
    public function configure_for_test(): void {
        $this->configure_from_request();
    }

    /**
     * Expose activity formatting for testing.
     *
     * @param object $course Course.
     * @param object $cm Course module.
     * @param object $record Activity record.
     * @return array Formatted activity.
     */
    public function format_activity_for_test($course, $cm, $record): array {
        return $this->format_activity($course, $cm, $record);
    }
}
