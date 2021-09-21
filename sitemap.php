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
require_once($CFG->libdir.'/filelib.php');
require_once('locallib.php');
/** @var moodle_database $DB */
global $DB;
$token = optional_param('token', '', PARAM_RAW);
$category = optional_param('category', 0, PARAM_INT);
$hiddencats = optional_param_array('hidden', [], PARAM_INT);

$PAGE->set_context(null);
header('Content-Type: text/json; charset=utf-8');

$cache = cache::make('local_appcrue', 'sitemaps');
$sitemap = false;//$cache->get($category);

if ($sitemap == false) {
    // This method does not show all categories recursively.
    //$categories = \core_course_category::get_all();
    $categories = $DB->get_records_select('course_categories', 'TRUE',
                        ['id', 'name', 'description', 'parent', 'coursecount', 'visible']);
    $catindex = array();
    $navegableroot = new stdClass();
    $catindex[0] = $navegableroot;
    $errors = []; // List of problems detected.
    // Build index.
    foreach ($categories as $id => $cat) {
        if ($cat->visible == '0') {
            continue;
        }
        $navegable = new stdClass();
        $navegable->name = $cat->name;
        $navegable->description = format_text($cat->description, FORMAT_PLAIN);
        $navegable->id = $cat->id;
        $catindex[$cat->id] = $navegable;
        if ($cat->id == $category) {
            $navegableroot = $navegable;
        }
    }
    // Build tree.
    foreach ($catindex as $catid => $navegable) {
        if ($catid != 0) {
            // Get navegable parent.
            $category = $categories[$catid];
            $parentid = $category->parent;

            $parent = $catindex[$parentid] ?? null;
            $ishidden = array_search($catid, $hiddencats) !== false;
            if ($ishidden) {
                continue;
            }
            // Add child.
            if ($parent ) {
                $parent->navegable[] = $navegable;
            } else {
                // Parent category missing. Do something.
                $errors[] = "Missing parent {$parentid} for category {$category->name}.";
            }
        }
    }
    // Add courses.
    // This implementatios uses one query but no caching.
    // $courses = get_courses(null, null, "c.fullname, c.summary, c.id, c.category");
    // Other way is core_course_category::get_courses() that uses caching. Da error si hay huérfanosen course_categories.
    // $topcat = \core_course_category::top();
    // $courses = $topcat->get_courses(['summary' => true, 'recursive' => true]);

    $courses = $DB->get_records_select('course', 'TRUE', ['fullname', 'summary', 'id', 'category']);
    foreach ($courses as $course) {
        // Find navegable.
        if ($course->id != SITEID && isset($catindex[$course->category])) {
            $nav = $catindex[$course->category];
            $coursenav = new stdClass();
            $coursenav->name = $course->fullname;
            // $context = context_course::instance($course->id);
            // $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);
            $coursenav->description = content_to_text($course->summary, false);
            if ($token) {
                $url = new moodle_url('/local/appcrue/autologin.php',
                    ['token' => $token,
                    'urltogo' => "/course/view.php?id={$course->id}"]);
            } else {
                $url = new moodle_url('/course/view.php', ['id' => $course->id]);
            }
            $coursenav->url = $url->out();
            $nav->navegable[] = $coursenav;
        }
    }

    if (debugging()) {
        $navegableroot->debug = new stdClass();
        $navegableroot->debug->token = $token;
        $navegableroot->debug->errors = $errors;
        $navegableroot->updated = userdate(time());
    }
    $sitemap = json_encode($navegableroot, JSON_HEX_QUOT | JSON_PRETTY_PRINT);
    $cache->set($category->id, $sitemap);
}
echo $sitemap;
