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
 * Class appcrue_service
 *
 * @package    local_appcrue
 * @copyright  2025 Juan Pablo de Castro <juan.pablo.de.castro@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class appcrue_service {
// phpcs:disable moodle.Commenting.MissingDocblock.Constant

    /**
         * Error codes for the AppCrue services.
     */

    const INVALID_API_KEY = 1;
    const MISSING_WS_TOKEN = 2;
    const USER_NOT_ENROLLED = 3;
    const JSON_DECODE_ERROR = 4;
    const INVALID_PARAMETER = 5;

// phpcs:enable moodle.Commenting.MissingDocblock.Constant

    /**
     * @var \stdClass $user User object
     */
    public $user = null;
    /**
     * @var string Diagnostic information
     */
    public $diag = null;
    /**
     * Token passed in request. Optional. If present it may be used in autologin deep linking.
     */
    public $token = null;
    /**
     * Token mark to use in the deep URLs: "token" tells the app to use the token in the URL as a query parameter.
     * "bearer" tells the app to use the token in the Authorization header. null means no deep URLs are used.
     */
    public $tokenmark = null;
    /**
     * constructor.
     */
    public function __construct() {
        $this->tokenmark = get_config('local_appcrue', 'deep_url_token_mark');

        $this->identify_from_request();
        // Configure the service based on the request parameters.
        $this->configure_from_request();
    }
    /**
     * Get items for the service.
     * @return array Array of items for the user.
     */
    public function get_items() {
        throw new \Exception('Method get_items not implemented in ' . __CLASS__);
    }
    /**
     * Read parameters from the request and configure them.
     */
    public function configure_from_request() {
    }
    /**
     * Identify the service from the request.
     * This method should be called in the constructor to set up the service.
     * It reads apikey, token and userid from the request and configures the service.
     */
    public function identify_from_request() {
        // Read parameters from the request and configure the service.
        [$user, $diag, $token] = appcrue_get_user_from_request();
        // Config user context. Calendar API does not need impersonation.
        appcrue_config_user($user, true);
        $this->user = $user;
        $this->diag = $diag;
        $this->token = $token;
    }
    /**
     * Get the endpoint implementation from the slash parameters.
     * @throws \moodle_exception
     * @return object the endpoint implementation.
     */
    public static function instance_from_request(): object {
        // Get endpoint from slash parameter using Moodle's URL handling.
        // Get pathinfo.
        $pathinfo = get_file_argument();
        // Parse slash-separated parameters.
        $pathparts = array_filter(explode('/', trim($pathinfo, '/')));
        $endpoint = isset($pathparts[0]) ? clean_param($pathparts[0], PARAM_ALPHA) : '';
        if ($endpoint == '') {
            throw new \Exception('Endpoint parameter is required', self::INVALID_PARAMETER);
        }
         // Get method get_items from local_appcrue\grades{$endpoint} class.
        $endpointclass = "local_appcrue\\{$endpoint}_service";
        if (!class_exists($endpointclass)) {
            throw new \Exception("Service class {$endpointclass} does not exist", self::INVALID_PARAMETER);
        }
        if (!method_exists($endpointclass, 'get_items')) {
            throw new \Exception("Method get_items does not exist in class {$endpointclass}", self::INVALID_PARAMETER);
        }
        return new $endpointclass();
    }
}
