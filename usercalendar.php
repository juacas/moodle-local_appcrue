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
 * Give the calendar events to the app.
 *
 * @package    local_appcrue
 * @copyright  2021 University of Valladoild, Spain
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once('locallib.php');

if (!get_config('local_appcrue', 'enable_usercalendar')) {
    throw new moodle_exception('servicedonotexist', 'error');
}
$token = required_param('token', PARAM_RAW);
$fromdate = optional_param('fromDate', '', PARAM_ALPHANUM);
$todate = optional_param('toDate', '', PARAM_ALPHANUM);
$category = optional_param('category', '', PARAM_ALPHA);
$lang = required_param('lang', PARAM_ALPHA);

$outputmessage = new stdClass;
$outputmessage->calendar = array();
$PAGE->set_context(null);
header('Content-Type: text/json; charset=utf-8');

// Check token.
list($user, $diag) = appcrue_get_user($token);
if ($user != null) {
    // Get the calendar type we are using.
    $calendartype = \core_calendar\type_factory::get_calendar_instance();
    // Select events.
    if (get_config('local_appcrue', 'share_course_events')) {
        // All courses.
        $courses = enrol_get_users_courses($user->id, true, 'id, visible, shortname');
        // All groups.
        $groups = array();
        foreach ($courses as $course) {
            $coursegroups = groups_get_all_groups($course->id, $user->id);
            $groups = array_merge($groups, array_keys($coursegroups));
        }
    } else {
        $courses = array();
        $groups = array();
    }
    // Site events.
    if (get_config('local_appcrue', 'share_site_events')) {
        $courses[SITEID] = new stdClass;
        $courses[SITEID]->shortname = get_string('siteevents', 'calendar');
    }

    if (get_config('local_appcrue', 'share_personal_events')) {
        $users = $user->id;
    } else {
        $users = array();
    }
    // Time range.
    // By default events in the last 5 or next 60 days.
    if ($fromdate != '') {
        $date = DateTime::createFromFormat('Ymd', $fromdate);
        $date->setTime(0, 0, 0);
        $timestart = $date->getTimeStamp();
    } else {
        // Last 5 days.
        $timestart = time() - 432000;
    }
    if ($todate != '') {
        $date = DateTime::createFromFormat('Ymd', $todate);
        $date->setTime(0, 0, 0);
        $timeend = $date->add(new DateInterval("P1D"))->getTimestamp();
    } else {
        // Next 60 days.
        $timeend = time() + 5184000;
    }

    $limitnum = 0;
    $events = calendar_get_legacy_events($timestart, $timeend, $users, $groups, array_keys($courses), false, true,
            true, $limitnum);
    // Order events by day.
    $eventsbyday = array();
    foreach ($events as $event) {
        $eventtype = appcrue_get_event_type($event);
        if ($category != '' && $eventtype != $category) {
            continue;
        }
        $day = date('Y-m-d', $event->timesort);
        $eventsbyday[$day][] = $event;
    }
    // Format output.

    foreach ($eventsbyday as $day => $eventlist) {
        $dayitem = new stdClass;
        $dayitem->date = $day;
        $dayitem->events = array();
        foreach ($eventlist as $event) {

            $me = new calendar_event($event); // To use moodle calendar event services.
            // Hide if module is hidden.
            if (!empty($event->modulename)) {
                $instances = get_fast_modinfo($event->courseid, $user->id)->get_instances_of($event->modulename);
                if (empty($instances[$event->instance]->uservisible)) {
                    continue;
                }
            }
            $eventitem = new stdClass;
            $eventitem->id = $event->id;
            $eventitem->title = $event->name;

            // Format the description text.
            $description = format_text($me->description, $me->format, ['context' => $me->context]);
            // Then convert it to plain text, since it's the only format allowed for the event description property.
            // We use html_to_text in order to convert <br> and <p> tags to new line characters for descriptions in HTML format.
            $description = html_to_text($description, 0);
            $eventitem->description = $description;

            $eventitem->nameAuthor = appcrue_get_username($event->userid); // TODO: get author.
            $eventitem->type = appcrue_get_event_type($event);
            $eventitem->startsAt = $event->timestart;
            $eventitem->endsAt = $event->timestart + $event->timeduration;
            if ($event->instance != null) {
                $eventitem->url = $instances[$event->instance]->url->out(true);
            } else {
                // The event is a calendar event.
                $params = [
                    'view' => 'day',
                    'time' => $event->timestart
                ];
                if (isset($event->courseid) && $event->eventtype != 'user') {
                    $params['course'] = $event->courseid;
                }
                $url = new moodle_url("/calendar/view.php", $params);
                $eventitem->url = $url->out(false);
            }
            $eventitem->url = appcrue_create_deep_url($eventitem->url, $token);
            $dayitem->events[] = $eventitem;
        }
        $outputmessage->calendar[] = $dayitem;
    }
}
if (debugging()) {
    $outputmessage->debug = new stdClass();
    $outputmessage->debug->user = $user ? $user->idnumber : null;
    $outputmessage->debug->token = $token;
    $outputmessage->debug->diag = $diag;
}
echo json_encode($outputmessage, JSON_HEX_QUOT | JSON_PRETTY_PRINT);
