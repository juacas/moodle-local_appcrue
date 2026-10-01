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
 * Tests for the grades JSON service.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/gradelib.php');
require_once(__DIR__ . '/appcrue_test_base.php');

/**
 * Tests for the grades JSON service.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue\grades_service::class)]
final class grades_service_test extends appcrue_test_base {
    /**
     * A disabled time window includes grades regardless of modification date.
     */
    public function test_configure_without_time_window_includes_all(): void {
        set_config('lmsappcrue_grades_timewindow', 0, 'local_appcrue');
        $service = new grades_service_test_double();

        $service->configure_for_test();

        $this->assertSame(0, $service->timestart);
    }

    /**
     * The grades endpoint returns visible final grades with filtered names.
     */
    public function test_get_items_returns_final_grade_and_filtered_names(): void {
        $fixture = $this->create_enrolled_user_course([
            'fullname' => '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}',
        ], ['lang' => 'en']);
        $assign = self::getDataGenerator()->create_module('assign', [
            'course' => $fixture['course']->id,
            'name' => '{mlang en}Assignment EN{mlang}{mlang es}Tarea ES{mlang}',
            'grade' => 100,
        ]);
        $gradeitem = \grade_item::fetch([
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'courseid' => $fixture['course']->id,
        ]);
        grade_update(
            'mod/assign',
            $gradeitem->courseid,
            $gradeitem->itemtype,
            $gradeitem->itemmodule,
            $gradeitem->iteminstance,
            $gradeitem->itemnumber,
            [
                'userid' => $fixture['user']->id,
                'rawgrade' => 82,
                'feedback' => '{mlang en}Feedback EN{mlang}{mlang es}Comentario ES{mlang}',
                'feedbackformat' => FORMAT_HTML,
            ]
        );
        $service = new grades_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = 0;
        set_config('lmsappcrue_show_total_grade_as_final', 0, 'local_appcrue');
        $this->use_language('en');

        $items = $service->get_items();

        $matching = array_values(array_filter($items, function($item) use ($assign) {
            return $item['itemname'] === $this->expected_multilang_text(
                'Assignment EN',
                '{mlang en}Assignment EN{mlang}{mlang es}Tarea ES{mlang}'
            ) && $item['itemtype'] === 'mod';
        }));
        $this->assertCount(1, $matching);
        $this->assertSame(
            $this->expected_multilang_text('Course EN', '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}'),
            $matching[0]['coursename']
        );
        $this->assertEquals(82, $matching[0]['finalgrade']);
        $this->assertSame(
            $this->expected_multilang_text('Feedback EN', '{mlang en}Feedback EN{mlang}{mlang es}Comentario ES{mlang}'),
            $matching[0]['feedback']
        );
        $this->assertSame($fixture['user']->id, $matching[0]['userid']);
    }

    /**
     * The total grade option changes the reported item type to category.
     */
    public function test_get_items_reports_course_total_as_category_when_configured(): void {
        $fixture = $this->create_enrolled_user_course();
        $assign = self::getDataGenerator()->create_module('assign', [
            'course' => $fixture['course']->id,
            'name' => 'Assignment',
            'grade' => 100,
        ]);
        $gradeitem = \grade_item::fetch([
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
            'courseid' => $fixture['course']->id,
        ]);
        grade_update(
            'mod/assign',
            $gradeitem->courseid,
            $gradeitem->itemtype,
            $gradeitem->itemmodule,
            $gradeitem->iteminstance,
            $gradeitem->itemnumber,
            ['userid' => $fixture['user']->id, 'rawgrade' => 75]
        );
        $service = new grades_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = 0;
        set_config('lmsappcrue_show_total_grade_as_final', 1, 'local_appcrue');

        $items = $service->get_items();

        $totals = array_values(array_filter($items, function($item) use ($fixture) {
            return $item['courseid'] === $fixture['course']->id && $item['itemtype'] === 'category';
        }));
        $this->assertNotEmpty($totals);
    }
}

/**
 * Request-independent grades service double.
 */
class grades_service_test_double extends grades_service {
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
}
