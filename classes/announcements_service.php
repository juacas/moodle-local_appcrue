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

namespace local_appcrue;

use context_module;
/**
 * Class announcements_service
 *
 * @package    local_appcrue
 * @copyright  2025 Alberto Otero Mato <alberto.otero@altia.es>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class announcements_service extends appcrue_service {
    /**
     * Get data response.
     */
    public function get_data_response() {
        // Get the items and count.
        [$items, $count] = $this->get_items();
        // Return the items and count.
        return [ [ 'announcements' => $items ], $count];
    }
    /**
     * Get announcements for a user.
     * @return array Array of announcements for the user.
     */
    public function get_items() {
        global $DB;

        // Devuelve solo cursos de tipos de inscripción ACTIVOS.
        $courses = enrol_get_users_courses($this->user->id, true);
        $results = [];
        $tracking = false;

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course);

            if (empty($modinfo->instances['forum'])) {
                continue;
            }

            $courseforums = $DB->get_records('forum', ['course' => $course->id, 'type' => 'news']);

            foreach ($modinfo->instances['forum'] as $forumid => $cm) {
                if (!isset($courseforums[$forumid]) || !$cm->uservisible) {
                    continue;
                }

                $forum = $courseforums[$forumid];
                $context = context_module::instance($cm->id);

                // Capability check.
                if (!has_capability('mod/forum:viewdiscussion', $context)) { // phpcs:ignore
                    continue;
                }

                // Grupos del usuario en el curso (array con claves = groupid).
                $usergroups = groups_get_all_groups($course->id, $this->user->id);
                $accessall = has_capability('moodle/site:accessallgroups', $context); // phpcs:ignore
                $groupmode = groups_get_activity_groupmode($cm, $course);

                // Obtener discusiones recientes (máx 10).
                $discussions = forum_get_discussions($cm, 'd.timemodified DESC', false, -1, 10);

                foreach ($discussions as $discussion) {
                    // Filtrado por grupos: si la actividad está en SEPARATEGROUPS y el usuario no tiene accessall,
                    // y la discusión tiene groupid distinto de -1, entonces sólo ver si el usuario pertenece.
                    if ($groupmode == SEPARATEGROUPS && !$accessall && !empty($discussion->groupid) && $discussion->groupid != -1) {
                        if (!isset($usergroups[$discussion->groupid])) {
                            continue;
                        }
                    }

                    // Obtener primer post (anuncio principal).
                    $posts = forum_get_all_discussion_posts($discussion->discussion, 'created ASC', $tracking);
                    $post = reset($posts);
                    if (!$post) {
                        continue;
                    }

                    // Procesar mensaje.
                    $message = file_rewrite_pluginfile_urls(
                        $post->message,
                        'pluginfile.php',
                        $context->id,
                        'mod_forum',
                        'post',
                        $post->id
                    );
                    $formattedtext = format_text($message, $post->messageformat, ['context' => $context]);
                    $plaintext = html_entity_decode(
                        strip_tags($formattedtext),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    );

                    $discussionurl = new \moodle_url('/mod/forum/discuss.php', ['d' => $discussion->discussion]);
                    $discussionurl = local_appcrue_create_deep_url($discussionurl->out(), $this->token, $this->tokenmark);

                    $author = \core_user::get_user($post->userid);

                    $results[] = [
                        'courseid' => $course->id,
                        'coursefullname' => $course->fullname,
                        'forumid' => $forum->id,
                        'forumname' => $forum->name,
                        'subject' => format_string($post->subject),
                        'message' => $plaintext,
                        'author' => fullname($author),
                        'timecreated' => (int)$post->created,
                        'url' => $discussionurl,
                    ];
                }
            }
        }

        return [$results, count($results)];
    }
}
