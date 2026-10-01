<?php
// phpcs:disable moodle.PHPUnit.TestCaseCovers.Missing -- PHP attributes keep PHPUnit 11 coverage metadata without PHPUnit 11 docblock deprecations.
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
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * PHPUnit coverage for the files service.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_appcrue;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/appcrue_test_base.php');
require_once(__DIR__ . '/fixtures/files_service_test_double.php');
require_once(__DIR__ . '/fixtures/files_service_test_double.php');

/**
 * Tests for the files JSON service.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue\files_service::class)]
final class files_service_test extends appcrue_test_base {
    /**
     * A disabled or invalid time window includes all files.
     */
    public function test_configure_without_time_window_includes_all(): void {
        set_config('lmsappcrue_files_timewindow', 0, 'local_appcrue');
        $service = new files_service_test_double();

        $service->configure_for_test();

        $this->assertSame(0, $service->timestart);
    }

    /**
     * The files endpoint reports files from visible resources and folders.
     */
    public function test_get_items_returns_resource_and_folder_files(): void {
        $fixture = $this->create_enrolled_user_course([
            'fullname' => '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}',
        ], ['lang' => 'en']);
        $this->setUser($fixture['user']);
        $generator = self::getDataGenerator();
        $resource = $generator->create_module('resource', [
            'course' => $fixture['course']->id,
            'name' => 'Resource',
            'files' => 0,
        ]);
        $folder = $generator->create_module('folder', [
            'course' => $fixture['course']->id,
            'name' => 'Folder',
        ]);
        $file = get_file_storage();
        $file->create_file_from_string([
            'contextid' => \context_module::instance($resource->cmid)->id,
            'component' => 'mod_resource',
            'filearea' => 'content',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'resource.txt',
        ], 'resource content');
        $file->create_file_from_string([
            'contextid' => \context_module::instance($folder->cmid)->id,
            'component' => 'mod_folder',
            'filearea' => 'content',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'folder.pdf',
        ], 'folder content');

        $service = new files_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = 0;
        set_config('includelegacyfiles', 0, 'local_appcrue');
        $this->use_language('en');

        $items = $service->get_items();

        $this->assertCount(2, $items);
        $filenames = array_column($items, 'file_name');
        sort($filenames);
        $this->assertSame(['folder.pdf', 'resource.txt'], $filenames);
        foreach ($items as $item) {
            $this->assertSame(
                $this->expected_multilang_text('Course EN', '{mlang en}Course EN{mlang}{mlang es}Curso ES{mlang}'),
                $item['course_title']
            );
            $this->assertNotEmpty($item['url']);
            $this->assertNotEmpty($item['content_type']);
        }
    }

    /**
     * A future timestamp excludes files created before it.
     */
    public function test_get_items_applies_time_window(): void {
        $fixture = $this->create_enrolled_user_course();
        $this->setUser($fixture['user']);
        $resource = self::getDataGenerator()->create_module('resource', ['course' => $fixture['course']->id]);
        get_file_storage()->create_file_from_string([
            'contextid' => \context_module::instance($resource->cmid)->id,
            'component' => 'mod_resource',
            'filearea' => 'content',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'old.txt',
        ], 'old content');
        $service = new files_service_test_double();
        $service->user = $fixture['user'];
        $service->timestart = time() + 1;

        $this->assertSame([], $service->get_items());
    }
}
