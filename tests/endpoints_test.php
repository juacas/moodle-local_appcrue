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

/**
 * Tests for REST endpoint resolution.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/appcrue/locallib.php');
require_once($CFG->dirroot . '/local/appcrue/externallib.php');
require_once(__DIR__ . '/appcrue_test_base.php');

/**
 * Tests for dynamic REST endpoint discovery and web-service declarations.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversMethod(\local_appcrue\appcrue_service::class, 'instance_from_request')]
final class endpoints_test extends appcrue_test_base {
    /**
     * Every dynamic REST service resolves to the expected implementation class.
     */
    public function test_dynamic_endpoint_resolves_to_service(): void {
        $user = self::getDataGenerator()->create_user(['username' => 'endpoint_user']);
        set_config('api_key', 'endpointkey', 'local_appcrue');
        set_config('lmsappcrue_match_user_by', 'username', 'local_appcrue');
        set_config('lmsappcrue_use_user_param', 'username', 'local_appcrue');
        foreach (['calendar', 'forums', 'files', 'grades', 'announcements', 'assignments', 'keyrotation'] as $endpoint) {
            set_config("lmsappcrue_enable_{$endpoint}", '1', 'local_appcrue');
            $request = [
                'file' => $endpoint,
                'apikey' => 'endpointkey',
                'username' => $user->username,
            ];
            if ($endpoint === 'keyrotation') {
                set_config('enable_api_rotation', 1, 'local_appcrue');
                $request['newapikey'] = 'newendpointkey';
            }
            $this->set_request_parameters($request);

            $service = appcrue_service::instance_from_request();

            $this->assertInstanceOf("local_appcrue\\{$endpoint}_service", $service);
            if ($endpoint !== 'keyrotation') {
                $this->assertSame($user->id, $service->user->id ?? null);
            }
        }
    }

    /**
     * Empty endpoint paths return the documented invalid-parameter error.
     */
    public function test_dynamic_endpoint_requires_endpoint_name(): void {
        $_SERVER['SERVER_SOFTWARE'] = 'PHPUnit';
        $this->set_request_parameters(['file' => '']);

        $this->expectExceptionCode(appcrue_service::INVALID_PARAMETER);
        appcrue_service::instance_from_request();
    }

    /**
     * Disabled and unknown endpoints are rejected before construction.
     */
    public function test_dynamic_endpoint_rejects_disabled_endpoint(): void {
        unset_config('lmsappcrue_enable_notreal', 'local_appcrue');
        $this->set_request_parameters(['file' => 'notreal']);

        $this->expectExceptionCode(appcrue_service::UNKNOWN_ENDPOINT);
        appcrue_service::instance_from_request();
    }

    /**
     * The three Moodle web-service declarations point to existing callable methods.
     *
     */
    public function test_declared_external_functions_are_callable(): void {
        global $CFG;
        $functions = [];
        include($CFG->dirroot . '/local/appcrue/db/services.php');

        $this->assertCount(3, $functions);
        foreach ($functions as $function) {
            $this->assertFileExists($CFG->dirroot . '/' . $function['classpath']);
            $this->assertTrue(class_exists($function['classname']));
            $this->assertTrue(method_exists($function['classname'], $function['methodname']));
        }
    }

    /**
     * The endpoint error envelope maps known internal errors to HTTP statuses.
     */
    public function test_error_code_map_is_exposed_by_exception_codes(): void {
        $this->assertSame(1, appcrue_service::INVALID_API_KEY);
        $this->assertSame(2, appcrue_service::MISSING_WS_TOKEN);
        $this->assertSame(3, appcrue_service::USER_NOT_ENROLLED);
        $this->assertSame(4, appcrue_service::JSON_DECODE_ERROR);
        $this->assertSame(5, appcrue_service::INVALID_PARAMETER);
        $this->assertSame(6, appcrue_service::UNKNOWN_ENDPOINT);
    }
}
