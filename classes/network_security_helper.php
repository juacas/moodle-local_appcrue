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

use core\files\curl_security_helper;

/**
 * Class network_security_helper.
 * Overrides curl_security_helper to get the addresses from local_appcrue settings.
 * Uses the former logic to check whether a client is in the list.
 *
 * @package    local_appcrue
 * @copyright  2025 Juan Pablo de Castro <juan.pablo.de.castro@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class network_security_helper extends curl_security_helper {
    /**
     * Check if caller is in the list.
     */
    public function is_request_in_list() {
        // Get request remote address.
        $remoteaddr = $_SERVER['REMOTE_ADDR'] ?? '';
        $isinlist = $this->address_explicitly_blocked($remoteaddr);
        return $isinlist;
    }
    /**
     * Overrides to return the configured hosts, as defined in the 'api_authorized_networks' setting.
     * Note: curl_security_helper was designed to block hosts. We use it just to check the list.
     * @return array the array of host/networks entries.
     */
    protected function get_blocked_hosts() {
        $hosts = get_config('local_appcrue', 'api_authorized_networks');
        if (empty($hosts)) {
            return [];
        }
        return array_filter(
            array_map('trim', explode("\n", $hosts)),
            function ($entry) {
                return !empty($entry);
            }
        );
    }
}
