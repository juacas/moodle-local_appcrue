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
 * AppCrue services plugin version information.
 *
 * @package local_appcrue
 * @category admin
 * @author  Juan Pablo de Castro
 * @copyright 2021 onwards juanpablo.decastro@uva.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Checks the token and gets the user associated with it.
 * @param strign $token authorization token given to AppCrue by the University IDP. Usually an OAuth2 token.
 */
function appcrue_get_user($token) {
    global $DB, $CFG;
    // The idp service for checking the token i.e. 'https://idp.uva.es/adas/usertoken'.
    $idpurl = $CFG->local_appcrue_idp_url;
    // TODO: make request to IDP to get de username.
    $username = 'testuva2';
    // Get user.
    $user = $DB->get_record('user', array('username' => $username), '*', MUST_EXIST);
    return $user;
}
function appcrue_get_event_type($event) {
    switch ($event->modulename) {
        case 'quiz':
            return 'EXAMEN';
            break;
        case 'assign':
            return 'EXAMEN';
            break;
        default:
            return 'HORARIO';
            break;
    }
}