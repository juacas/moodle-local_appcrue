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
 * @param string $token authorization token given to AppCrue by the University IDP. Usually an OAuth2 token.
 * @return stdClass|null
 */
function appcrue_get_user($token) {
    /** @var moodle_database $DB */
    global $DB;
    $matchvalue = false;
    $tokentype = optional_param('method','JWT_UVa', PARAM_ALPHANUMEXT); // TODO: Quitar todas menos OAUTH (genérico).
    switch ($tokentype) {
        case 'JWT_unsecure':
            $tokendata = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
            $matchvalue = 'e' . strtolower($tokendata->ID->document);
            break;
        case 'JWT_UVa':
            // Validate signature with Midleware at UVa.
            $checktokenurl = get_config('local_appcrue', 'idp_token_url');
            // Currently something like 'https://appcrue-des.uva.es:449/appcrueservices/meID'.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $checktokenurl);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout after 30 seconds.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
            $result = curl_exec($ch);
            $statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);   // Get status code.
            // TODO: Maybe check status code. Probably it's enough with parsing the result.
            curl_close($ch);
            $response = json_decode($result);
            if (isset($response->dni)) {
                $matchvalue = strtolower('e'.json_decode($result)->dni);
            } else {
                $matchvalue = false;
            }
            break;
        case 'OAUTH2': // TODO: dejar como Generic token information.
            global $CFG;
            require_once($CFG->dirroot . '/lib/filelib.php');
            // The idp service for checking the token i.e. 'https://idp.uva.es/api/adas/oauth2/tokendata'.
            $idpurl = get_config('local_appcrue', 'idp_token_url');
            $idpurl = 'https://appcrue-des.uva.es:449/appcrueservices/meID'; // TODO: Debug. Quitar.
            $idpurl = 'https://idpre.uva.es/api/adas/oauth2/tokendata'; // TODO: Debug. Quitar
            $curl = new \curl();
            $options = [
                'CURLOPT_RETURNTRANSFER' => true,
                'CURLOPT_CONNECTTIMEOUT' => 5,
                'CURLOPT_HTTPAUTH' => CURLAUTH_ANY
            ];
            $curl->setHeader(["Authorization: Bearer $token"]);
            $result = $curl->get($idpurl, null, $options);
            $statuscode = $curl->get_info()['http_code'];
            $result = '{"USUARIO_MOODLE": ["e11965920d"]}'; // TODO: Debug. Quitar.
            $statuscode = 200;                              // TODO: Debug. Quitar.

            // Get matchvalue of the token from the idp.
            if ($statuscode == 200) {
                $jsonpath = get_config('local_appcrue', 'idp_user_json_path');
                $matchvalue = appcrue_get_json_node($result, $jsonpath);
            } else {
                debugging("Permission denied for the token: $token", DEBUG_NORMAL);
                $matchvalue = false;
            }
            break;
    }
    // Get user.
    if ($matchvalue == false) {
        $user = null;
    } else {
        $fieldname = get_config('local_appcrue', 'match_user_by');
        // First check in standard fieldnames.
        $fields = get_user_fieldnames();
        if (array_search($fieldname, $fields) !== false) {
            $user = $DB->get_record('user', array($fieldname => $matchvalue), '*');
        } else {
            global $CFG;
            require_once($CFG->dirroot . '/user/profile/lib.php');
            $customfields = profile_get_custom_fields();
            $fieldname = substr($fieldname, 14); // Trim prefix 'profile_field'.
            $fieldid = null;
            // Find custom field id.
            foreach($customfields as $field) {
                if ($field->shortname == $fieldname) {
                    $fieldid = $field->id;
                    break;
                }
            }
            // Query user.
            $sql = 'fieldid = ? AND ' . $DB->sql_compare_text('data') . ' = ?';
            $userid = $DB->get_record_select('user_info_data', $sql, [$fieldid, $matchvalue], 'userid');
            if ($userid) {
                $user = $DB->get_record('user', array('id' => $userid->userid), '*');
            } else {
                $user = false;
            }
        }
    }
    return $user;
}
/**
 * Simple path traversal. Support only dot separator. If it finds an array takes the first item.
 * @param string text the text to search in
 * @param string jsonpath a list of dot separated terms.
 */
function appcrue_get_json_node($text, $jsonpath) {
    $steps = explode('.',$jsonpath);
    $json = json_decode($text);
    // Traverse the steps.
    $node = $json;
    foreach($steps as $step) {
        if ($step == '') continue;
        $node = $node->$step;
        if (is_array($node)) {
            $node = $node[0];
        }
    }
    return $node;
}
/**
 * Returns the target URL according to optional_param parameters in @see autologin.php.
 * - urltogo: if present, uses it as relative path.
 * - course, group: search a course with matching idnumber
 *   the pattern '%-{$course}-{$group}-%'. Resolves any metalinking and
 *   returns the parent course.
 * @return \moodle_url
 */
function appcrue_get_target_url() {
    $urltogo = optional_param('urltogo', null, PARAM_URL);    // Relative URL to redirect.
    $course = optional_param('course', null, PARAM_INT); // Course internal ID SIGMA
    $group = optional_param('group', 1, PARAM_INT); // Grupo docente.
    if ($urltogo !== null) {
        return new moodle_url($urltogo);
    } else if ($course !== null) {
        // Search a course that matches its idnumber with the pattern using course and group.
        /** @var \moodle_database $DB */
        global $DB;
        $coursepattern = get_config('local_appcrue', 'course_pattern');
        // Compose the pattern.
        $coursepattern = str_replace(['{course}', '{group}'],
                                    [$course, $group],
                                    $coursepattern);
        // Pattern is scaped to avoid SQL injection risks.
        $courserecord = $DB->get_record_select(
                            'course',
                            "idnumber LIKE :coursepattern",
                            ['coursepattern' => $coursepattern]);
        if ($courserecord) {
            // Check if it is metalinked to any parent "META" course.
            $metaid = $DB->get_record('enrol', array('customint1' => $courserecord->id, 'enrol' => 'meta'), 'courseid');
            if ($metaid) {
                return new moodle_url("/course/view.php", ["id" => $metaid->courseid]);
            } else {
                return new moodle_url("/course/view.php", ["id" => $courserecord->id]);
            }
        }
    }
    // Default target.
    return new moodle_url("/my");
}
function appcrue_get_username($userid) {
    global $DB;
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
    return fullname($user);
}
/**
 * Classify the events into the AppCrue types of events: “EXAMEN”, “HORARIO”, “REVISION_DE_EXAMEN”, “TUTORIA”
 * TODO: Refine events types for more activities.
 * @param stdClass $event
 * @return "EXAMEN"|"HORARIO"
 */
function appcrue_get_event_type($event) {
    $examentype = get_config('local_appcrue', 'examen_event_type');
    if ($event->modulename != null && strpos($examentype, $event->modulename) !== false) {
        return 'EXAMEN';
    }
    return 'HORARIO';
}
/**
 * Renew the idp token.
 * @deprecated
 * TODO: Este proceso no será necesario si usamos directametne el token válido.
 */
function appcrue_get_new_idp_token() {
    // Mockup pseudocode.
    $idpgettokenurl = get_config('local_appcrue', 'idp_token_url');
    $idpid = get_config('local_appcrue', 'idp_client_id');
    $idpsecret = get_config('local_appcrue', 'idp_client_secret');
    $matchvalue = base64_encode("$idpid:$idpsecret");
    $password = '';
    // Make a request to obtain a new token.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $idpgettokenurl);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 30 seconds.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_USERPWD, "$matchvalue:$password");
    $result = curl_exec($ch);
    $statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);   // Get status code.
    // TODO: Maybe check status code. Probably it's enough with parsing the result.
    curl_close($ch);
    /* Expected return is:
     { "token_type": "Bearer",
     "access_token": "f57e1777-a199-416e-917b-f3ea8e93f5be",
     "expires_in": 1627040493 } */
    $token = json_decode($result);
    set_config('local_appcrue', "idp_last_token", $result);
    return $token;
}
/**
 * Gets last_known token to connect to IDP.
 */
function appcrue_get_idp_token() {
    $token = json_decode(get_config('local_appcrue', "idp_last_token"));
    if ($token && $token->expires_in < time() ) {
        return $token->access_token;
    } else {
        return appcrue_get_new_idp_token();
    }
}