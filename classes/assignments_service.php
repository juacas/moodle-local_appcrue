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

        $supportedmods = ['assign', 'quiz', 'offlinequiz', 'lesson', 'scorm', 'workshop', 'quest'];

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course, $this->user->id);

            foreach ($modinfo->get_cms() as $cm) {
                // Filter only visible assign modules.
                if (!$cm->uservisible || !in_array($cm->modname, $supportedmods)) {
                    continue;
                }

                // Get the assignment record
                $record  = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);

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
    private function format_activity($course, $cm, $record) {
        $data = [
            'course_title' => $course->fullname,
            'title'        => $record->name ?? $cm->name,
            'description'  => html_entity_decode(strip_tags($record->intro ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'type'         => $cm->modname,
            'html_url'     => (new \moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]))->out(false),
            'due_at'       => null,
        ];
    
        // Extraer fechas según el tipo de actividad.
        switch ($cm->modname) {
            case 'assign':
                $data['due_at'] = $record->duedate;
                break;
            case 'quiz':
            case 'offlinequiz':
                $data['due_at'] = $record->timeclose;
                break;
            case 'lesson':
                $data['due_at'] = $record->deadline;
                break;
            case 'scorm':
                $data['due_at'] = $record->timeclose ?? null;
                break;
            case 'workshop':
                $data['due_at'] = $record->submissionend;
                break;
            case 'quest':
                $data['due_at'] = $record->timeend;
                break;
        }
    
        return $data;
    }
}
