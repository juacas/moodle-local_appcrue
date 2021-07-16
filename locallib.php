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
    // The idp service for checking the token i.e. 'https://idp.uva.es/api/adas/oauth2/tokendata'.
    $idpurl = get_config('local_appcrue', 'idp_token_url');
    $idpurl = $CFG->local_appcrue_idp_url;
    $token = appcrue_get_idp_token();
    // Make a request to obtain a user name.
    // TODO: make request to IDP to get de username.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $$idpurl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_USERPWD, "Bearer: $token");
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
    curl_close($ch);
    $username = json_decode($result)['USUARIO_MOODLE'];
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
function appcrue_get_new_idp_token() {
    // Mockup pseudocode.
    $idpgettokenurl = get_config('local_appcrue', 'idp_token_url');
    $idpid = get_config('local_appcrue', 'idp_client_id');
    $idpsecret = get_config('local_appcrue', 'idp_client_secret');
    $username = base64_encode("$idpid:$idpsecret");
    $password = '';
    // Make a request to obtain a new token.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $idpgettokenurl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
    curl_close($ch);
    // Expected return:
    //     {
    // "token_type": "Bearer",
    // "access_token": "f57e1777-a199-416e-917b-f3ea8e93f5be",
    // "expires_in": 1627040493
    // }
    $token = json_decode($result);
    set_config('local_appcrue',"idp_last_token", $result);

    return $token;
}
function appcrue_get_idp_token() {
    $token = json_decode(get_config('local_appcrue',"idp_last_token"));
    if ($token && $token->expires_in < time() ) {
        return $token->access_token;
    } else {
        return appcrue_get_new_idp_token();
    }
}