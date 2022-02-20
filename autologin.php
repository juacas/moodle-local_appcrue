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
 * Auto-login end-point, a user can be fully authenticated in the site providing a valid token.
 *
 * @package    local_appcrue
 * @copyright  2021 Juan Pblo de Castro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once('locallib.php');

$context = context_system::instance();
$PAGE->set_context($context);

if (!get_config('local_appcrue', 'enable_autologin')) {
    header('HTTP/1.1 405 Method Not Allowed');
    die();
    // Better act as a service don't throw new moodle_exception('servicedonotexist', 'error').
}
try{
    $token = appcrue_get_token_param();    // The key generated by the IDP for AppCrue.
    $fallback = optional_param('fallback', 'error', PARAM_ALPHA); // If token fails: error, logout, ignore.
    // If allow_continue is false then transform fallback mode to error.
    if ($fallback === 'continue' && get_config('local_appcrue', 'allow_continue') == false) {
       throw new moodle_exception('continue_not_allowed', 'local_appcrue');
    }  
} catch (moodle_exception $e) {
    header('HTTP/1.0 400 Bad Request: ' . $e->getMessage());
    die();
}

// Check token and get user record.
// Check token.
list($user, $diag) = appcrue_get_user($token);
if ($user == null && $fallback == 'error') {
    // Better act as service do not throw new moodle_exception('invalidaccessparameter', 'error', '', '', $token).
    header('HTTP/1.0 401 Unauthorized ' . $diag->result);
    die();
}

if ($user == null && $fallback == 'logout') {
    require_logout();
} else if ($user == null && $fallback == 'continue') {
    // Do nothing with session.
    // Pero si no hay sesión entrar como guest para ver la ficha de asignatura.
    if (!isset($USER->id) || $USER->id == 0) {
        $user = core_user::get_user_by_username('guest');
        complete_user_login($user);
        \core\session\manager::apply_concurrent_login_limit($user->id, session_id());
    }
} else if ($user != null) {
    // Token validated, now require an active user: not guest, not suspended.
    core_user::require_active_user($user, true, true);
    complete_user_login($user);
    \core\session\manager::apply_concurrent_login_limit($user->id, session_id());
}
// Get parameters to apply the redirection rules.
$token = optional_param('token', null, PARAM_RAW);    // The key generated by the IDP for AppCrue.
$urltogo = optional_param('urltogo', null, PARAM_URL);    // Relative URL to redirect.
$course = optional_param('course', null, PARAM_INT); // Course internal ID SIGMA
$group = optional_param('group', 1, PARAM_INT); // Grupo docente.
$year = optional_param('year', null, PARAM_INT); // Curso docente.
$pattern = optional_param('pattern', null, PARAM_ALPHA);
$param1 = optional_param('param1', null, PARAM_ALPHANUMEXT);
$param2 = optional_param('param2', null, PARAM_ALPHANUMEXT);
$param3 = optional_param('param3', null, PARAM_ALPHANUMEXT);

$urltogo = appcrue_get_target_url($token, $urltogo, $course, $group, $year, $pattern, $param1, $param2, $param3);
redirect($urltogo);
