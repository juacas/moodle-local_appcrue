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
 * Assignments service implementation for the AppCrue.
 *
 * @package    local_appcrue
 * @copyright  2025 Alberto Otero Mato <alberto.otero@altia.es>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;

use context_module;
use core_availability\info_module;

/**
 * Class assignments_service
 */
class assignments_service extends appcrue_service {
    /** @var array Map of assignment types to their date fields. */
    private $assignmentsdates;
    /**
     * Constructor.
     * Load the assignments:dates map.
     * Each line in the map is in format mod_activityname|table|datestart|duedate
     * Store it in an associative array indexed by mod_activityname.
     */
    public function __construct() {
        parent::__construct();

        $this->assignmentsdates = [];
        $map = get_config('local_appcrue', 'assignments_dates');
        if ($map) {
            $lines = explode("\n", $map);
            foreach ($lines as $line) {
                $parts = explode('|', trim($line));
                if (count($parts) >= 4) {
                    $this->assignmentsdates[$parts[0]] = [
                        'table'     => $parts[1],
                        'duedate'   => $parts[2],
                        'cutoffdate'   => $parts[3],
                    ];
                }
            }
        }
    }


    /**
     * Get data response.
     */
    public function get_data_response() {
        $items = $this->get_items();
        $count = count($items);
        return [[ 'assignments' => $items ], $count];
    }

    /**
     * Retrieves all assignments visible to the user in their active courses.
     *
     * @return array
     */
    public function get_items() {
        global $DB;

        $courses = enrol_get_users_courses($this->user->id, true);
        $assignments = [];
        $supportedmods = array_keys($this->assignmentsdates);

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course, $this->user->id);

            foreach ($modinfo->get_cms() as $cm) {
                // Filter only visible assign module that are in the assignmentsdates map.
                if (!$cm->uservisible || !in_array("mod_" . $cm->modname, $supportedmods)) {
                    continue;
                }

                // Get the assignment record.
                $record  = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);
                // TODO: Implement access dates from a secondary table if different than the main one.
                $assignments[] = $this->format_activity($course, $cm, $record);
            }
        }

        return $assignments;
    }

    /**
     * Formats the activity data to return in the service.
     *
     * @param object $course
     * @param object $cm
     * @param object $record
     * @return array
     */
    protected function format_activity($course, $cm, $record) {
        // Format description and title applying filters and removing HTML tags.
        $title = format_string($record->name ?? $cm->name);
        $title = html_to_text($title, 0, false);
        $description = format_text(
            $record->intro ?? '',
            $record->introformat ?? FORMAT_MOODLE,
            ['context' => context_module::instance($cm->id)]
        );
        $description = html_to_text($description, 0, false);
        $data = [
            'course_title' => $course->fullname,
            'title'        => $title,
            'description'  => $description,
            'type'         => $cm->modname,
            'html_url'     => (new \moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]))->out(false),
            'due_at'       => null,
        ];
        if (isset($this->assignmentsdates['mod_' . $cm->modname])) {
            $datesinfo = $this->assignmentsdates['mod_' . $cm->modname];
            $datefieldname = $datesinfo['duedate'];
            // Check if the record has the date fields.
            if (property_exists($record, $datefieldname)) {
                $data['due_at'] = $record->{$datefieldname} != "0" ? $record->{$datefieldname} : null;
            }
        }
        return $data;
    }
}
