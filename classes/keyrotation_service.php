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
 * Class keyrotation_service
 *
 * @package    local_appcrue
 * @copyright  2025 Juan Pablo de Castro <juan.pablo.de.castro@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class keyrotation_service extends \local_appcrue\appcrue_service {
    /** @var string APIKey to update to. */
    private string $newapikey;
    /** @var string $oldapikey Previous APIKey */
    private string $oldapikey;

    /**
     * Configure the service from HTTP request parameters.
     *
     * Reads the new API key from the request and validates it.
     *
     * @throws \exception If API key rotation is disabled or the provided API key is invalid.
     * @return void
     */
    public function configure_from_request() {
        global $CFG, $DB;

        // Re-Check if the API key rotation is enabled.
        if (!get_config('local_appcrue', 'enable_api_rotation')) {
            throw new \exception('API key rotation is not enabled.');
        }
        // Read the new API key from the request.
        $this->newapikey = optional_param('newapikey', "", PARAM_ALPHANUMEXT);
        if (!$this->newapikey) {
            throw new \exception('New API key is required.', self::INVALID_PARAMETER);
        }
        // Ensure API Key is different.
        if ($this->newapikey === $this->oldapikey) {
            throw new \exception('New API key must be different from the old API key.', self::INVALID_PARAMETER);
        }
    }
    /**
     * Accepts only API Key authorization.
     * @return void
     */
    public function identify_from_request() {
        $this->oldapikey = optional_param('apikey', "", PARAM_ALPHANUMEXT);
        if (!$this->oldapikey || ! local_appcrue_is_apikey_valid($this->oldapikey)) {
            throw new \exception('Invalid API key provided.', self::INVALID_API_KEY);
        }
    }
    /**
     * Updates the API key in the configuration.
     */
    public function get_data_response() {
        self::rotate_key($this->oldapikey, $this->newapikey);
        $response = [
            'success' => true,
            'message' => 'API key updated successfully.',
            'old_api_key' => $this->oldapikey,
            'new_api_key' => $this->newapikey,
        ];
        return [$response, 1];
    }
    /**
     * Store a new apikey and report rotation time.
     * @param string $oldapikey
     * @param string $newapikey
     * @return void
     */
    public static function rotate_key(string $oldapikey, string $newapikey): void {
        // Store new API key.
        set_config('api_key', $newapikey, 'local_appcrue');
        // Record the rotation time.
        set_config('api_key_last_rotation', time(), 'local_appcrue');
        debugging("API key updated from {$oldapikey} to {$newapikey}", DEBUG_NORMAL);
    }
}
