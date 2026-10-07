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

/**
 * Tests for handling failed AppCrue autologin attempts.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(autologin_helper::class)]
final class autologin_helper_test extends \advanced_testcase {
    /**
     * Reset state before every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * The error fallback stops before logging in and records the failed attempt.
     */
    public function test_null_user_with_error_fallback_stops_and_logs_failure(): void {
        $this->setUser();
        $eventsink = $this->redirectEvents();

        $shouldcontinue = autologin_helper::handle_failed_login('error', 'invalid-token', (object)['result' => 'Invalid token']);

        global $USER;
        $this->assertFalse($shouldcontinue);
        $this->assertSame(0, $USER->id);
        $this->assert_failure_was_logged($eventsink, 'Invalid token');
    }

    /**
     * The logout fallback ends the current session and records the failed attempt.
     */
    public function test_null_user_with_logout_fallback_logs_out_and_continues(): void {
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        $eventsink = $this->redirectEvents();

        $shouldcontinue = autologin_helper::handle_failed_login('logout', 'invalid-token', (object)['result' => 'Invalid token']);

        global $USER;
        $this->assertTrue($shouldcontinue);
        $this->assertSame(0, $USER->id);
        $this->assert_failure_was_logged($eventsink, 'Invalid token');
    }

    /**
     * The continue fallback requests guest login when there is no current session.
     */
    public function test_null_user_with_continue_fallback_requests_guest_login(): void {
        $this->setUser();
        $eventsink = $this->redirectEvents();
        $guestuser = null;

        $shouldcontinue = autologin_helper::handle_failed_login(
            'continue',
            'invalid-token',
            (object)['result' => 'Invalid token'],
            function ($user) use (&$guestuser) {
                $guestuser = $user;
            }
        );

        $this->assertTrue($shouldcontinue);
        $this->assertSame('guest', $guestuser->username);
        $this->assert_failure_was_logged($eventsink, 'Invalid token Continuing as guest or current user.');
    }

    /**
     * Check the failure event and its diagnosis.
     *
     * @param \phpunit_event_sink $eventsink Captured Moodle events.
     * @param string $diagnosis Expected failure diagnosis.
     */
    private function assert_failure_was_logged(\phpunit_event_sink $eventsink, string $diagnosis): void {
        $events = array_values(array_filter($eventsink->get_events(), function ($event) {
            return $event instanceof \local_appcrue\event\autologin_failed;
        }));
        $eventsink->close();

        $this->assertCount(1, $events);
        $this->assertSame($diagnosis, $events[0]->get_data()['other']['diagnosis']);
        $this->assertSame('invalid-token', $events[0]->get_data()['other']['token']);
    }
}
