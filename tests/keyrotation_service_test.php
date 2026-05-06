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
 * Tests for keyrotation_service.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \local_appcrue\keyrotation_service
 */
final class keyrotation_service_test extends \advanced_testcase {
    /**
     * Reset state before every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * rotate_key updates both API key and last-rotation timestamp.
     */
    public function test_rotate_key_updates_plugin_configuration(): void {
        set_config('api_key', 'oldapikey', 'local_appcrue');
        set_config('api_key_last_rotation', 100, 'local_appcrue');
        $before = time();

        keyrotation_service::rotate_key('oldapikey', 'newapikey');
        $after = time();

        $this->assertSame('newapikey', get_config('local_appcrue', 'api_key'));
        $rotationtime = (int)get_config('local_appcrue', 'api_key_last_rotation');
        $this->assertGreaterThanOrEqual($before, $rotationtime);
        $this->assertLessThanOrEqual($after, $rotationtime);
    }
}
