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
 * @return list(stdClass|null, stdClass)
 */
function appcrue_get_user($token) {
    /** @var moodle_database $DB */
    global $DB;
    $matchvalue = false;
    $tokentype = optional_param('method','OAUTH2', PARAM_ALPHANUMEXT); // TODO: Quitar todas menos OAUTH (genérico).

    global $CFG;
    // Load curl class.
    require_once($CFG->dirroot . '/lib/filelib.php');
    // The idp service for checking the token i.e. 'https://idp.uva.es/api/adas/oauth2/tokendata'.
    $idpurl = get_config('local_appcrue', 'idp_token_url');
    $curl = new \curl();
    $options = [
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_CONNECTTIMEOUT' => 5,
        'CURLOPT_HTTPAUTH' => CURLAUTH_ANY
    ];
    $curl->setHeader(["Authorization: Bearer $token"]);
    $result = $curl->get($idpurl, null, $options);
    $statuscode = $curl->get_info()['http_code'];
    // Debugging info for response.
    $returnstatus = new stdClass();
    $returnstatus->code = $statuscode;
    $returnstatus->result = $result;

    // Get matchvalue of the token from the idp.
    if ($statuscode == 200) {
        $jsonpath = get_config('local_appcrue', 'idp_user_json_path');
        $matchvalue = appcrue_get_json_node($result, $jsonpath);
    } else {
        debugging("Permission denied for the token: $token", DEBUG_NORMAL);
        $matchvalue = false;
    }

    // Get user.
    if ($matchvalue == false) {
        $user = null;
        debugging("Path {$jsonpath} not found in: {$result}", DEBUG_NORMAL);
    } else {
        $fieldname = get_config('local_appcrue', 'match_user_by');
        // First check in standard fieldnames.
        $fields = get_user_fieldnames();
        if (array_search($fieldname, $fields) !== false) {
            $user = $DB->get_record('user', array($fieldname => $matchvalue), '*');
            if ($user == false) {
                debugging("No match with: {$fieldname} => {$matchvalue}", DEBUG_NORMAL);
            }
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
                debugging("No match with: {$sql}", DEBUG_NORMAL);
            }
        }
    }
    return [$user, $returnstatus];
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
        if (!isset($node->$step)) {
            return null;
        }
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