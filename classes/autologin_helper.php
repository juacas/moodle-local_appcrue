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

namespace local_appcrue;

/**
 * Handle unsuccessful AppCrue autologin attempts.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class autologin_helper {
    /**
     * Validate an explicit redirect destination within this Moodle installation.
     * Fix #11: Prevent open redirect attacks by validating the destination URL.
     * @param string $url Requested destination.
     * @return \moodle_url Validated URL.
     * @throws \moodle_exception If the destination is malformed or outside Moodle.
     */
    public static function get_local_target_url(string $url): \moodle_url {
        if ($url === '' || clean_param($url, PARAM_LOCALURL) !== $url || str_starts_with($url, '//')) {
            throw new \moodle_exception('invalidurl');
        }

        // Resolve relative paths against Moodle's root, independently of the current endpoint.
        if ($url[0] !== '/' && !preg_match('~^https?://~i', $url)) {
            $url = '/' . $url;
        }
        $target = new \moodle_url($url);
        // Browsers normalize backslashes and dot segments before navigating.
        $path = rawurldecode($target->get_path());
        if (preg_match('~[\\\\\x00-\x1f\x7f]|(?:^|/)\.{1,2}(?:/|$)~', $path)) {
            throw new \moodle_exception('invalidurl');
        }
        try {
            $target->out_as_local_url(false);
        } catch (\coding_exception $e) {
            throw new \moodle_exception('invalidurl');
        }
        return $target;
    }

    /**
     * Build a redirect script with the destination encoded as JavaScript data.
     * Fix #11: Prevent open redirect attacks by validating the destination URL and encoding it as JSON.
     * @param \moodle_url $url Destination, including URLs from administrator-defined patterns.
     * @return string Script safe to embed in an HTML script element.
     */
    public static function get_redirect_script(\moodle_url $url): string {
        $destination = json_encode($url->out(false), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        return "setTimeout(function() { window.location.href = {$destination}; }, 100);";
    }

    /**
     * Apply the configured fallback after token validation fails.
     *
     * @param string $fallback Configured fallback mode. Only 'continue' and 'logout' are valid.
     * Other values are treated as 'error'.
     * @param string $token Submitted token.
     * @param \stdClass $diagnostic Token validation diagnostic.
     * @param callable|null $completeguestlogin Optional guest-login callback, available only in PHPUnit tests.
     * @return bool Whether the endpoint should continue to the target URL.
     */
    public static function handle_failed_login(
        string $fallback,
        string $token,
        \stdClass $diagnostic,
        ?callable $completeguestlogin = null
    ): bool {
        if ($completeguestlogin !== null && (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST)) {
            throw new \coding_exception('The guest-login callback is only available during PHPUnit tests.');
        }

        $diagnosis = $diagnostic->result ?? '';

        if ($fallback !== 'continue' && $fallback !== 'logout') {
            self::log_failure($token, $diagnosis);
            return false;
        }

        if ($fallback === 'logout') {
            require_logout();
        } else {
            global $USER;

            if (!isset($USER->id) || $USER->id == 0) {
                $guest = \core_user::get_user_by_username('guest');
                if ($completeguestlogin) {
                    $completeguestlogin($guest);
                } else {
                    complete_user_login($guest);
                    \core\session\manager::apply_concurrent_login_limit($guest->id, session_id());
                }
            }
            $diagnosis .= ' Continuing as guest or current user.';
        }

        self::log_failure($token, $diagnosis);
        return true;
    }

    /**
     * Log an unsuccessful token validation.
     *
     * @param string $token Submitted token.
     * @param string $diagnosis Failure explanation.
     */
    private static function log_failure(string $token, string $diagnosis): void {
        \local_appcrue\event\autologin_failed::create([
            'other' => [
                'ipaddress' => network_security_helper::getremoteaddr(),
                'token' => $token,
                'diagnosis' => $diagnosis,
            ],
        ])->trigger();
    }
}
