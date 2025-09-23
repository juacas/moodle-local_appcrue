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

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course, $this->user->id);

            foreach ($modinfo->get_cms() as $cm) {
                // Filter only visible assign modules.
                if (!$cm->uservisible || $cm->modname !== 'assign') {
                    continue;
                }

                $context = context_module::instance($cm->id);

                // Get the assignment record
                $assign = $DB->get_record('assign', ['id' => $cm->instance], '*', MUST_EXIST);

                $assignments[] = $this->format_assignment($course, $assign, $cm);
            }
        }

        return $assignments;
    }

    /**
     * Formats the assignment data to return in the service.
     *
     * @param object $course
     * @param object $assign
     * @param object $cm
     * @return array
     */
    private function format_assignment($course, $assign, $cm) {
        global $DB;

        // Get the enabled submission types
        $sql = "SELECT plugin
                FROM {assign_plugin_config}
                WHERE assignment = :assignid
                  AND subtype = :subtype
                  AND name = :name
                  AND " . $DB->sql_compare_text('value') . " = :value";

        $params = [
            'assignid' => $assign->id,
            'subtype'  => 'assignsubmission',
            'name'     => 'enabled',
            'value'    => '1'
        ];

        $records = $DB->get_records_sql($sql, $params);

        $submissiontypes = array_values(array_map(function($r) {
            return $r->plugin; // "file", "onlinetext"
        }, $records));

        return [
            'course_title'     => $course->fullname,
            'title'            => $assign->name,
            'description'      => html_entity_decode(strip_tags($assign->intro), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'due_at'           => $assign->duedate,
            'submission_types' => $submissiontypes,
            'html_url'         => (new \moodle_url('/mod/assign/view.php', ['id' => $cm->id]))->out(false),
        ];
    }
}
