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

/**
 * Tests for safe configuration repairs in the troubleshooting helper.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(troubleshooting_helper::class)]
final class troubleshooting_helper_test extends \advanced_testcase {
    /**
     * Finds the latest completed autologin within the selected log period.
     */
    public function test_recent_log_summary_reports_latest_successful_autologin(): void {
        global $DB;

        $this->resetAfterTest();
        if (!$DB->get_manager()->table_exists(new \xmldb_table('logstore_standard_log'))) {
            $this->markTestSkipped('The standard Moodle log table is unavailable.');
        }

        $user = $this->getDataGenerator()->create_user();
        $context = \context_system::instance();
        $now = time();
        $record = (object)[
            'eventname' => '\\local_appcrue\\event\\autologin_login',
            'component' => 'local_appcrue',
            'action' => 'loggedin',
            'target' => 'user',
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
            'other' => null,
            'timecreated' => $now - 120,
            'origin' => 'web',
            'ip' => '192.0.2.1',
            'realuserid' => null,
        ];
        $DB->insert_record('logstore_standard_log', clone $record);
        $record->timecreated = $now - 30;
        $DB->insert_record('logstore_standard_log', clone $record);
        $record->eventname = '\\local_appcrue\\event\\autologin_failed';
        $record->timecreated = $now - 10;
        $DB->insert_record('logstore_standard_log', clone $record);

        $summary = troubleshooting_helper::get_recent_log_summary($now - 180);
        $this->assertSame($now - 30, $summary['lastsuccessfulautologin']);

        $recent = troubleshooting_helper::get_recent_log_summary($now - 20);
        $this->assertNull($recent['lastsuccessfulautologin']);
    }

    /**
     * Interprets the optional log cutoff in the administrator's Moodle timezone.
     */
    public function test_parse_log_since_uses_user_timezone_and_rejects_invalid_dates(): void {
        $this->resetAfterTest(true);
        $this->setUser($this->getDataGenerator()->create_user(['timezone' => 'Europe/Madrid']));

        $this->assertSame(
            strtotime('2026-10-29 09:30:00 UTC'),
            troubleshooting_helper::parse_log_since('2026-10-29T10:30')
        );
        $this->assertNull(troubleshooting_helper::parse_log_since('2026-02-30T10:30'));
        $this->assertNull(troubleshooting_helper::parse_log_since('2026-10-29T10:30Z'));
    }

    /**
     * Reports the effective request IP through Moodle's global IP helper.
     */
    public function test_get_request_ip_diagnostics_uses_moodle_ip_helper(): void {
        global $CFG;

        $originalserver = $_SERVER;
        $hadremoteaddrsetting = isset($CFG->getremoteaddrconf);
        $originalremoteaddrsetting = $CFG->getremoteaddrconf ?? null;
        try {
            $_SERVER['REMOTE_ADDR'] = '192.0.2.44';
            unset($_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_X_FORWARDED_FOR']);
            $CFG->getremoteaddrconf = GETREMOTEADDR_SKIP_DEFAULT;

            $diagnostics = troubleshooting_helper::get_request_ip_diagnostics();

            $this->assertSame('192.0.2.44', $diagnostics['effective']);
            $this->assertSame('192.0.2.44', $diagnostics['remoteaddr']);
            $this->assertSame('REMOTE_ADDR only', $diagnostics['selection']);
        } finally {
            $_SERVER = $originalserver;
            if ($hadremoteaddrsetting) {
                $CFG->getremoteaddrconf = $originalremoteaddrsetting;
            } else {
                unset($CFG->getremoteaddrconf);
            }
        }
    }

    /**
     * Appends exact addresses while preserving existing entries and avoiding duplicates.
     */
    public function test_merge_networks_preserves_existing_entries(): void {
        $actual = troubleshooting_helper::merge_networks(
            "192.0.2.10\r\n198.51.100.0/24\n",
            ['203.0.113.8', '192.0.2.10']
        );

        $this->assertSame("192.0.2.10\n198.51.100.0/24\n203.0.113.8", $actual);
    }

    /**
     * Rejects additions that are not literal IP addresses.
     */
    public function test_merge_networks_rejects_ranges_and_invalid_values(): void {
        $this->expectException(\InvalidArgumentException::class);
        troubleshooting_helper::merge_networks('', ['0.0.0.0/0']);
    }

    /**
     * Detects all-address ranges but permits narrow subnets.
     */
    public function test_has_unrestricted_network(): void {
        $this->assertFalse(troubleshooting_helper::has_unrestricted_network("192.0.2.10\n198.51.100.0/24"));
        $this->assertTrue(troubleshooting_helper::has_unrestricted_network("192.0.2.10\n0.0.0.0/0"));
        $this->assertTrue(troubleshooting_helper::has_unrestricted_network('::/0'));
        $this->assertTrue(troubleshooting_helper::has_unrestricted_network('192.0.2.10/0'));
        $this->assertTrue(troubleshooting_helper::has_unrestricted_network('2001:db8::1/0'));
    }

    /**
     * Recognizes when configured CIDR entries cover the official source IPs.
     */
    public function test_get_missing_addresses_respects_cidr_ranges(): void {
        $missing = troubleshooting_helper::get_missing_addresses(
            "18.202.186.0/24\n203.0.113.4",
            ['18.202.186.178', '52.31.215.134', '203.0.113.4']
        );

        $this->assertSame(['52.31.215.134'], $missing);
    }

    /**
     * Treats networks other than official and explicitly registered local-test IPs as additional.
     */
    public function test_get_additional_networks(): void {
        $actual = troubleshooting_helper::get_additional_networks(
            "18.202.186.178\n192.0.2.10\n198.51.100.0/24",
            ['18.202.186.178'],
            ['192.0.2.10']
        );

        $this->assertSame(['198.51.100.0/24'], $actual);
    }

    /**
     * Allowlist correction preserves exact official and explicitly registered local-test IPs.
     */
    public function test_keep_trusted_networks(): void {
        $actual = troubleshooting_helper::keep_trusted_networks(
            "18.202.186.178\n192.0.2.10\n198.51.100.0/24\n192.0.2.10",
            ['18.202.186.178'],
            ['192.0.2.10']
        );

        $this->assertSame("18.202.186.178\n192.0.2.10", $actual);
    }

    /**
     * Tracks local test IPs and removes their exact API allowlist entries on request.
     */
    public function test_local_test_ip_can_be_added_and_removed(): void {
        $this->resetAfterTest(true);
        set_config('api_authorized_networks', '18.202.186.178', 'local_appcrue');

        $this->assertTrue(troubleshooting_helper::add_local_test_ip('192.0.2.10'));
        $this->assertSame(['192.0.2.10'], troubleshooting_helper::get_local_test_ips());
        $this->assertSame("18.202.186.178\n192.0.2.10", get_config('local_appcrue', 'api_authorized_networks'));
        $this->assertTrue(troubleshooting_helper::remove_local_test_ip('192.0.2.10'));
        $this->assertSame([], troubleshooting_helper::get_local_test_ips());
        $this->assertSame('18.202.186.178', get_config('local_appcrue', 'api_authorized_networks'));
    }

    /**
     * Applies exact addresses without removing configured networks or duplicating entries.
     */
    public function test_add_networks_preserves_configuration(): void {
        $this->resetAfterTest(true);
        set_config('api_authorized_networks', "192.0.2.10\n198.51.100.0/24", 'local_appcrue');

        $this->assertTrue(troubleshooting_helper::add_networks(['203.0.113.8', '192.0.2.10']));
        $this->assertSame(
            "192.0.2.10\n198.51.100.0/24\n203.0.113.8",
            get_config('local_appcrue', 'api_authorized_networks')
        );
        $this->assertFalse(troubleshooting_helper::add_networks(['203.0.113.8']));
    }

    /**
     * Selects the highest version published in Moodle's plugin update feed.
     */
    public function test_get_latest_remote_update(): void {
        $updates = [
            new \core\update\info('local_appcrue', ['version' => 2026010100]),
            new \core\update\info('local_appcrue', ['version' => 2026120100]),
            new \core\update\info('local_appcrue', ['version' => 2026050100]),
        ];

        $this->assertSame(2026120100, troubleshooting_helper::get_latest_remote_update($updates)->version);
        $this->assertNull(troubleshooting_helper::get_latest_remote_update(null));
    }
}
