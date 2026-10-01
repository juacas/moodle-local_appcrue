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
 * Manage the MFA redirect exclusion for AppCRUE autologin.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mfa_helper {
    /** @var string Autologin entry point allowed to validate its token before MFA. */
    private const AUTOLOGIN_URL = '/local/appcrue/autologin.php';

    /**
     * Whether the autologin entry point is already excluded.
     *
     * @return bool
     */
    public static function has_autologin_exclusion(): bool {
        $urls = preg_split('/\r\n|\r|\n/', (string) get_config('tool_mfa', 'redir_exclusions'));
        return in_array(self::AUTOLOGIN_URL, array_map('trim', $urls), true);
    }

    /**
     * Update the exclusion after the autologin setting changes, preserving other URLs.
     */
    public static function update_autologin_exclusion(): void {
        $enabled = (bool) get_config('local_appcrue', 'enable_autologin');
        if ($enabled === self::has_autologin_exclusion()) {
            return;
        }

        $urls = preg_split('/\r\n|\r|\n/', (string) get_config('tool_mfa', 'redir_exclusions'), -1, PREG_SPLIT_NO_EMPTY);
        if ($enabled) {
            $urls[] = self::AUTOLOGIN_URL;
        } else {
            $urls = array_filter($urls, static function (string $url): bool {
                return trim($url) !== self::AUTOLOGIN_URL;
            });
        }
        set_config('redir_exclusions', implode("\n", $urls), 'tool_mfa');
    }
}
