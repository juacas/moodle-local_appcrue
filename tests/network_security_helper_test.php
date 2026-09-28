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

namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for network_security_helper.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \local_appcrue\network_security_helper
 */
final class network_security_helper_test extends \advanced_testcase {
    /**
     * Reset state before every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Missing config must return an empty list.
     */
    public function test_get_blocked_hosts_returns_empty_array_when_not_configured(): void {
        unset_config('api_authorized_networks', 'local_appcrue');
        $helper = new network_security_helper_exposed();

        $this->assertSame([], $helper->get_blocked_hosts_for_test());
    }

    /**
     * Config lines are trimmed and empty entries are ignored.
     */
    public function test_get_blocked_hosts_parses_and_trims_config_lines(): void {
        set_config('api_authorized_networks', " 10.0.0.1 \n\n example.com \n 192.168.1.0/24  \n", 'local_appcrue');
        $helper = new network_security_helper_exposed();

        $this->assertSame(
            ['10.0.0.1', 'example.com', '192.168.1.0/24'],
            $helper->get_blocked_hosts_for_test()
        );
    }
    /**
     * is_request_in_list should return false if 10.0.0.22 is not in the list,
     * true if it is.
     */
    public function test_is_request_in_list_checks_remote_address(): void {
        set_config('api_authorized_networks', "192.168.123.1\n10.0.1.1\n10.0.0.22", 'local_appcrue');
        $helper = new network_security_helper_exposed();
        $this->assertTrue($helper->is_request_in_list());
        set_config('api_authorized_networks', "10.0.0.1/24\n192.168.123.1", 'local_appcrue');
        $this->assertTrue($helper->is_request_in_list());
        set_config('api_authorized_networks', "192.168.123.1\n10.0.1.1/24", 'local_appcrue');
        $this->assertFalse($helper->is_request_in_list());
        set_config('api_authorized_networks', "192.168.123.1\n10.0.1.1/16", 'local_appcrue');
        $this->assertTrue($helper->is_request_in_list());
        set_config('api_authorized_networks', "192.168.123/0.1\n10.0.1.1", 'local_appcrue');
        $this->assertFalse($helper->is_request_in_list());
    }
}

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
