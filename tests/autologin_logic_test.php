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

global $CFG;
require_once($CFG->dirroot . '/local/appcrue/locallib.php');

/**
 * Tests covering autologin URL decision logic.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class autologin_logic_test extends \advanced_testcase {
    /**
     * Reset state before every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * No token and no tokenmark must keep URL unchanged.
     */
    public function test_create_deep_url_without_token_or_mark_keeps_original_url(): void {
        $rawurl = '/course/view.php?id=77';

        $deepurl = \local_appcrue_create_deep_url($rawurl, null, null);

        $this->assertSame($rawurl, $deepurl);
    }

    /**
     * With a token, autologin URL must include token + fallback + target URL.
     */
    public function test_create_deep_url_with_token_includes_expected_parameters(): void {
        $deepurl = \local_appcrue_create_deep_url('/course/view.php?id=12', 'abc123', 'appcrue_token', 'continue');

        $this->assertStringContainsString('/local/appcrue/autologin.php', $deepurl);
        $this->assertStringContainsString('token=abc123', $deepurl);
        $this->assertStringContainsString('fallback=continue', $deepurl);
        $this->assertStringContainsString('urltogo=%2Fcourse%2Fview.php%3Fid%3D12', $deepurl);
    }

    /**
     * With mark-only mode, the marker suffix must be appended.
     */
    public function test_create_deep_url_with_token_mark_adds_suffix_marker(): void {
        $deepurl = \local_appcrue_create_deep_url('/my/', null, 'bearer');

        $this->assertStringContainsString('/local/appcrue/autologin.php', $deepurl);
        $this->assertStringEndsWith('&<bearer>', $deepurl);
    }

    /**
     * urltogo parameter has highest priority in target URL logic.
     */
    public function test_get_target_url_prefers_urltogo(): void {
        $target = \local_appcrue_get_target_url('tok', '/mod/forum/view.php?id=3', null, null, null, null, null, null, null);

        $this->assertSame((new \moodle_url('/mod/forum/view.php?id=3'))->out(false), $target->out(false));
    }

    /**
     * pattern parameter should resolve placeholders from the pattern library config.
     */
    public function test_get_target_url_uses_pattern_library(): void {
        set_config(
            'pattern_lib',
            "mycourse=/course/view.php?id={course}\nforum=/mod/forum/view.php?id={param1}&group={group}&y={year}&t={token}",
            'local_appcrue'
        );

        $target = \local_appcrue_get_target_url('tok123', null, 55, 9, 2026, 'forum', 42, 'x', 'y');

        $url = $target->out(false);
        $this->assertStringContainsString('/mod/forum/view.php', $url);
        $this->assertStringContainsString('id=42', $url);
        $this->assertStringContainsString('group=9', $url);
        $this->assertStringContainsString('y=2026', $url);
        $this->assertStringContainsString('t=tok123', $url);
    }

    /**
     * Unknown pattern should throw a Moodle invalid request exception.
     */
    public function test_get_target_url_throws_for_unknown_pattern(): void {
        set_config('pattern_lib', "known=/my/", 'local_appcrue');
        $this->expectException(\moodle_exception::class);

        \local_appcrue_get_target_url('tok', null, null, null, null, 'unknown', null, null, null);
    }

    /**
     * Course search mode should return the matched course view URL.
     */
    public function test_get_target_url_resolves_course_by_course_pattern(): void {
        $course = self::getDataGenerator()->create_course(['idnumber' => 'PRE-200-A-2026-SUF']);
        set_config('course_pattern', '%{course}-{group}-{year}%', 'local_appcrue');

        $target = \local_appcrue_get_target_url('tok', null, '200', 'A', '2026', null, null, null, null);

        $this->assertSame((new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false), $target->out(false));
    }

    /**
     * With no selectors, target should fall back to dashboard.
     */
    public function test_get_target_url_defaults_to_my_page(): void {
        $target = \local_appcrue_get_target_url('tok', null, null, null, null, null, null, null, null);

        $this->assertSame((new \moodle_url('/my/'))->out(false), $target->out(false));
    }
}
