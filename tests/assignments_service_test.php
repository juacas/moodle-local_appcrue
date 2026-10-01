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
 * Tests for the assignments JSON service.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/appcrue_test_base.php');

/**
 * Tests for the assignments JSON service.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue\assignments_service::class)]
final class assignments_service_test extends appcrue_test_base {
    /**
     * A disabled or invalid time window includes all assignments.
     */
    public function test_configure_without_time_window_includes_all(): void {
        set_config('lmsappcrue_assignments_timewindow', 0, 'local_appcrue');
        $service = new assignments_service_test_double();

        $service->configure_for_test();

        $this->assertNull($service->timestart);
    }

    /**
     * The configured time window wins over an older request timestamp.
     */
    public function test_configure_uses_latest_time_window_start(): void {
        set_config('lmsappcrue_assignments_timewindow', 3600, 'local_appcrue');
        $this->set_request_parameters(['timestart' => 100]);
        $service = new assignments_service_test_double();
        $before = time() - 3600;

        $service->configure_for_test();

        $this->assertGreaterThanOrEqual($before, $service->timestart);
        $this->assertLessThanOrEqual(time(), $service->timestart);
    }

    /**
     * Activity formatting applies the module and course contexts to multilang text.
     */
    public function test_format_activity_filters_title_description_and_course_name(): void {
        global $DB;
        $this->use_language('en');
        $course = self::getDataGenerator()->create_course([
            'fullname' => '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}',
        ]);
        $assign = self::getDataGenerator()->create_module('assign', [
            'course' => $course->id,
            'name' => '{mlang en}Assignment EN{mlang}{mlang es}Tarea ES{mlang}',
            'intro' => '{mlang en}Description EN{mlang}{mlang es}Descripción ES{mlang}',
            'introformat' => FORMAT_HTML,
            'duedate' => time() + 3600,
        ]);
        $cm = get_fast_modinfo($course)->get_cm($assign->cmid);
        $record = $DB->get_record('assign', ['id' => $assign->id], '*', MUST_EXIST);
        $service = new assignments_service_test_double();
        $this->set_service_property($service, 'assignmentsdates', [
            'mod_assign' => ['table' => 'assign', 'duedate' => 'duedate', 'cutoffdate' => 'cutoffdate'],
        ]);

        $result = $service->format_activity_for_test($course, $cm, $record);

        $this->assertSame(

            $this->expected_multilang_text('Course EN', '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}'),

            $result['course_title']

        );
        $this->assertSame(
            $this->expected_multilang_text('Assignment EN', '{mlang en}Assignment EN{mlang}{mlang es}Tarea ES{mlang}'),
            $result['title']
        );
        $this->assertSame(
            $this->expected_multilang_text('Description EN', '{mlang en}Description EN{mlang}{mlang es}Descripción ES{mlang}'),
            $result['description']
        );
        $this->assertSame($record->duedate, $result['due_at']);
        $this->assertSame('assign', $result['type']);
    }

    /**
     * The endpoint returns only visible, mapped activities with grade items.
     */
    public function test_get_items_returns_enrolled_assignment(): void {
        $fixture = $this->create_enrolled_user_course([
            'fullname' => '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}',
        ], ['lang' => 'en']);
        $assign = self::getDataGenerator()->create_module('assign', [
            'course' => $fixture['course']->id,
            'name' => '{mlang en}Assignment EN{mlang}{mlang es}Tarea ES{mlang}',
            'intro' => 'Assignment description',
            'duedate' => time() + 3600,
        ]);
        $service = new assignments_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = null;
        $this->set_service_property($service, 'assignmentsdates', [
            'mod_assign' => ['table' => 'assign', 'duedate' => 'duedate', 'cutoffdate' => 'cutoffdate'],
        ]);
        $this->use_language('en');

        $items = $service->get_items();

        $this->assertCount(1, $items);
        $this->assertSame($assign->cmid, (int)(new \moodle_url($items[0]['html_url']))->get_param('id'));
        $this->assertSame(
            $this->expected_multilang_text('Assignment EN', '{mlang en}Assignment EN{mlang}{mlang es}Tarea ES{mlang}'),
            $items[0]['title']
        );
    }
}

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
