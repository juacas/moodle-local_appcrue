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
 * Safe configuration helpers used by the administrator troubleshooting page.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class troubleshooting_helper {
    /**
     * Parse a datetime-local value in the administrator's Moodle timezone.
     *
     * @param string $value HTML datetime-local value.
     * @return int|null Unix timestamp, or null for an invalid value.
     */
    public static function parse_log_since(string $value): ?int {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value)) {
            return null;
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value, \core_date::get_user_timezone_object());
        if ($date === false || $date->format('Y-m-d\TH:i') !== $value) {
            return null;
        }
        return $date->getTimestamp();
    }

    /**
     * Probe the configured Universia IdP using a random invalid user token.
     * Only the HTTP and cURL status and elapsed time are returned; credentials and response bodies remain private.
     *
     * @return array HTTP status, cURL error number and elapsed seconds.
     */
    public static function probe_universia_idp(): array {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->dirroot . '/local/appcrue/locallib.php');
        $curl = new \curl();
        $curl->setHeader(['Content-Type: application/json', 'Accept: application/json']);
        $started = microtime(true);
        try {
            $curl->post(
                local_appcrue_get_universia_idp_url(),
                json_encode([
                    'import_code' => get_config('local_appcrue', 'appcrue_appid'),
                    'token' => get_config('local_appcrue', 'appcrue_apptoken'),
                    'user_token' => 'appcrue-diagnostic-' . bin2hex(random_bytes(24)),
                ]),
                [
                    'CURLOPT_RETURNTRANSFER' => true,
                    'CURLOPT_CONNECTTIMEOUT' => 5,
                    'CURLOPT_TIMEOUT' => 15,
                    'CURLOPT_FOLLOWLOCATION' => false,
                    'CURLOPT_HTTPAUTH' => CURLAUTH_ANY,
                ]
            );
            return [
                'httpcode' => (int)($curl->get_info()['http_code'] ?? 0),
                'curlerror' => (int)$curl->get_errno(),
                'duration' => max(0, microtime(true) - $started),
            ];
        } catch (\Throwable $e) {
            return [
                'httpcode' => 0,
                'curlerror' => (int)$curl->get_errno(),
                'duration' => max(0, microtime(true) - $started),
            ];
        }
    }

    /**
     * Moodle event names included in the administrator troubleshooting report.
     *
     * @return array
     */
    public static function get_reported_event_names(): array {
        return [
            '\\local_appcrue\\event\\api_accessed',
            '\\local_appcrue\\event\\bad_apikey_failed',
            '\\local_appcrue\\event\\unauthorized_ip_failed',
            '\\local_appcrue\\event\\autologin_login',
            '\\local_appcrue\\event\\autologin_failed',
        ];
    }

    /**
     * Return the timestamp of the most recent logged IdP response with a given status.
     * The raw event payload can contain login tokens, so only the timestamp is returned.
     *
     * @param int $statuscode HTTP status returned by the IdP.
     * @param int $since Unix timestamp lower bound.
     * @return int|false|null Timestamp, false when not found, or null when the log table is unavailable.
     */
    public static function get_recent_idp_status(int $statuscode, int $since): int|false|null {
        global $DB;

        $table = new \xmldb_table('logstore_standard_log');
        if (!$DB->get_manager()->table_exists($table)) {
            return null;
        }

        $records = $DB->get_records_select(
            'logstore_standard_log',
            'component = :component AND eventname = :eventname AND timecreated >= :since',
            [
                'component' => 'local_appcrue',
                'eventname' => '\\local_appcrue\\event\\autologin_failed',
                'since' => $since,
            ],
            'timecreated DESC, id DESC',
            'id, timecreated, other',
            0,
            200
        );

        foreach ($records as $record) {
            $other = json_decode($record->other ?? '', true);
            $diagnosis = is_array($other) ? (string)($other['diagnosis'] ?? '') : '';
            if (preg_match('/IDP returned status code:\\s*' . preg_quote((string)$statuscode, '/') . '\\b/i', $diagnosis)) {
                return (int)$record->timecreated;
            }
        }
        return false;
    }

    /**
     * Return IP-related request values and the Moodle selection settings.
     *
     * @return array
     */
    public static function get_request_ip_diagnostics(): array {
        global $CFG;

        $selection = isset($CFG->getremoteaddrconf) ? (int)$CFG->getremoteaddrconf : GETREMOTEADDR_SKIP_DEFAULT;
        $selectionlabels = [
            0 => 'HTTP_CLIENT_IP → HTTP_X_FORWARDED_FOR → REMOTE_ADDR',
            GETREMOTEADDR_SKIP_HTTP_CLIENT_IP => 'HTTP_X_FORWARDED_FOR → REMOTE_ADDR',
            GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR => 'HTTP_CLIENT_IP → REMOTE_ADDR',
            GETREMOTEADDR_SKIP_HTTP_CLIENT_IP | GETREMOTEADDR_SKIP_HTTP_X_FORWARDED_FOR => 'REMOTE_ADDR only',
        ];

        return [
            'effective' => \getremoteaddr(),
            'remoteaddr' => (string)($_SERVER['REMOTE_ADDR'] ?? ''),
            'clientip' => substr((string)($_SERVER['HTTP_CLIENT_IP'] ?? ''), 0, 512),
            'forwardedfor' => substr((string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''), 0, 512),
            'selection' => $selectionlabels[$selection] ?? (string)$selection,
            'reverseproxyignore' => (string)($CFG->reverseproxyignore ?? ''),
        ];
    }

    /**
     * Return recent AppCRUE events without their descriptions or other payload.
     * Existing failed-authentication events can contain API keys and login tokens.
     *
     * @param int $since Unix timestamp lower bound.
     * @param int $limit Maximum number of records.
     * @return array|null Null when the standard log table is unavailable.
     */
    public static function get_recent_log_events(int $since, int $limit = 50): ?array {
        global $DB;

        $table = new \xmldb_table('logstore_standard_log');
        if (!$DB->get_manager()->table_exists($table)) {
            return null;
        }

        $eventnames = self::get_reported_event_names();
        [$insql, $params] = $DB->get_in_or_equal($eventnames, SQL_PARAMS_NAMED, 'event');
        $params['component'] = 'local_appcrue';
        $params['since'] = $since;
        $records = $DB->get_records_select(
            'logstore_standard_log',
            "component = :component AND eventname {$insql} AND timecreated >= :since",
            $params,
            'timecreated DESC, id DESC',
            'id, eventname, timecreated, userid, ip, other',
            0,
            min(max($limit, 1), 200)
        );
        $safeevents = [];
        $knownendpoints = ['calendar', 'forums', 'grades', 'announcements', 'files', 'assignments'];
        foreach ($records as $record) {
            $endpoint = '';
            if ($record->eventname === '\\local_appcrue\\event\\api_accessed') {
                $other = json_decode($record->other ?? '', true);
                if (is_array($other) && in_array($other['endpoint'] ?? '', $knownendpoints, true)) {
                    $endpoint = $other['endpoint'];
                }
            }
            $safeevents[] = (object)[
                'id' => $record->id,
                'eventname' => $record->eventname,
                'timecreated' => $record->timecreated,
                'userid' => $record->userid,
                'ip' => $record->ip,
                'endpoint' => $endpoint,
            ];
        }
        return $safeevents;
    }

    /**
     * Return counts and distinct actors/IPs for recent AppCRUE activity.
     *
     * @param int $since Unix timestamp lower bound.
     * @param int $listlimit Maximum number of distinct users or addresses to return.
     * @return array|null Null when the standard log table is unavailable.
     */
    public static function get_recent_log_summary(int $since, int $listlimit = 50): ?array {
        global $DB;

        $table = new \xmldb_table('logstore_standard_log');
        if (!$DB->get_manager()->table_exists($table)) {
            return null;
        }

        $eventnames = self::get_reported_event_names();
        [$insql, $params] = $DB->get_in_or_equal($eventnames, SQL_PARAMS_NAMED, 'event');
        $params['component'] = 'local_appcrue';
        $params['since'] = $since;
        $where = "component = :component AND eventname {$insql} AND timecreated >= :since";
        $counts = $DB->get_records_sql(
            "SELECT eventname, COUNT(*) AS total, MAX(timecreated) AS lasttimecreated " .
                "FROM {logstore_standard_log} WHERE {$where} GROUP BY eventname",
            $params
        );
        $autologinevent = '\\local_appcrue\\event\\autologin_login';
        $lastsuccessfulautologin = isset($counts[$autologinevent])
            ? (int)$counts[$autologinevent]->lasttimecreated
            : null;

        $limit = min(max($listlimit, 1), 200);
        $activities = [];
        foreach ($eventnames as $eventname) {
            $activities[$eventname] = [
                'users' => ['values' => [], 'truncated' => false],
                'ips' => ['values' => [], 'truncated' => false],
            ];
        }
        $userrecords = $DB->get_recordset_sql(
            "SELECT eventname, userid FROM {logstore_standard_log} WHERE {$where} AND userid > 0 " .
                "GROUP BY eventname, userid ORDER BY eventname, userid",
            $params
        );
        foreach ($userrecords as $record) {
            $activity =& $activities[$record->eventname];
            if (count($activity['users']['values']) < $limit) {
                $activity['users']['values'][] = (int)$record->userid;
            } else {
                $activity['users']['truncated'] = true;
            }
            unset($activity);
        }
        $userrecords->close();

        $iprecords = $DB->get_recordset_sql(
            "SELECT eventname, ip FROM {logstore_standard_log} WHERE {$where} AND ip <> '' " .
                "GROUP BY eventname, ip ORDER BY eventname, ip",
            $params
        );
        foreach ($iprecords as $record) {
            $activity =& $activities[$record->eventname];
            if (count($activity['ips']['values']) < $limit) {
                $activity['ips']['values'][] = (string)$record->ip;
            } else {
                $activity['ips']['truncated'] = true;
            }
            unset($activity);
        }
        $iprecords->close();
        [$officialsql, $officialparams] = $DB->get_in_or_equal(
            \local_appcrue\appcrue_service::APPCRUE_SERVERS,
            SQL_PARAMS_NAMED,
            'officialip'
        );
        $officialparams['component'] = 'local_appcrue';
        $officialparams['badkeyevent'] = '\\local_appcrue\\event\\bad_apikey_failed';
        $officialparams['since'] = $since;
        $badkeyofficialtotal = $DB->count_records_select(
            'logstore_standard_log',
            "component = :component AND eventname = :badkeyevent AND timecreated >= :since AND ip {$officialsql}",
            $officialparams
        );
        $badkeyofficialips = $DB->get_records_sql(
            "SELECT ip, COUNT(*) AS total FROM {logstore_standard_log} WHERE component = :component " .
                "AND eventname = :badkeyevent AND timecreated >= :since AND ip {$officialsql} GROUP BY ip",
            $officialparams
        );

        return [
            'counts' => array_map(static function ($record): int {
                return (int)$record->total;
            }, $counts),
            'lastsuccessfulautologin' => $lastsuccessfulautologin,
            'activities' => $activities,
            'badkeyofficialtotal' => $badkeyofficialtotal,
            'badkeyofficialips' => array_values($badkeyofficialips),
        ];
    }

    /**
     * Return whitelist entries other than official AppCRUE IPs and explicitly trusted local-test IPs.
     *
     * @param string $networks Newline-separated IP/CIDR entries.
     * @param array $officialips Exact official AppCRUE IPs.
     * @param array $localtestips Exact IPs explicitly added for local administrator tests.
     * @return array
     */
    public static function get_additional_networks(
        string $networks,
        array $officialips,
        array $localtestips = []
    ): array {
        $entries = preg_split('/\\r\\n|\\r|\\n/', $networks, -1, PREG_SPLIT_NO_EMPTY);
        $trusted = array_merge($officialips, $localtestips);
        return array_values(array_diff(array_map('trim', $entries), $trusted));
    }

    /**
     * Keep exact official AppCRUE addresses and explicitly trusted local-test IPs.
     *
     * @param string $networks Current newline-separated list.
     * @param array $officialips Exact official AppCRUE IPs.
     * @param array $localtestips Exact IPs explicitly added for local administrator tests.
     * @return string Cleaned newline-separated list.
     */
    public static function keep_trusted_networks(
        string $networks,
        array $officialips,
        array $localtestips = []
    ): string {
        $entries = preg_split('/\\r\\n|\\r|\\n/', $networks, -1, PREG_SPLIT_NO_EMPTY);
        $configured = array_map('trim', $entries);
        $trusted = array_merge($officialips, $localtestips);
        return implode("\n", array_values(array_unique(array_intersect($configured, $trusted))));
    }

    /**
     * Return exact IPs explicitly allowed for administrator browser tests.
     *
     * @return array
     */
    public static function get_local_test_ips(): array {
        $configured = (string)get_config('local_appcrue', 'api_authorized_local_test_ips');
        $entries = preg_split('/\\r\\n|\\r|\\n/', $configured, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_unique(array_filter(array_map('trim', $entries), static function (string $ip): bool {
            return (bool)filter_var($ip, FILTER_VALIDATE_IP);
        })));
    }

    /**
     * Add one exact IP to both the API allowlist and the tracked local-test IP list.
     *
     * @param string $ip Exact IPv4 or IPv6 address.
     * @return bool Whether configuration changed.
     */
    public static function add_local_test_ip(string $ip): bool {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException('Invalid IP address.');
        }
        $ipchanged = self::add_networks([$ip]);
        $current = (string)get_config('local_appcrue', 'api_authorized_local_test_ips');
        $updated = self::merge_networks($current, [$ip]);
        if ($updated !== $current) {
            set_config('api_authorized_local_test_ips', $updated, 'local_appcrue');
            return true;
        }
        return $ipchanged;
    }

    /**
     * Remove one explicitly trusted local-test IP from tracking and the exact allowlist entry.
     *
     * @param string $ip Exact IPv4 or IPv6 address.
     * @return bool Whether configuration changed.
     */
    public static function remove_local_test_ip(string $ip): bool {
        if (!filter_var($ip, FILTER_VALIDATE_IP) || !in_array($ip, self::get_local_test_ips(), true)) {
            return false;
        }
        $localtestips = array_values(array_diff(self::get_local_test_ips(), [$ip]));
        set_config('api_authorized_local_test_ips', implode("\n", $localtestips), 'local_appcrue');
        if (!in_array($ip, \local_appcrue\appcrue_service::APPCRUE_SERVERS, true)) {
            $networkconfig = (string)get_config('local_appcrue', 'api_authorized_networks');
            $entries = preg_split('/\\r\\n|\\r|\\n/', $networkconfig, -1, PREG_SPLIT_NO_EMPTY);
            $entries = array_values(array_diff(array_map('trim', $entries), [$ip]));
            set_config('api_authorized_networks', implode("\n", $entries), 'local_appcrue');
        }
        return true;
    }

    /**
     * Return the highest published version reported by Moodle's update checker.
     *
     * @param array|null $updates Published updates for one plugin.
     * @return \core\update\info|null
     */
    public static function get_latest_remote_update(?array $updates): ?\core\update\info {
        if (empty($updates)) {
            return null;
        }

        usort($updates, static function (\core\update\info $left, \core\update\info $right): int {
            return (int)$right->version <=> (int)$left->version;
        });
        return $updates[0];
    }

    /**
     * Merge exact IP addresses into the configured network list without removing existing entries.
     *
     * @param string $current Current newline-separated entries.
     * @param array $additions Exact IP addresses to append.
     * @return string Updated newline-separated list.
     */
    public static function merge_networks(string $current, array $additions): string {
        $entries = preg_split('/\r\n|\r|\n/', $current, -1, PREG_SPLIT_NO_EMPTY);
        $entries = array_values(array_filter(array_map('trim', $entries)));

        foreach ($additions as $address) {
            if (!is_string($address) || filter_var($address, FILTER_VALIDATE_IP) === false) {
                throw new \InvalidArgumentException('Only exact IPv4 or IPv6 addresses may be added.');
            }
            if (!in_array($address, $entries, true)) {
                $entries[] = $address;
            }
        }

        return implode("\n", $entries);
    }

    /**
     * Return whether a network list contains an all-address IPv4 or IPv6 range.
     *
     * @param string $networks Newline-separated IP addresses and CIDR ranges.
     * @return bool
     */
    public static function has_unrestricted_network(string $networks): bool {
        $entries = preg_split('/\r\n|\r|\n/', $networks, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($entries as $entry) {
            $parts = explode('/', trim($entry), 2);
            if (count($parts) !== 2 || trim($parts[1]) !== '0') {
                continue;
            }
            $packed = @inet_pton(trim($parts[0]));
            if ($packed !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return official AppCRUE addresses not covered by the configured network list.
     *
     * @param string $networks Newline-separated IP addresses and CIDR ranges.
     * @param array $addresses Known AppCRUE source addresses.
     * @return array
     */
    public static function get_missing_addresses(string $networks, array $addresses): array {
        $entries = preg_split('/\r\n|\r|\n/', $networks, -1, PREG_SPLIT_NO_EMPTY);
        $subnets = implode(',', array_map('trim', $entries));
        $missing = [];
        foreach ($addresses as $address) {
            if (!address_in_subnet($address, $subnets, true)) {
                $missing[] = $address;
            }
        }
        return $missing;
    }

    /**
     * Add exact IP addresses to the plugin network list, preserving existing entries.
     *
     * @param array $addresses Exact IPv4 or IPv6 addresses to add.
     * @return bool Whether the setting changed.
     */
    public static function add_networks(array $addresses): bool {
        $current = (string) get_config('local_appcrue', 'api_authorized_networks');
        $updated = self::merge_networks($current, $addresses);
        if ($updated === $current) {
            return false;
        }
        set_config('api_authorized_networks', $updated, 'local_appcrue');
        return true;
    }

    /**
     * Call an enabled endpoint over HTTP as an administrator troubleshooting check.
     * The caller must require the site configuration capability before using this method.
     *
     * @param string $endpoint Endpoint name.
     * @param string $identifier Value used to identify the user.
     * @return array HTTP status and decoded endpoint response or a safe error payload.
     */
    public static function run_endpoint_for_user(string $endpoint, string $identifier): array {
        global $CFG;

        $knownendpoints = ['calendar', 'forums', 'grades', 'announcements', 'files', 'assignments'];
        if (
            !in_array($endpoint, $knownendpoints, true)
            || get_config('local_appcrue', "lmsappcrue_enable_{$endpoint}") !== '1'
        ) {
            return self::endpoint_test_error(get_string('invalidparameter'));
        }

        $lock = null;
        $addedloopback = false;
        $originalnetworks = '';
        $temporarynetworks = '';
        try {
            $apikey = (string)get_config('local_appcrue', 'api_key');
            if ($apikey === '' || clean_param($apikey, PARAM_ALPHANUMEXT) !== $apikey) {
                return self::endpoint_test_error(get_string('troubleshooting:endpointtestnokey', 'local_appcrue'));
            }

            // These HTTP requests originate from Moodle itself, so the web server sees loopback.
            $lockfactory = \core\lock\lock_config::get_lock_factory('local_appcrue_endpoint_test');
            $lock = $lockfactory->get_lock('temporary_loopback_allowlist', 5, 45);
            if (!$lock) {
                return self::endpoint_test_error(get_string('troubleshooting:endpointtestlock', 'local_appcrue'));
            }
            $networks = (string)get_config('local_appcrue', 'api_authorized_networks');
            $entries = array_map('trim', preg_split('/\r\n|\r|\n/', $networks, -1, PREG_SPLIT_NO_EMPTY));
            if (!in_array('127.0.0.1', $entries, true)) {
                $separator = $networks === '' || str_ends_with($networks, "\n") ? '' : "\n";
                $originalnetworks = $networks;
                $temporarynetworks = $networks . $separator . '127.0.0.1';
                set_config('api_authorized_networks', $temporarynetworks, 'local_appcrue');
                $addedloopback = true;
            }

            $userparameter = get_config('local_appcrue', 'lmsappcrue_use_user_param') === 'username'
                ? 'username' : 'studentemail';
            require_once($CFG->libdir . '/filelib.php');
            $url = new \moodle_url(
                '/local/appcrue/appcrue.php/' . $endpoint,
                [$userparameter => $identifier]
            );
            $curl = new \curl();
            $curl->setHeader([
                'Accept: application/json',
                'X-API-KEY: ' . $apikey,
            ]);
            // Do not follow redirects: the API key must only be sent to this Moodle host.
            $requeststart = microtime(true);
            $body = $curl->get($url->out(false), [], [
                'timeout' => 30,
                'connecttimeout' => 10,
                'followlocation' => false,
            ]);
            $responsetime = max(0, microtime(true) - $requeststart);
            $httpcode = (int)($curl->get_info()['http_code'] ?? 0);
            if ($curl->get_errno() !== 0) {
                return self::endpoint_test_error(
                    get_string('troubleshooting:endpointtestcurlerror', 'local_appcrue', $curl->get_errno()),
                    $httpcode,
                    $responsetime
                );
            }

            $decoded = json_decode((string)$body, true);
            $validjson = json_last_error() === JSON_ERROR_NONE;
            if ($httpcode === 403 && trim((string)$body) === '') {
                $decoded = [
                    'error' => [
                        'message' => get_string('troubleshooting:endpointtestforbidden', 'local_appcrue'),
                    ],
                ];
                $validjson = true;
            } else if (!$validjson) {
                $decoded = ['raw_response' => (string)$body];
            }
            return [
                'success' => $httpcode >= 200 && $httpcode < 300 && $validjson,
                'httpcode' => $httpcode,
                'response' => $decoded,
                'responsetime' => $responsetime,
            ];
        } catch (\Throwable $e) {
            return self::endpoint_test_error($e->getMessage(), 0, $responsetime ?? null);
        } finally {
            try {
                if ($addedloopback) {
                    $networks = (string)get_config('local_appcrue', 'api_authorized_networks');
                    if ($networks === $temporarynetworks) {
                        set_config('api_authorized_networks', $originalnetworks, 'local_appcrue');
                    } else {
                        // Preserve any unrelated network changes made during the HTTP request.
                        $entries = preg_split('/\r\n|\r|\n/', $networks, -1, PREG_SPLIT_NO_EMPTY);
                        $entries = array_values(array_filter($entries, static function (string $entry): bool {
                            return trim($entry) !== '127.0.0.1';
                        }));
                        set_config('api_authorized_networks', implode("\n", $entries), 'local_appcrue');
                    }
                }
            } finally {
                if ($lock) {
                    $lock->release();
                }
            }
        }
    }

    /**
     * Summarize item counts and date fields from an endpoint JSON response.
     *
     * @param mixed $response Decoded endpoint JSON response.
     * @return array|null Summary, or null when the response contains no item data.
     */
    public static function summarize_endpoint_response($response): ?array {
        if (!is_array($response)) {
            return null;
        }

        $payload = $response['data'] ?? $response;
        if (isset($response['count']) && is_numeric($response['count'])) {
            $count = (int)$response['count'];
        } else {
            $counts = [];
            self::collect_named_item_counts($payload, $counts);
            if (!empty($counts['events'])) {
                $count = array_sum($counts['events']);
            } else {
                $count = null;
                foreach (['announcements', 'files', 'assignments', 'grades', 'forums', 'data'] as $key) {
                    if (isset($counts[$key])) {
                        $count = array_sum($counts[$key]);
                        break;
                    }
                }
                if ($count === null && array_is_list($payload)) {
                    $count = count($payload);
                }
            }
        }

        $timestamps = [];
        self::collect_endpoint_dates($payload, $timestamps);
        if ($count === null && empty($timestamps)) {
            return null;
        }

        return [
            'count' => $count,
            'from' => $timestamps ? min($timestamps) : null,
            'to' => $timestamps ? max($timestamps) : null,
        ];
    }

    /**
     * Collect counts for recognized arrays of endpoint items.
     *
     * @param mixed $node JSON node.
     * @param array $counts Counts grouped by array name.
     * @return void
     */
    private static function collect_named_item_counts($node, array &$counts): void {
        if (!is_array($node)) {
            return;
        }

        foreach ($node as $key => $value) {
            if (
                is_array($value) && in_array(
                    $key,
                    ['events', 'announcements', 'files', 'assignments', 'grades', 'forums', 'data'],
                    true
                )
            ) {
                $counts[$key][] = count($value);
            }
            self::collect_named_item_counts($value, $counts);
        }
    }

    /**
     * Collect timestamps from fields that represent dates or times in API items.
     *
     * @param mixed $node JSON node.
     * @param array $timestamps Collected Unix timestamps.
     * @param string|null $fieldname Current field name.
     * @return void
     */
    private static function collect_endpoint_dates($node, array &$timestamps, ?string $fieldname = null): void {
        if (!is_array($node)) {
            if ($fieldname !== null && preg_match('/(date|^time|_at$|At$|start$|end$)/', $fieldname)) {
                $timestamp = self::parse_endpoint_date($node);
                if ($timestamp !== null) {
                    $timestamps[] = $timestamp;
                }
            }
            return;
        }

        foreach ($node as $key => $value) {
            if (strtolower((string)$key) === 'timestamp') {
                continue;
            }
            self::collect_endpoint_dates($value, $timestamps, (string)$key);
        }
    }

    /**
     * Convert an API date field value to a Unix timestamp.
     *
     * @param mixed $value Date value.
     * @return int|null
     */
    private static function parse_endpoint_date($value): ?int {
        if (!is_scalar($value) || $value === '' || $value === null) {
            return null;
        }
        $value = trim((string)$value);
        if (preg_match('/^\d{8}$/', $value)) {
            $date = \DateTimeImmutable::createFromFormat('!Ymd', $value);
            return $date ? $date->getTimestamp() : null;
        }
        if (is_numeric($value)) {
            $numeric = (float)$value;
            if ($numeric <= 0) {
                return null;
            }
            if ($numeric >= 100000000000) {
                $numeric /= 1000;
            }
            if ($numeric >= 100000000 && $numeric < 10000000000) {
                return (int)$numeric;
            }
            return null;
        }

        $timestamp = strtotime($value);
        return $timestamp === false ? null : $timestamp;
    }

    /**
     * Wrap a safe endpoint test error in the common response structure.
     *
     * @param string $message Error message.
     * @param int $httpcode HTTP response status, if known.
     * @param float|null $responsetime Request duration in seconds, if a request was made.
     * @return array Error payload.
     */
    private static function endpoint_test_error(string $message, int $httpcode = 0, ?float $responsetime = null): array {
        return [
            'success' => false,
            'httpcode' => $httpcode,
            'responsetime' => $responsetime,
            'response' => [
                'success' => false,
                'error' => ['message' => $message],
            ],
        ];
    }
}
