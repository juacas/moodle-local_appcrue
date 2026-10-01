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
 * Small testing subclass exposing protected behavior.
 */
class network_security_helper_exposed extends network_security_helper {
    /**
     * Public wrapper for protected get_blocked_hosts().
     *
     * @return array
     */
    public function get_blocked_hosts_for_test(): array {
        return $this->get_blocked_hosts();
    }
    /**
     * Override to avoid using the real remote address in tests.
     * Returns 10.0.0.22 for testing purposes.
     */
    public static function getremoteaddr() {
        return '10.0.0.22';
    }
}
