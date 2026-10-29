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

namespace local_appcrue\event;

use local_appcrue\troubleshooting_helper;

/**
 * Tests successful API access event logging for the troubleshooting report.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(api_accessed::class)]
final class api_accessed_event_test extends \advanced_testcase {
    /**
     * The event is recorded with its endpoint and user, without credential payloads.
     */
    public function test_successful_api_access_is_reportable_without_secrets(): void {
        global $DB;
        $this->resetAfterTest();
        if (!$DB->get_manager()->table_exists(new \xmldb_table('logstore_standard_log'))) {
            $this->markTestSkipped('The standard Moodle log table is unavailable.');
        }

        $user = $this->getDataGenerator()->create_user();
        $event = api_accessed::create([
            'userid' => $user->id,
            'other' => ['endpoint' => 'calendar'],
        ]);
        $this->assertSame('calendar', $event->other['endpoint']);
        $this->assertStringContainsString('calendar', $event->get_description());

        $context = \context_system::instance();
        $DB->insert_record('logstore_standard_log', (object)[
            'eventname' => '\\local_appcrue\\event\\api_accessed',
            'component' => 'local_appcrue',
            'action' => 'viewed',
            'target' => 'lms_api',
            'objecttable' => null,
            'objectid' => null,
            'crud' => 'r',
            'edulevel' => \core\event\base::LEVEL_OTHER,
            'contextid' => $context->id,
            'contextlevel' => CONTEXT_SYSTEM,
            'contextinstanceid' => $context->instanceid,
            'userid' => $user->id,
            'courseid' => SITEID,
            'relateduserid' => null,
            'anonymous' => 0,
            'other' => json_encode([
                'endpoint' => 'calendar',
                'apikey' => 'secret-test-value',
                'token' => 'secret-token-value',
            ]),
            'timecreated' => time(),
            'origin' => 'web',
            'ip' => '192.0.2.1',
            'realuserid' => null,
        ]);

        $records = troubleshooting_helper::get_recent_log_events(time() - 60);
        $this->assertNotNull($records);
        $record = null;
        foreach ($records as $candidate) {
            if ($candidate->eventname === '\\local_appcrue\\event\\api_accessed' && $candidate->userid == $user->id) {
                $record = $candidate;
                break;
            }
        }
        $this->assertNotNull($record, 'The successful API event should be present in the troubleshooting report.');
        $this->assertObjectNotHasProperty('other', $record);
        $this->assertObjectNotHasProperty('description', $record);
        $this->assertSame('192.0.2.1', $record->ip);
        $this->assertSame('calendar', $record->endpoint);
    }
}
