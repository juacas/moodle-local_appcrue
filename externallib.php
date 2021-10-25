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
 * External message API
 *
 * @package    local_appcrue
 * @category   external
 * @copyright  2021 Juan Pablo de Castro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . "/message/lib.php");
require_once($CFG->dirroot . "/message/externallib.php");
require_once("$CFG->dirroot/local/appcrue/locallib.php");

/**
 * Message external functions
 *
 * @package    local_appcrue
 * @category   external
 * @copyright  2021 Juan Pablo de Castro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class local_appcrue_external extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function send_instant_messages_parameters() {
        return new external_function_parameters(
            array(
                'messages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'touserkey' => new external_value(PARAM_RAW, 'Match value for finding the user to send the private message to'),
                            'text' => new external_value(PARAM_RAW, 'The text of the message'),
                            'textformat' => new external_format_value('text', VALUE_DEFAULT, FORMAT_MOODLE),
                            'clientmsgid' => new external_value(PARAM_ALPHANUMEXT, 'your own client id for the message. If this id is provided, the fail message id will be returned to you', VALUE_OPTIONAL),
                        )
                    )
                ),
                'field' => new external_value(PARAM_RAW, 'User field for finding the user'),
            )
        );
    }

    /**
     * Send private messages from the admin USER to other users
     *
     * @param array $messages An array of message to send.
     * @return array
     * @since Moodle 2.2
     */
    public static function send_instant_messages($messages = array(), $field = 'email') {
        global $CFG, $USER;
        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }
        self::validate_parameters(self::send_instant_messages_parameters(), array('messages' => $messages, 'field' => $field));

        // Remap all tousers of the messages.
        foreach($messages as $key=>$message) {
            if (isset($message['touserkey'])) {
                $receiver = $message['touserkey'];
                $touser = appcrue_find_user($field, $receiver);
                if ($touser) {
                    $messages[$key]['touserid'] = $touser->id;
                    unset($messages[$key]['touserkey']); // Clean the parameters.
                }
            }
        }
        return core_message_external::send_instant_messages($messages);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function send_instant_messages_returns() {
        return core_message_external::send_instant_messages_returns();
    }
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function send_instant_message_parameters()
    {
        return new external_function_parameters(
            array(
                'touserkey' => new external_value(PARAM_RAW, 'Match value for finding the user to send the private message to'),
                'text' => new external_value(PARAM_RAW, 'The text of the message'),
                'textformat' => new external_format_value('text', VALUE_DEFAULT, FORMAT_MOODLE),
                'field' => new external_value(PARAM_RAW, 'User field for finding the user', VALUE_DEFAULT, 'email')
            )
        );
    }

    /**
     * Send private messages from the admin USER to other users
     *
     * @param array $messages An array of message to send.
     * @return array
     * @since Moodle 2.2
     */
    public static function send_instant_message($touserkey, $text, $textformat = FORMAT_MOODLE, $field = 'email')
    {
        self::validate_parameters(self::send_instant_message_parameters(), array('touserkey' => $touserkey, 'text' => $text, 'textformat' => $textformat, 'field' => $field));
        $message = array();
        $message['touserkey'] = $touserkey;
        $message['text'] = $text;
        $message['textformat'] = $textformat;

        return local_appcrue_external::send_instant_messages([$message], $field);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function send_instant_message_returns()
    {
        return core_message_external::send_instant_messages_returns();
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function notify_grade_parameters()
    {
        return new external_function_parameters(
            array(
                'idusuario' => new external_value(PARAM_RAW, 'Oficial university id for a student.', VALUE_OPTIONAL),
                'nip' => new external_value(PARAM_RAW, 'Source system\'s user identification.', VALUE_OPTIONAL),
                'useremail' => new external_value(PARAM_EMAIL, 'Email of the student.', VALUE_OPTIONAL),
                'subject' => new external_value(PARAM_RAW, 'Internal subject code.'),
                'group' => new external_value(PARAM_INT, 'Enrollment group.', VALUE_OPTIONAL),
                'subjectname' => new external_value(PARAM_RAW, 'Oficial subject name.'),
                'course' => new external_value(PARAM_RAW, 'Course year.'),
                'grade' => new external_value(PARAM_RAW, 'Numerical grade.'),
                'call' => new external_value(PARAM_RAW, 'Grading call.', VALUE_OPTIONAL),
                'gradealpha' => new external_value(PARAM_RAW, 'Grade text equivalence.', VALUE_OPTIONAL),
                'revdate' => new external_value(PARAM_INT, 'Date of the revision in epoch format.', VALUE_OPTIONAL),
                'comment' => new external_value(PARAM_RAW, 'Description of the grade publication', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Send private messages from the admin USER to other users
     *
     * @param array $messages An array of message to send.
     * @return array
     * @since Moodle 2.2
     */
    public static function notify_grade($idusuario, $nip, $useremail = '', $subject, $group, $subjectname, $course, $grade, $call, $gradealpha, $revdate, $comment) {
        $params = array(
            'idusuario' => $idusuario,
            'nip' => $nip,
            'useremail' => $useremail,
            'subjectname' => $subjectname,
            'subject' => $subject,
            'group' => $group,
            'course' => $course,
            'grade' => $grade,
            'call' => $call,
            'gradealpha' => $gradealpha,
            'revdate' => $revdate,
            'comment' => $comment
        );
        self::validate_parameters(self::notify_grade_parameters(), $params);
        // TODO: Find a way to integrate final grades into gradebook.

        // Compose message.
        // Find user.
        if ($idusuario) {
            $touser = appcrue_find_user('idnumber', $idusuario);
        }
        if ($touser == false) {
            $touser = appcrue_find_user('email', $useremail);
        }
        if ($touser == false) {
            throw new moodle_exception('invalidarguments');
        }
        force_current_language($touser->lang);
        $revdateformat = userdate($revdate, get_string('strftimedatetime', 'core_langconfig'));
        $params['revdateformat'] = $revdateformat;
        $text = get_string('new_grade_message', 'local_appcrue', $params);
        // Send the message.
        $message = array();
        $message['touserid'] = $touser->id;
        $message['text'] = $text;
        $message['textformat'] = FORMAT_MARKDOWN;

        return core_message_external::send_instant_messages([$message]);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function notify_grade_returns()
    {
        // TODO: Customize return type.
        return self::send_instant_message_returns();
    }
}
