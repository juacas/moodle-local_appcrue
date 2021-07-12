<?php
/**
 * Checks the token and gets the user associated with it.
 * @param strign $token authorization token given to AppCrue by the University IDP. Usually an OAuth2 token.
 */
function appcrue_get_user($token) {
    global $DB, $CFG;
    //$idp_url = 'https://idp.uva.es/adas/usertoken';
    $idp_url = $CFG->local_appcrue_idp_url;
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