<?php
// phpcs:disable moodle.PHPUnit.TestCaseCovers.Missing -- PHP attributes keep PHPUnit 11 coverage metadata without PHPUnit 11 docblock deprecations.
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

require_once(__DIR__ . '/appcrue_test_base.php');

/**
 * Tests for keyrotation_service.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue\keyrotation_service::class)]
final class keyrotation_service_test extends appcrue_test_base {
    /**
     * rotate_key updates both API key and last-rotation timestamp.
     */
    public function test_rotate_key_updates_plugin_configuration(): void {
        set_config('api_key', 'oldapikey', 'local_appcrue');
        set_config('api_key_last_rotation', 100, 'local_appcrue');
        $before = time();

        keyrotation_service::rotate_key('oldapikey', 'newapikey');
        $this->assertDebuggingCalled('API key updated from oldapikey to newapikey', DEBUG_NORMAL);
        $after = time();

        $this->assertSame('newapikey', get_config('local_appcrue', 'api_key'));
        $rotationtime = (int)get_config('local_appcrue', 'api_key_last_rotation');
        $this->assertGreaterThanOrEqual($before, $rotationtime);
        $this->assertLessThanOrEqual($after, $rotationtime);
    }

    /**
     * Data response returns the rotated API keys and updates plugin config.
     */
    public function test_get_data_response_rotates_key_and_returns_result(): void {
        set_config('api_key', 'oldapikey', 'local_appcrue');
        $service = $this->new_service_with_old_key('oldapikey');
        $this->set_service_property($service, 'newapikey', 'newapikey');

        $response = $service->get_data_response();

        $this->assertSame([
            'success' => true,
            'message' => 'API key updated successfully.',
            'old_api_key' => 'oldapikey',
            'new_api_key' => 'newapikey',
        ], $response[0]);
        $this->assertSame(1, $response[1]);
        $this->assertSame('newapikey', get_config('local_appcrue', 'api_key'));
    }

    /**
     * Configuration accepts a new API key when rotation is enabled.
     */
    public function test_configure_from_request_accepts_new_api_key(): void {
        set_config('enable_api_rotation', 1, 'local_appcrue');
        $service = $this->new_service_with_old_key('oldapikey');
        $this->set_request_parameters(['newapikey' => 'newapikey']);

        $service->configure_from_request();

        $property = (new \ReflectionClass(keyrotation_service::class))->getProperty('newapikey');
        $this->assertSame('newapikey', $property->getValue($service));
    }

    /**
     * Configuration rejects a missing new API key.
     */
    public function test_configure_from_request_requires_new_api_key(): void {
        set_config('enable_api_rotation', 1, 'local_appcrue');
        $service = $this->new_service_with_old_key('oldapikey');
        $this->set_request_parameters([]);

        $this->expectExceptionCode(keyrotation_service::INVALID_PARAMETER);
        $service->configure_from_request();
    }

    /**
     * Configuration rejects reusing the current API key.
     */
    public function test_configure_from_request_rejects_same_api_key(): void {
        set_config('enable_api_rotation', 1, 'local_appcrue');
        $service = $this->new_service_with_old_key('oldapikey');
        $this->set_request_parameters(['newapikey' => 'oldapikey']);

        $this->expectExceptionCode(keyrotation_service::INVALID_PARAMETER);
        $service->configure_from_request();
    }

    /**
     * Configuration rejects key rotation when the feature is disabled.
     */
    public function test_configure_from_request_requires_rotation_to_be_enabled(): void {
        set_config('enable_api_rotation', 0, 'local_appcrue');
        $service = $this->new_service_with_old_key('oldapikey');
        $this->set_request_parameters(['newapikey' => 'newapikey']);

        $this->expectExceptionMessage('API key rotation is not enabled.');
        $service->configure_from_request();
    }

    /**
     * Create a service without running request authentication.
     *
     * @param string $oldapikey Existing API key.
     * @return keyrotation_service Service instance ready for configuration.
     */
    private function new_service_with_old_key(string $oldapikey): keyrotation_service {
        $service = $this->service_without_constructor(keyrotation_service::class);
        $this->set_service_property($service, 'oldapikey', $oldapikey);
        return $service;
    }
}
