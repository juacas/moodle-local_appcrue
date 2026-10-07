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
 * Tests for announcements responses.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/appcrue_test_base.php');
require_once(__DIR__ . '/fixtures/announcements_service_test_fixture.php');

/**
 * Tests for the announcements JSON service.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue\announcements_service::class)]
final class announcements_service_test extends appcrue_test_base {
    /**
     * A disabled time window includes announcements regardless of age.
     */
    public function test_configure_without_time_window_includes_all(): void {
        set_config('lmsappcrue_announcements_timewindow', 0, 'local_appcrue');
        $service = new announcements_service_test_double();

        $service->configure_for_test();

        $this->assertSame(0, $service->timestart);
    }

    /**
     * A configured time window is combined with the requested start timestamp.
     */
    public function test_configure_uses_latest_requested_or_window_start(): void {
        $window = 3600;
        $requestedstart = time() - 100;
        set_config('lmsappcrue_announcements_timewindow', $window, 'local_appcrue');
        $this->set_request_parameters(['timestart' => $requestedstart]);
        $service = new announcements_service_test_double();

        $service->configure_for_test();

        $this->assertSame($requestedstart, $service->timestart);
    }

    /**
     * Data response wraps announcement items and preserves their count.
     */
    public function test_get_data_response_wraps_items_and_count(): void {
        $items = [['id' => 10], ['id' => 20]];
        $service = $this->getMockBuilder(announcements_service::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_items'])
            ->getMock();
        $service->expects($this->once())->method('get_items')->willReturn([$items, 2]);

        $this->assertSame([['announcements' => $items], 2], $service->get_data_response());
    }

    /**
     * News forum posts are returned with filtered course, forum and subject text.
     */
    public function test_get_items_returns_news_post(): void {
        $fixture = $this->create_enrolled_user_course([
            'fullname' => '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}',
        ], ['lang' => 'en']);
        $this->setUser($fixture['user']);
        $forum = self::getDataGenerator()->create_module('forum', [
            'course' => $fixture['course']->id,
            'type' => 'news',
            'name' => '{mlang en}Announcements EN{mlang}{mlang es}Anuncios ES{mlang}',
            'intro' => 'Announcements',
        ]);
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion([
            'course' => $fixture['course']->id,
            'forum' => $forum->id,
            'userid' => $fixture['user']->id,
            'name' => '{mlang en}Subject EN{mlang}{mlang es}Asunto ES{mlang}',
            'message' => '{mlang en}Message EN{mlang}{mlang es}Mensaje ES{mlang}',
            'messageformat' => FORMAT_HTML,
        ]);
        $service = new announcements_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = 0;
        $this->use_language('en');

        [$items, $count] = $service->get_items();

        $this->assertSame(1, $count);
        $this->assertCount(1, $items);
        $this->assertSame(
            $this->expected_multilang_text('Course EN', '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}'),
            $items[0]['coursefullname']
        );
        $this->assertSame(
            $this->expected_multilang_text('Announcements EN', '{mlang en}Announcements EN{mlang}{mlang es}Anuncios ES{mlang}'),
            $items[0]['forumname']
        );
        $this->assertSame(
            $this->expected_multilang_text('Subject EN', '{mlang en}Subject EN{mlang}{mlang es}Asunto ES{mlang}'),
            $items[0]['subject']
        );
        $this->assertSame(
            $this->expected_multilang_text('Message EN', '{mlang en}Message EN{mlang}{mlang es}Mensaje ES{mlang}'),
            trim($items[0]['message'])
        );
        $this->assertSame($discussion->course, $items[0]['courseid']);
        if ($this->has_multilang2_filter()) {
            $this->assertStringNotContainsString('{mlang', json_encode($items[0]));
        }
    }

    /**
     * Announcements older than the configured/requested threshold are excluded.
     */
    public function test_get_items_applies_timestart(): void {
        global $DB;
        $fixture = $this->create_enrolled_user_course();
        $this->setUser($fixture['user']);
        $forum = self::getDataGenerator()->create_module('forum', [
            'course' => $fixture['course']->id,
            'type' => 'news',
        ]);
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion([
            'course' => $fixture['course']->id,
            'forum' => $forum->id,
            'userid' => $fixture['user']->id,
        ]);
        $oldtime = time() - 1000;
        $discussionrecord = $DB->get_record('forum_discussions', ['id' => $discussion->id], '*', MUST_EXIST);
        $discussionrecord->timemodified = $oldtime;
        $DB->update_record('forum_discussions', $discussionrecord);
        $service = new announcements_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = time() - 100;

        [, $count] = $service->get_items();

        $this->assertSame(0, $count);
    }
}
