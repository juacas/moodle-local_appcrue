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
 * Request-independent calendar service double.
 */
class calendar_service_test_double extends calendar_service {
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
     * Expose LMS event formatting for testing.
     *
     * @param array $events Events.
     * @param \stdClass $user User.
     * @return array Formatted events.
     */
    public function format_events_for_lmsappcrue_for_test(array $events, \stdClass $user): array {
        return $this->format_events_for_lmsappcrue($events, $user);
    }
}
