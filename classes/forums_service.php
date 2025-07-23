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
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/forum/lib.php');
/**
 * Class forums_service
 *
 * @package    local_appcrue
 * @copyright  2025 Juan Pablo de Castro <juan.pablo.de.castro@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forums_service {
    private static function build_post_tree(array &$postmap, string $parentid = "0"): array {
        $tree = [];

        foreach ($postmap as $postid => $post) {
            if ($post['parent_id'] === $parentid) {
                $children = self::build_post_tree($postmap, $post['id']);
                $post['replies'] = $children;
                $tree[] = $post;
            }
        }

        return $tree;
    }
    /**
     * Get forums data for the user.
     * @param mixed $user
     * @return array{course_title: string, description: string, forum_name: string, html_url: string, lock_at: string, posted_at: int, replies: bool|string, todo_date: string, topic_title: string, unread_count: int|string[]}
     */
    public static function get_items($user) {
        global $DB, $CFG, $USER;
        // TODO: Show only tracking forums??
        $tracking = false;
        // JPC: ¿¿¿¿ LIMITAR POR TIEMPO??

        $forumoutput = [];
        // Get forums accessible to the user.
        $forums = self::get_readable_forums($user->id);

        foreach ($forums as $forum) {
            $courseid = $forum->course;
            $modinfos = get_fast_modinfo($courseid);
            $cm = $modinfos->instances['forum'][$forum->id] ?? null;
            if (!$cm) {
                continue;
            }
            if (!$cm->uservisible) {
                continue;
            }
            $context = context_module::instance($cm->id);
            // Process files.
            $forumdescription = file_rewrite_pluginfile_urls(
                $forum->intro,
                'pluginfile.php',
                $context->id,
                'mod_forum',
                'intro',
                0
            );
            $forumdescription = format_text(
                $forumdescription,
                $forum->introformat,
                ['context' => $context],
            );
            // Get all the recent discussions we're allowed to see
            // get the most recent posts in a forum in descending order.
            // The call to default sort order here will use
            // that unless the discussion that post is in has a timestart set
            // in the future.
            // This sort will ignore pinned posts as we want the most recent.
            $sort = forum_get_default_sort_order(true, 'p.modified', 'd', false);
            $discussions = forum_get_discussions(
                $cm,
                $sort,
                false,
                -1,
                100, // Limit to 100 discussions.
                false,
                -1,
                0,
                FORUM_POSTS_ALL_USER_GROUPS
            );

            foreach ($discussions as $discussion) {
                $posts = forum_get_all_discussion_posts($discussion->discussion, 'created ASC', $tracking);
                $postmap = [];
                foreach ($posts as $post) {
                    $message = file_rewrite_pluginfile_urls(
                        $post->message,
                        'pluginfile.php',
                        $context->id,
                        'mod_forum',
                        'post',
                        $post->id
                    );
                    $message = format_text(
                        $message,
                        $post->messageformat,
                        ['context' => $context, 'para' => false, 'trusted' => true]
                    );
                    $postmap[$post->id] = [
                        'id'           => (string)$post->id,
                        'parent_id'    => (string)$post->parent,
                        'display_name' => appcrue_get_userfullname($post->userid),
                        'createdAt'    => $post->created,
                        'message'      => $message,
                        'replies'      => [],
                    ];
                }

                $rootposts = self::build_post_tree($postmap);

                $discussionurl = new \moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]);

                // JPC ¿¿¿¿¿ Se mezcla foro y discussion ????
                $forumoutput[] = [
                    'course_title' => (string) ($course->fullname ?? ''),
                    'forum_name'   => (string) ($forum->name ?? ''),
                    'description'  => (string) $forumdescription,
                    'lock_at'      => '',
                    'todo_date'    => '',
                    'html_url'     => (string) ($discussionurl->out(false) ?? ''),
                    'topic_title'  => (string) ($discussion->name ?? ''),
                    'posted_at'    => isset($discussion->created) ? (int)$discussion->created : time(),
                    'unread_count' => isset($discussion->replies) ? (string)$discussion->replies : '0',
                    'replies'      => $rootposts,
                ];
            }
        }

        return $forumoutput;
    }

    /**
     * An array of forum objects that the user is allowed to read/search through.
     * NOTE: This is copypasted from Moodle core function forum_get_readable_forums()
     * because it has a bug that breaks in Moodle 4.5, at least.
     * @global object
     * @global object
     * @global object
     * @param int $userid
     * @param int $courseid if 0, we look for forums throughout the whole site.
     * @return array of forum objects, or false if no matches
     *         Forum objects have the following attributes:
     *         id, type, course, cmid, cmvisible, cmgroupmode, accessallgroups,
     *         viewhiddentimedposts
     */
    public static function get_readable_forums($userid, $courseid = 0) {

        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/course/lib.php');

        if (!$forummod = $DB->get_record('modules', ['name' => 'forum'])) {
            throw new \moodle_exception('notinstalled', 'forum');
        }

        if ($courseid) {
            $courses = $DB->get_records('course', ['id' => $courseid]);
        } else {
            // If no course is specified, then the user can see SITE + his courses.
            $courses1 = $DB->get_records('course', ['id' => SITEID]);
            $courses2 = enrol_get_users_courses($userid, true);
            $courses = array_merge($courses1, $courses2);
        }
        if (!$courses) {
            return [];
        }

        $readableforums = [];

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course);

            if (empty($modinfo->instances['forum'])) {
                // hmm, no forums?
                continue;
            }

            $courseforums = $DB->get_records('forum', ['course' => $course->id]);

            foreach ($modinfo->instances['forum'] as $forumid => $cm) {
                if (!$cm->uservisible or !isset($courseforums[$forumid])) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                $forum = $courseforums[$forumid];
                $forum->context = $context;
                $forum->cm = $cm;

                if (!has_capability('mod/forum:viewdiscussion', $context)) {
                    continue;
                }

                /// group access
                if (groups_get_activity_groupmode($cm, $course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                    $forum->onlygroups = $modinfo->get_groups($cm->groupingid);
                    $forum->onlygroups[] = -1;
                }

                /// hidden timed discussions
                $forum->viewhiddentimedposts = true;
                if (!empty($CFG->forum_enabletimedposts)) {
                    if (!has_capability('mod/forum:viewhiddentimedposts', $context)) {
                        $forum->viewhiddentimedposts = false;
                    }
                }

                /// qanda access
                if (
                    $forum->type == 'qanda'
                    && !has_capability('mod/forum:viewqandawithoutposting', $context)
                ) {
                    // We need to check whether the user has posted in the qanda forum.
                    $forum->onlydiscussions = [];  // Holds discussion ids for the discussions
                                                    // the user is allowed to see in this forum.
                    if ($discussionspostedin = forum_discussions_user_has_posted_in($forum->id, $USER->id)) {
                        foreach ($discussionspostedin as $d) {
                            $forum->onlydiscussions[] = $d->id;
                        }
                    }
                }

                $readableforums[$forum->id] = $forum;
            }

            unset($modinfo);
        } // End foreach $courses

        return $readableforums;
    }
}
