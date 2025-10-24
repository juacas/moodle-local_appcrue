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

// phpcs:disable moodle.Files.RequireLogin.Missing

/**
 * Handles API requests for the appcrue service in Moodle.
 * This file processes incoming requests, validates the endpoint, retrieves data,
 * and sends a JSON response to the client.
 *
 * @package    local_appcrue
 * @copyright  2025 Juan Pablo de Castro <juan.pablo.de.castro@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_appcrue\appcrue_service;
// Define AJAX_SCRIPT to avoid debug messages in output.
define('AJAX_SCRIPT', true);
// Avoid creating sesions.
define('NO_MOODLE_COOKIES', true);
require_once('../../config.php');
require_once('locallib.php');

// No requiere login ya que usaremos un apikey interna.
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
// Disable context in formatting.
$PAGE->set_context(null);

try {
    // Check autoconfig mode.
    appcrue_service::check_autoconfig_mode();
    // Check network restrictions.
    $networkhelper = new \local_appcrue\network_security_helper();
    if (!$networkhelper->is_request_in_list()) {
        @header('HTTP/1.1 403 Forbidden');
        die();
    }

    $endpoint = appcrue_service::instance_from_request();
    // Check if the endpoint is enabled.
    if (!$endpoint->is_enabled()) {
        @header('HTTP/1.1 404 Not Found');
        die();
    }
    $response = $endpoint->get_response_json();
    // Send the response as JSON.
    echo json_encode($response, JSON_HEX_QUOT | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    appcrue_service::send_error_response($e, debugging());
}
