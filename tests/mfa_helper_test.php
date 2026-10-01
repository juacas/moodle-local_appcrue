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
 * Tests for the autologin MFA exclusion.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(mfa_helper::class)]
final class mfa_helper_test extends \advanced_testcase {
    /**
     * Enabling autologin preserves other exclusions and does not add duplicates.
     */
    public function test_enable_preserves_other_exclusions(): void {
        $this->resetAfterTest(true);
        set_config('enable_autologin', 1, 'local_appcrue');
        set_config('redir_exclusions', "/login/example.php\r\n/local/other/login.php", 'tool_mfa');

        mfa_helper::update_autologin_exclusion();
        mfa_helper::update_autologin_exclusion();

        $this->assertSame(
            "/login/example.php\n/local/other/login.php\n/local/appcrue/autologin.php",
            get_config('tool_mfa', 'redir_exclusions')
        );
        $this->assertTrue(mfa_helper::has_autologin_exclusion());
    }

    /**
     * Disabling removes all exact matches, including surrounding whitespace.
     */
    public function test_disable_removes_only_autologin(): void {
        $this->resetAfterTest(true);
        set_config('enable_autologin', 0, 'local_appcrue');
        set_config('redir_exclusions', "/local/appcrue/autologin.php\r\n"
            . " /local/appcrue/autologin.php \r/local/other/login.php\n/local/appcrue/autologin_direct.php", 'tool_mfa');

        mfa_helper::update_autologin_exclusion();

        $this->assertSame(
            "/local/other/login.php\n/local/appcrue/autologin_direct.php",
            get_config('tool_mfa', 'redir_exclusions')
        );
        $this->assertFalse(mfa_helper::has_autologin_exclusion());
    }

    /**
     * Missing exclusions can be enabled and disabled, leaving an empty list.
     */
    public function test_empty_exclusions(): void {
        $this->resetAfterTest(true);
        unset_config('redir_exclusions', 'tool_mfa');
        set_config('enable_autologin', 0, 'local_appcrue');
        mfa_helper::update_autologin_exclusion();
        $this->assertFalse(get_config('tool_mfa', 'redir_exclusions'));

        set_config('enable_autologin', 1, 'local_appcrue');
        mfa_helper::update_autologin_exclusion();
        $this->assertSame('/local/appcrue/autologin.php', get_config('tool_mfa', 'redir_exclusions'));

        set_config('enable_autologin', 0, 'local_appcrue');
        mfa_helper::update_autologin_exclusion();
        $this->assertSame('', get_config('tool_mfa', 'redir_exclusions'));
    }
}
