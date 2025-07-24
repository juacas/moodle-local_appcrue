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

/**
 * Proxy service for calendar events with enhanced error handling.
 *
 * @package   local_appcrue
 * @author    Alberto Otero Mato
 * @copyright 2025 alberto.otero@altia.es
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.RequireLogin.Missing

require_once('../../config.php');
require_once('locallib.php');

if (!get_config('local_appcrue', 'lmsappcrue_enable_calendar')) {
    @header('HTTP/1.1 404 Not Found');
    die();
    // Better act as a service don't throw new moodle_exception('servicedonotexist', 'error').
}

// No requiere login ya que usaremos un apikey interna.
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
// Disable context in formatting.
$PAGE->set_context(null);
try {
    $fileservice = new local_appcrue\files_service();
    $files = $fileservice->get_items();
    echo json_encode($files, JSON_HEX_QUOT | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    appcrue_send_error_response($e, debugging());
}
