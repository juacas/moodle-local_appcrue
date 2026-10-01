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


/**
 * Shared helpers for the AppCrue PHPUnit tests.
 *
 * This file is intentionally not suffixed with _test.php. It is a support
 * class loaded by the individual test cases and is not a test case itself.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class appcrue_test_base extends \advanced_testcase {
    /** @var array Original GET parameters. */
    private array $originalget;
    /** @var array Original POST parameters. */
    private array $originalpost;
    /** @var array Original server parameters. */
    private array $originalserver;

    /**
     * Reset Moodle and request state before each test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->originalget = $_GET;
        $this->originalpost = $_POST;
        $this->originalserver = $_SERVER;
    }

    /**
     * Restore request state after each test.
     */
    public function tearDown(): void {
        $_GET = $this->originalget;
        $_POST = $this->originalpost;
        $_SERVER = $this->originalserver;
        parent::tearDown();
    }

    /**
     * Instantiate a service without running its request-dependent constructor.
     *
     * @param string $classname Fully-qualified service class.
     * @return object Service instance.
     */
    protected function service_without_constructor(string $classname): object {
        return (new \ReflectionClass($classname))->newInstanceWithoutConstructor();
    }

    /**
     * Set a property, including a private property declared by a parent class.
     *
     * @param object $object Object to modify.
     * @param string $property Property name.
     * @param mixed $value Property value.
     */
    protected function set_service_property(object $object, string $property, mixed $value): void {
        $reflection = new \ReflectionClass($object);
        while (!$reflection->hasProperty($property) && $reflection->getParentClass()) {
            $reflection = $reflection->getParentClass();
        }
        $reflection->getProperty($property)->setValue($object, $value);
    }

    /**
     * Populate the request parameters consumed by optional_param/required_param.
     *
     * @param array $parameters Request parameters.
     */
    protected function set_request_parameters(array $parameters): void {
        $_GET = $parameters;
        $_POST = [];
    }

    /**
     * Enable the standard Moodle multilang filter for a test.
     *
     * @param string $language Current language.
     */
    protected function use_language(string $language): void {
        if ($this->has_multilang2_filter()) {
            filter_set_global_state('multilang2', TEXTFILTER_ON);
            filter_set_applies_to_strings('multilang2', true);
            \filter_manager::instance()->reset_caches();
        }
        force_current_language($language);
    }

    /**
     * Whether the optional multilang2 filter is available in this Moodle checkout.
     *
     * @return bool
     */
    protected function has_multilang2_filter(): bool {
        return array_key_exists('multilang2', \core_component::get_plugin_list('filter'));
    }

    /**
     * Expected text for fixtures using multilang2 markup.
     *
     * @param string $english English text when the filter is available.
     * @param string $source Original fixture text.
     * @return string
     */
    protected function expected_multilang_text(string $english, string $source): string {
        return $this->has_multilang2_filter() ? $english : $source;
    }

    /**
     * Create a course and an enrolled user for endpoint integration tests.
     *
     * @param array $courseoptions Course generator options.
     * @param array $useroptions User generator options.
     * @param string|int $role Role shortname or id.
     * @return array{course: \stdClass, user: \stdClass}
     */
    protected function create_enrolled_user_course(
        array $courseoptions = [],
        array $useroptions = [],
        string|int $role = 'student'
    ): array {
        $generator = self::getDataGenerator();
        $course = $generator->create_course($courseoptions);
        $user = $generator->create_user($useroptions);
        $generator->enrol_user($user->id, $course->id, $role);
        return ['course' => $course, 'user' => $user];
    }
}
