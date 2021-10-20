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

        // Send as Admin. TODO: Configure sender.
        $USER = get_admin();
        // Remap all tousers of the messages.
        foreach($messages as $key=>$message) {
            $receiver = $message['touserkey'];
            $touser = appcrue_find_user($field, $receiver);
            if ($touser) {
                $messages[$key]['touserid'] = $touser->id;
                unset($messages[$key]['touserkey']); // Clean the parameters.
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
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'msgid' => new external_value(PARAM_INT, 'test this to know if it succeeds:  id of the created message if it succeeded, -1 when failed'),
                    'clientmsgid' => new external_value(PARAM_ALPHANUMEXT, 'your own id for the message', VALUE_OPTIONAL),
                    'errormessage' => new external_value(PARAM_TEXT, 'error message - if it failed', VALUE_OPTIONAL),
                    'text' => new external_value(PARAM_RAW, 'The text of the message', VALUE_OPTIONAL),
                    'timecreated' => new external_value(PARAM_INT, 'The timecreated timestamp for the message', VALUE_OPTIONAL),
                    'conversationid' => new external_value(PARAM_INT, 'The conversation id for this message', VALUE_OPTIONAL),
                    'useridfrom' => new external_value(PARAM_INT, 'The user id who sent the message', VALUE_OPTIONAL),
                    'candeletemessagesforallusers' => new external_value(PARAM_BOOL,
                        'If the user can delete messages in the conversation for all users', VALUE_DEFAULT, false),
                )
            )
        );
    }
}