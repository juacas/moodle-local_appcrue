<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/appcrue/locallib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once(__DIR__ . '/appcrue_test_base.php');

/**
 * Tests for the forums JSON service.
 *
 * @package local_appcrue
 * @covers \local_appcrue\forums_service
 */
final class forums_service_test extends appcrue_test_base {
    /**
     * Post trees recursively attach replies to their parent post.
     */
    public function test_build_post_tree_builds_nested_replies(): void {
        $posts = [
            '1' => ['id' => '1', 'parent_id' => '0', 'replies' => []],
            '2' => ['id' => '2', 'parent_id' => '1', 'replies' => []],
            '3' => ['id' => '3', 'parent_id' => '2', 'replies' => []],
        ];

        $tree = forums_service::build_post_tree($posts);

        $this->assertCount(1, $tree);
        $this->assertSame('1', $tree[0]['id']);
        $this->assertSame('2', $tree[0]['replies'][0]['id']);
        $this->assertSame('3', $tree[0]['replies'][0]['replies'][0]['id']);
    }

    /**
     * The forums endpoint returns visible discussions and filtered text fields.
     */
    public function test_get_items_returns_discussion_tree_for_enrolled_user(): void {
        $fixture = $this->create_enrolled_user_course([
            'fullname' => '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}',
        ], ['lang' => 'en']);
        $this->setUser($fixture['user']);
        $forum = self::getDataGenerator()->create_module('forum', [
            'course' => $fixture['course']->id,
            'name' => '{mlang en}Forum EN{mlang}{mlang es}Foro ES{mlang}',
            'intro' => '{mlang en}Forum description EN{mlang}{mlang es}Descripción ES{mlang}',
            'introformat' => FORMAT_HTML,
        ]);
        $forumgenerator = self::getDataGenerator()->get_plugin_generator('mod_forum');
        $discussion = $forumgenerator->create_discussion([
            'course' => $fixture['course']->id,
            'forum' => $forum->id,
            'userid' => $fixture['user']->id,
            'name' => '{mlang en}Topic EN{mlang}{mlang es}Tema ES{mlang}',
            'message' => '{mlang en}Root message EN{mlang}{mlang es}Mensaje raíz ES{mlang}',
            'messageformat' => FORMAT_HTML,
        ]);
        $forumgenerator->create_post([
            'discussion' => $discussion->id,
            'userid' => $fixture['user']->id,
            'parent' => $discussion->firstpost,
            'subject' => 'Reply',
            'message' => 'Reply message',
            'messageformat' => FORMAT_HTML,
        ]);
        $service = new forums_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = 0;
        $this->use_language('en');

        [$items, $count] = $service->get_items();

        $this->assertSame(2, $count);
        $this->assertNotEmpty($items);
        $item = end($items);
        $this->assertSame('Course EN', $item['course_title']);
        $this->assertSame('Forum EN', $item['forum_name']);
        $this->assertSame('Topic EN', $item['topic_title']);
        $this->assertSame('Forum description EN', $item['description']);
        $this->assertCount(1, $item['replies']);
        $this->assertStringContainsString('Root message EN', $item['replies'][0]['message']);
        $this->assertStringNotContainsString('{mlang', $item['replies'][0]['message']);
    }

    /**
     * The service reports an empty forum with a synthetic topic.
     */
    public function test_get_items_reports_forum_without_discussions(): void {
        $fixture = $this->create_enrolled_user_course();
        $this->setUser($fixture['user']);
        self::getDataGenerator()->create_module('forum', [
            'course' => $fixture['course']->id,
            'name' => 'Empty forum',
        ]);
        $service = new forums_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = 0;

        [$items, $count] = $service->get_items();

        $this->assertCount(1, $items);
        $this->assertSame(0, $count);
        $this->assertSame([], $items[0]['replies']);
        $this->assertSame('Empty forum', $items[0]['topic_title']);
    }
}

/**
 * Request-independent forums service double.
 */
class forums_service_test_double extends forums_service {
    /**
     * Avoid authentication while testing service methods.
     */
    public function __construct() {
    }
}
