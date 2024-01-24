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
 * @return list(stdClass|null, stdClass) the user and the result of the check.
 */
function appcrue_get_user($token) {
    /** @var moodle_database $DB */
    global $DB;
    $matchvalue = false;
    $user = false;
    $returnstatus = new stdClass();
    list($matchvalue, $tokenstatus) = appcrue_validate_token($token);
    $returnstatus->code = $tokenstatus->code;
    $returnstatus->result = $tokenstatus->result;
    // Get user.
    if ($returnstatus->code == 401) {
        debugging("Token not valid: " . $returnstatus->result, DEBUG_MINIMAL);
        $user = null;
        $returnstatus->status = 'error';
    } else {
        $returnstatus->status = 'validated';
        // TODO: Refactor this block as function.
        $fieldname = get_config('local_appcrue', 'match_user_by');
        $user = appcrue_find_user($fieldname, $matchvalue);
        if (!$user) {
            $returnstatus->code = 404; // 404 Not found.
        } else {
            $returnstatus->code = 200; // 200 OK.
        }
    }
    return [$user, $returnstatus];
}
/**
 * Get token from the request.
 */
function appcrue_get_token_param($required = false) : string {
    $token = optional_param('token', '', PARAM_TEXT);
     // Try to extract a Bearer token.
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        if (preg_match('/^Bearer\s+(.*)$/', $auth, $matches)) {
            $token = $matches[1];
        }
    }
    if ($required && empty($token)) {
        throw new moodle_exception('missingtoken', 'local_appcrue');
    }
    return $token;
}
/**
 * Validate token and return the matchvalue.
 * @return list(string|false, stdClass) the matchvalue or false if the token is not valid and a status object.
 */
function appcrue_validate_token($token) {
    $matchvalue = false;
    $returnstatus = new stdClass();
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
    $returnstatus->code = $statuscode;
    $returnstatus->result = $result;

    // Extract a matchvalue of the token from the idp.
    if ($statuscode == 200) {
        $jsonpath = get_config('local_appcrue', 'idp_user_json_path');
        $matchvalue = appcrue_get_json_node($result, $jsonpath);
        if ($matchvalue == false) {
            $returnstatus->result = "Path {$jsonpath} not found in: {$result}";
            debugging($returnstatus->result, DEBUG_NORMAL);
        }
    } else if ($statuscode == 401) {
        $returnstatus->result = "Permission denied for the token: {$token}";
        // Do not break the output: debugging($returnstatus->result, DEBUG_NORMAL);.
        $matchvalue = false;
    } else {
        debugging("IDP problem: $statuscode", DEBUG_MINIMAL);
    }
    return [$matchvalue, $returnstatus];
}
/**
 * Search user fields and get the user
 * @param string $fieldname the name of the field to search into
 * @param string $matchvalue the value to search for.
 * @return stdClass|false user structure
 */
function appcrue_find_user($fieldname, $matchvalue) {
    global $DB;
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
        foreach ($customfields as $field) {
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
    return $user;
}
/**
 * Envelops the url with an token-based url.
 * If token is not provided, the url is labelled depending on $tokenmark:
 * @param string $url the url to be enveloped.
 * @param string $token the token to be used.
 * @param string $tokenmark the mark to be used in the url if $token is nos provided. Can be 'bearer' or 'token'.
 * @param string $fallback the behaviour desired if token validation fails.
 * @return string the enveloped url.
 */
function appcrue_create_deep_url(string $url, $token, $tokenmark = 'bearer', $fallback = 'continue') {
    $params = [];
    $params['urltogo'] = $url;
    $params['fallback'] = $fallback;

    if ($token) {
        $params['token'] = $token; 
    } else if ($tokenmark == 'bearer') {
        $params['<bearer>'] = '';
    } else if ($tokenmark == 'token') {
        $params['token'] = '<token>';
    }
       
    $deepurl = new moodle_url('/local/appcrue/autologin.php', $params);
    return $deepurl->out(false);
}
/**
 * Traverse all nodes and re-encode the urls.
 */
function appcrue_filter_urls($node, $token, $tokenmark) {
    if (isset($node->url)) {
        $node->url = appcrue_create_deep_url($node->url, $token, $tokenmark);
    }
    if (isset($node->navegable)) {
        foreach ($node->navegable as $child) {
            appcrue_filter_urls($child, $token, $tokenmark);
        }
    }
}
/**
 * Simple path traversal. Support only dot separator. If it finds an array takes the first item.
 * @param string text the text to search in
 * @param string jsonpath a list of dot separated terms.
 */
function appcrue_get_json_node($text, $jsonpath) {
    $steps = explode('.', $jsonpath);
    $json = json_decode($text);
    // Traverse the steps.
    $node = $json;
    foreach ($steps as $step) {
        if ($step != '') {
            if (!isset($node->$step)) {
                return null;
            }
            $node = $node->$step;
            if (is_array($node)) {
                $node = $node[0];
            }
        }
    }
    return $node;
}
/**
 * Returns the target URL according to optional_param parameters in @see autologin.php.
 * - urltogo: if present, uses it as relative path.
 * - course, group, year: (not necessarily Moodle's identifiers) search a course with idnumber matching the course pattern i.e.'%-{$course}-{$group}-%'.
 *   Resolves any metalinking and returns the parent course.
 * - pattern: Selector from the patterns library.
 * - param1, param2: general purpose ALPHANUM arguments for generating redirections.
 * @return \moodle_url
 */
function appcrue_get_target_url($token, $urltogo, $course, $group, $year, $pattern, $param1, $param2, $param3) {

    if ($urltogo !== null) {
        return new moodle_url($urltogo);
    } else if ($pattern !== null) {
        // Use pattern lib.
        $patterns = get_config('local_appcrue', 'pattern_lib');
        $patternlib = [];
        $parts = explode("\n", $patterns);
        $parts = array_map("trim", $parts);
        foreach($parts as $currentPart)
        {
            list($key, $value) = explode("=", $currentPart, 2);
            $patternlib[$key] = $value;
        }
        if (isset($patternlib[$pattern])) {
            $selectedpattern = $patternlib[$pattern];
            $url = str_replace(['{token}', '{course}', '{group}', '{year}', '{param1}', '{param2}', '{param3}'],
                                [$token, $course, $group, $year, $param1, $param2, $param3],
                                $selectedpattern
                            );
            return $url;
        } else {
            throw new moodle_exception('invalidrequest');
        }
    } else if ($course !== null) {
        // Search a course that matches its idnumber with the pattern using course, group, year, param1, param2, param3.
        /** @var \moodle_database $DB */
        global $DB;
        $coursepattern = get_config('local_appcrue', 'course_pattern');
        // Compose the pattern.
        $coursepattern = str_replace(['{course}', '{group}', '{year}', '{param1}', '{param2}', '{param3}'],
                                    [$course, $group, $year, $param1, $param2, $param3],
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
    return new moodle_url("/my/");
}
function appcrue_get_username($userid) {
    global $DB;
    $user = $DB->get_record('user', array('id' => $userid), '*');
    if ($user) {
        return fullname($user);
    } else {
        return get_string('unknownuser');
    }
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