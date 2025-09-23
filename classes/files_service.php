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
 * Files service implementation for the AppCrue.
 *
 * @package    local_appcrue
 * @copyright  2025 Alberto Otero Mato <alberto.otero@altia.es>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_appcrue;

use context_module;
use context_course;
use core_availability\info_module;

/**
 * Class files_service
 */
class files_service extends appcrue_service {

    /**
     * Get data response.
     */
    public function get_data_response() {
        $items = $this->get_items();
        $count = count($items);

        return [[ 'files' => $items ], $count];
    }

    /**
     * Recover files visible to the user in the courses in which they are enrolled.
     *
     * @return array
     */
    public function get_items() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->dirroot . '/course/lib.php');

        $courses = enrol_get_users_courses($this->user->id, true);
        $files = [];
        $fs = get_file_storage();

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course, $this->user->id);

            // 1) "Resource" module files.
            foreach ($modinfo->get_cms() as $cm) {
                if (!$cm->uservisible || empty($cm->modname)) {
                    continue;
                }

                $context = \context_module::instance($cm->id);

                // Resource (archivo suelto).
                if ($cm->modname === 'resource') {
                    $storedfiles = $fs->get_area_files(
                        $context->id,
                        'mod_resource',
                        'content',
                        0,
                        'filename',
                        false
                    );

                    foreach ($storedfiles as $f) {
                        $files[] = $this->format_file($course, $f, $CFG);
                    }
                }

                // Folder (carpeta con varios archivos).
                if ($cm->modname === 'folder') {
                    $storedfiles = $fs->get_area_files(
                        $context->id,
                        'mod_folder',
                        'content',
                        0,
                        'filename',
                        false
                    );

                    foreach ($storedfiles as $f) {
                        $files[] = $this->format_file($course, $f, $CFG);
                    }
                }
            }

            // 2) Files in the course's "legacy" area.
            $coursecontext = \context_course::instance($course->id);

            $legacyfiles = $fs->get_area_files(
                $coursecontext->id,
                'course',
                'legacy',
                0,
                'filename',
                false
            );

            foreach ($legacyfiles as $f) {
                $files[] = $this->format_file($course, $f, $CFG);
            }
        }

        return $files;
    }

    // Uniformly formats the information in a file.
    private function format_file($course, $f, $CFG) {
        return [
            'course_title' => $course->fullname,
            'file_name'    => $f->get_filename(),
            'created_at'   => $f->get_timecreated(),
            'content_type' => $f->get_mimetype(),
            'size'         => $f->get_filesize(),
            'url'          => file_encode_url(
                "$CFG->wwwroot/pluginfile.php",
                '/' . $f->get_contextid() . '/' . $f->get_component() . '/' .
                $f->get_filearea() . '/' . $f->get_itemid() .
                $f->get_filepath() . $f->get_filename()
            )
        ];
    }
}
