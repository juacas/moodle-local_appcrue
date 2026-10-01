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
 * PHPUnit coverage for the appcrue service.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/testable_service.php');

/**
 * Tests for appcrue_service behavior.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue\appcrue_service::class)]
final class appcrue_service_test extends \advanced_testcase {
    /**
     * Reset state before every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Base service should return [items, count(items)].
     */
    public function test_get_data_response_returns_items_and_count(): void {
        $items = [['id' => 11], ['id' => 22], ['id' => 33]];
        $service = new testable_service($items);

        [$data, $count] = $service->get_data_response();

        $this->assertSame($items, $data);
        $this->assertSame(3, $count);
    }

    /**
     * JSON response envelope includes core fields and generated timestamp.
     */
    public function test_get_response_json_contains_expected_structure(): void {
        $items = [['id' => 1], ['id' => 2]];
        $service = new testable_service($items);
        $before = time();

        $response = $service->get_response_json();

        $this->assertTrue($response['success']);
        $this->assertSame(2, $response['count']);
        $this->assertSame($items, $response['data']);
        $this->assertGreaterThanOrEqual($before, $response['timestamp']);
        $this->assertLessThanOrEqual(time(), $response['timestamp']);
    }

    /**
     * Service is enabled only when config value is exactly string "1".
     */
    public function test_is_enabled_reads_service_specific_config(): void {
        $service = new testable_service([]);
        $configkey = 'lmsappcrue_enable_testable';

        unset_config($configkey, 'local_appcrue');
        $this->assertFalse($service->is_enabled());

        set_config($configkey, '0', 'local_appcrue');
        $this->assertFalse($service->is_enabled());

        set_config($configkey, '1', 'local_appcrue');
        $this->assertTrue($service->is_enabled());
    }
}
