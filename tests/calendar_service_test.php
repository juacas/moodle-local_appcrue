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
 * Tests for calendar services.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/appcrue/locallib.php');
require_once($CFG->dirroot . '/calendar/lib.php');
require_once(__DIR__ . '/appcrue_test_base.php');
require_once(__DIR__ . '/fixtures/calendar_service_test_fixture.php');

/**
 * Tests for the calendar service and both calendar response formats.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue\calendar_service::class)]
final class calendar_service_test extends appcrue_test_base {
    /**
     * Request parameters configure the calendar time range.
     */
    public function test_configure_reads_time_range(): void {
        $this->set_request_parameters(['timestart' => 100, 'timeend' => 200]);
        $service = new calendar_service_test_double();

        $service->configure_for_test();

        $this->assertSame(100, $service->timestart);
        $this->assertSame(200, $service->timeend);
    }

    /**
     * The inherited data response returns calendar items and their count.
     */
    public function test_get_data_response_returns_items_and_count(): void {
        $items = [['id' => 10], ['id' => 20]];
        $service = $this->getMockBuilder(calendar_service::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_items'])
            ->getMock();
        $service->expects($this->once())->method('get_items')->willReturn($items);

        $this->assertSame([$items, 2], $service->get_data_response());
    }

    /**
     * Invalid ranges are rejected before querying the calendar.
     */
    public function test_get_events_rejects_inverted_time_range(): void {
        $user = self::getDataGenerator()->create_user();

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(404);
        calendar_service::get_events($user, 200, 100);
    }

    /**
     * Calendar events are grouped by date and category-filtered for usercalendar.
     */
    public function test_format_events_for_usercalendar_groups_and_filters_events(): void {
        $fixture = $this->create_enrolled_user_course([], ['lang' => 'en']);
        $eventtime = strtotime('2030-01-02 10:00:00 UTC');
        $assign = self::getDataGenerator()->create_module('assign', ['course' => $fixture['course']->id]);
        $event = (object)[
            'id' => 123,
            'name' => '{mlang en}Exam EN{mlang}{mlang es}Examen ES{mlang}',
            'description' => '{mlang en}Description EN{mlang}{mlang es}Descripción ES{mlang}',
            'location' => '{mlang en}Room EN{mlang}{mlang es}Sala ES{mlang}',
            'format' => FORMAT_HTML,
            'courseid' => $fixture['course']->id,
            'groupid' => 0,
            'userid' => $fixture['user']->id,
            'modulename' => 'assign',
            'instance' => $assign->id,
            'eventtype' => 'due',
            'timestart' => $eventtime,
            'timesort' => $eventtime,
            'timeduration' => 3600,
            'visible' => 1,
        ];
        $this->use_language('en');
        set_config('calendar_examen_event_type', 'quiz', 'local_appcrue');

        $output = calendar_service::format_events_for_usercalendar(
            [$event],
            $fixture['user'],
            'EXAMEN'
        );

        $this->assertCount(0, $output->calendar);

        $output = calendar_service::format_events_for_usercalendar(
            [$event],
            $fixture['user'],
            'HORARIO'
        );
        $this->assertCount(1, $output->calendar[0]->events);

        set_config('calendar_examen_event_type', 'assign', 'local_appcrue');
        $output = calendar_service::format_events_for_usercalendar(
            [$event],
            $fixture['user'],
            'EXAMEN'
        );
        $this->assertCount(1, $output->calendar[0]->events);
        $formatted = $output->calendar[0]->events[0];
        $this->assertStringContainsString(
            $this->expected_multilang_text('Exam EN', '{mlang en}Exam EN{mlang}{mlang es}Examen ES{mlang}'),
            $formatted->title
        );
        if ($this->has_multilang2_filter()) {
            $this->assertStringNotContainsString('{mlang', $formatted->title);
        }
        $this->assertSame(
            $this->expected_multilang_text('Description EN', '{mlang en}Description EN{mlang}{mlang es}Descripción ES{mlang}'),
            trim($formatted->description)
        );
        $this->assertSame($eventtime, $formatted->startsAt);
        $this->assertSame($eventtime + 3600, $formatted->endsAt);
    }

    /**
     * The LMS response includes filtered names, locations, descriptions and URLs.
     */
    public function test_format_events_for_lmsappcrue_returns_filtered_event(): void {
        $fixture = $this->create_enrolled_user_course([
            'fullname' => '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}',
        ], ['lang' => 'en']);
        $assign = self::getDataGenerator()->create_module('assign', ['course' => $fixture['course']->id]);
        $event = (object)[
            'id' => 123,
            'name' => '{mlang en}Event EN{mlang}{mlang es}Evento ES{mlang}',
            'description' => '{mlang en}Description EN{mlang}{mlang es}Descripción ES{mlang}',
            'location' => '{mlang en}Location EN{mlang}{mlang es}Ubicación ES{mlang}',
            'format' => FORMAT_HTML,
            'courseid' => $fixture['course']->id,
            'groupid' => 0,
            'userid' => $fixture['user']->id,
            'modulename' => 'assign',
            'instance' => $assign->id,
            'eventtype' => 'due',
            'timestart' => time(),
            'timeduration' => 0,
        ];
        $service = new calendar_service_test_double();
        $this->use_language('en');

        $items = $service->format_events_for_lmsappcrue_for_test([$event], $fixture['user']);

        $this->assertCount(1, $items);
        $this->assertStringContainsString(
            $this->expected_multilang_text('Event EN', '{mlang en}Event EN{mlang}{mlang es}Evento ES{mlang}'),
            $items[0]['name']
        );
        if ($this->has_multilang2_filter()) {
            $this->assertStringNotContainsString('{mlang', $items[0]['name']);
        }
        $this->assertSame(
            $this->expected_multilang_text('Description EN', '{mlang en}Description EN{mlang}{mlang es}Descripción ES{mlang}'),
            trim($items[0]['description'])
        );
        $this->assertSame(
            $this->expected_multilang_text('Course EN', '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}'),
            $items[0]['fullname']
        );
        $this->assertSame(
            $this->expected_multilang_text('Location EN', '{mlang en}Location EN{mlang}{mlang es}Ubicación ES{mlang}'),
            $items[0]['location']
        );
    }

    /**
     * Event URLs use the configured deep-link marker when no token is present.
     */
    public function test_get_event_url_uses_deep_link_marker(): void {
        $event = (object)[
            'timestart' => 123,
            'eventtype' => 'user',
        ];

        $url = calendar_service::get_event_url($event, null, null, 'bearer');

        $this->assertStringContainsString('/local/appcrue/autologin.php', $url);
        $this->assertStringEndsWith('&<bearer>', $url);
    }
}
