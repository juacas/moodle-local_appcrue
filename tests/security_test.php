<?php
// phpcs:disable moodle.PHPUnit.TestCaseCovers.Missing -- Use attributes for PHPUnit 11 compatibility.
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
require_once(__DIR__ . '/appcrue_test_base.php');

/**
 * Regression tests for issue #11: API-key HTML injection and autologin destinations.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(autologin_helper::class)]
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_get_apikey_param')]
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_get_target_url')]
final class security_test extends appcrue_test_base {
    /**
     * Both credential sources preserve supported characters and parameter precedence.
     */
    public function test_apikey_preserves_valid_credentials(): void {
        $_SERVER['HTTP_X_API_KEY'] = 'Header-Key_123';
        $this->set_request_parameters([]);
        $this->assertSame('Header-Key_123', local_appcrue_get_apikey_param(true));

        $this->set_request_parameters(['apikey' => 'Parameter-Key_456']);
        $this->assertSame('Parameter-Key_456', local_appcrue_get_apikey_param(true));
    }

    /**
     * Malformed credentials cannot be normalized into a valid key or stored as failed attempts.
     */
    public function test_apikey_rejects_malicious_parameters_and_headers(): void {
        set_config('api_key', 'safeKey', 'local_appcrue');
        set_config('api_key_attempt', 'previousattempt', 'local_appcrue');
        $payloads = [
            '<img src=x onerror=alert(1)>',
            'safe<Key>',
            'safe Key',
            "safeKey\r\nX-Test: injected",
            'safeKey%00',
        ];
        foreach ($payloads as $payload) {
            foreach (['parameter', 'header'] as $source) {
                $this->set_request_parameters($source === 'parameter' ? ['apikey' => $payload] : []);
                $_SERVER['HTTP_X_API_KEY'] = $source === 'header' ? $payload : 'safeKey';
                try {
                    local_appcrue_get_user_from_request();
                    $this->fail('Malformed ' . $source . ' credential was accepted.');
                } catch (\Exception $e) {
                    $this->assertSame(appcrue_service::INVALID_API_KEY, $e->getCode());
                }
                $this->assertSame('previousattempt', get_config('local_appcrue', 'api_key_attempt'));
                $this->assertSame('safeKey', get_config('local_appcrue', 'api_key'));
            }
        }
    }

    /**
     * Missing credentials remain optional unless the caller requires an API key.
     */
    public function test_apikey_missing_value(): void {
        $this->set_request_parameters([]);
        unset($_SERVER['HTTP_X_API_KEY']);
        $this->assertSame('', local_appcrue_get_apikey_param());
        $this->expectExceptionCode(appcrue_service::INVALID_API_KEY);
        local_appcrue_get_apikey_param(true);
    }

    /**
     * Previously stored malicious keys are displayed as text in the actual settings description.
     */
    public function test_settings_escape_previously_stored_apikey(): void {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');
        $this->setAdminUser();
        $payload = '<img src=x onerror=alert(1)><script>alert(2)</script>';
        set_config('api_key_attempt', $payload, 'local_appcrue');
        $ADMIN = new \admin_root(true); // phpcs:ignore moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
        $ADMIN->add('root', new \admin_category('localplugins', 'Local plugins'));
        $hassiteconfig = true;
        require($CFG->dirroot . '/local/appcrue/settings.php');

        $description = $settings->settings->local_appcrueapi_key->description;
        $html = markdown_to_html($description);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringContainsString(s($payload), $html);
        \core\notification::fetch();
    }

    /**
     * Local destinations retain query parameters and fragments under a Moodle subdirectory.
     */
    public function test_urltogo_accepts_local_destinations(): void {
        global $CFG;
        $CFG->wwwroot = 'https://moodle.example.test/campus';
        foreach (['/course/view.php?id=7&section=2#topic-2', 'course/view.php?id=7&section=2#topic-2',
            $CFG->wwwroot . '/course/view.php?id=7&section=2#topic-2'] as $input) {
            $target = local_appcrue_get_target_url('', $input, null, null, null, null, null, null, null);
            $this->assertSame($CFG->wwwroot . '/course/view.php?id=7&section=2#topic-2', $target->out(false));
        }
    }

    /**
     * Explicit destinations cannot leave Moodle or use browser URL normalization tricks.
     */
    public function test_urltogo_rejects_unsafe_destinations(): void {
        global $CFG;
        $CFG->wwwroot = 'https://moodle.example.test/campus';
        $inputs = [
            '', 'https://other.example.test/', '//other.example.test/', 'javascript:alert(1)',
            'data:text/html,test', 'https://moodle.example.test.evil.test/campus/',
            'https://moodle.example.test@other.example.test/campus/',
            'https://moodle.example.test/campus-other/', 'https://moodle.example.test:8443/campus/',
            "https://moodle.example.test/campus/\r\n", '/\\other.example.test/',
            '/%5cother.example.test/', '/../outside', '/%2e%2e/outside', '/.%2e/outside',
        ];
        foreach ($inputs as $input) {
            try {
                local_appcrue_get_target_url('', $input, null, null, null, null, null, null, null);
                $this->fail('Unsafe URL was accepted: ' . $input);
            } catch (\moodle_exception $e) {
                $this->assertSame('invalidurl', $e->errorcode);
            }
        }
    }

    /**
     * Quotes, HTML delimiters and ampersands remain data inside the redirect script.
     */
    public function test_redirect_script_encodes_destination(): void {
        $targets = [
            autologin_helper::get_local_target_url("/my/'-alert(1)-'"),
            new \moodle_url('/my/</script><script>alert(1)</script>'),
            new \moodle_url('/course/view.php', ['id' => 7, 'name' => "O'Reilly"]),
        ];
        foreach ($targets as $target) {
            $script = autologin_helper::get_redirect_script($target);
            $this->assertStringNotContainsString('</script', $script);
            $this->assertStringNotContainsString("'", $script);
            $this->assertStringNotContainsString('&amp;', $script);
            $this->assertSame(1, preg_match('/^setTimeout\(function\(\) \{ window.location.href = (.+); \}, 100\);$/',
                $script, $matches));
            $this->assertSame($target->out(false), json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR));
        }
    }
}
