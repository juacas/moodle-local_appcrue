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
require_once(__DIR__ . '/appcrue_test_base.php');

/**
 * Tests for the AppCrue procedural library and request authentication helpers.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_get_json_node')]
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_get_user_from_request')]
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_is_apikey_valid')]
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_config_user')]
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_filter_sitemap_urls')]
#[\PHPUnit\Framework\Attributes\CoversFunction('local_appcrue_get_event_type')]
final class locallib_test extends appcrue_test_base {
    /**
     * JSON path traversal supports nested objects and takes the first array item.
     */
    public function test_get_json_node_traverses_objects_and_arrays(): void {
        $json = json_encode([
            'user' => [
                ['identity' => ['username' => 'student01']],
            ],
        ]);

        $this->assertSame('student01', local_appcrue_get_json_node($json, 'user.identity.username'));
        $this->assertNull(local_appcrue_get_json_node($json, 'user.identity.email'));
        $this->assertIsObject(local_appcrue_get_json_node($json, ''));
    }

    /**
     * Sitemap URL filtering is recursive and adds the expected destination type.
     */
    public function test_filter_sitemap_urls_recursively_wraps_urls(): void {
        $root = (object)[
            'url' => '/course/index.php',
            'navegable' => [
                (object)['url' => '/course/view.php?id=2'],
                (object)['name' => 'leaf'],
            ],
        ];

        local_appcrue_filter_sitemap_urls($root, 'token123', 'token');

        $this->assertStringContainsString('/local/appcrue/autologin.php', $root->url);
        $this->assertSame('customTab', $root->destinyType);
        $this->assertStringContainsString('token=token123', $root->navegable[0]->url);
        $this->assertSame('customTab', $root->navegable[0]->destinyType);
        $this->assertObjectNotHasProperty('destinyType', $root->navegable[1]);
    }

    /**
     * A missing token marker leaves sitemap URLs untouched.
     */
    public function test_filter_sitemap_urls_without_authentication_does_nothing(): void {
        $root = (object)['url' => '/my/'];

        local_appcrue_filter_sitemap_urls($root, '', '');

        $this->assertSame('/my/', $root->url);
        $this->assertObjectNotHasProperty('destinyType', $root);
    }

    /**
     * Event classification uses the configured module list.
     */
    public function test_get_event_type_uses_configured_exam_modules(): void {
        set_config('calendar_examen_event_type', 'quiz,assign', 'local_appcrue');

        $this->assertSame('EXAMEN', local_appcrue_get_event_type((object)['modulename' => 'quiz']));
        $this->assertSame('HORARIO', local_appcrue_get_event_type((object)['modulename' => 'forum']));
        $this->assertSame('HORARIO', local_appcrue_get_event_type((object)['modulename' => null]));
    }

    /**
     * API-key requests resolve the configured user field and diagnostic status.
     */
    public function test_get_user_from_request_resolves_user_with_api_key(): void {
        $user = self::getDataGenerator()->create_user(['username' => 'appcrue_student']);
        set_config('api_key', 'testapikey', 'local_appcrue');
        set_config('lmsappcrue_use_user_param', 'username', 'local_appcrue');
        set_config('lmsappcrue_match_user_by', 'username', 'local_appcrue');
        $this->set_request_parameters(['apikey' => 'testapikey', 'username' => $user->username]);

        [$resolveduser, $diagnostic, $token] = local_appcrue_get_user_from_request();

        $this->assertSame($user->id, $resolveduser->id);
        $this->assertSame(200, $diagnostic->code);
        $this->assertSame('User found', $diagnostic->message);
        $this->assertSame('', $token);
    }

    /**
     * Invalid and incomplete authentication requests expose the documented error codes.
     */
    public function test_get_user_from_request_rejects_invalid_authentication(): void {
        set_config('api_key', 'testapikey', 'local_appcrue');
        $this->set_request_parameters(['apikey' => 'wrongkey', 'username' => 'someone']);
        $this->expectExceptionCode(appcrue_service::INVALID_API_KEY);
        local_appcrue_get_user_from_request();
    }

    /**
     * Missing credentials fail before looking up a user.
     */
    public function test_get_user_from_request_requires_token_or_api_key(): void {
        $this->set_request_parameters([]);
        $this->expectExceptionCode(appcrue_service::MISSING_WS_TOKEN);
        local_appcrue_get_user_from_request();
    }

    /**
     * Impersonating a user also sets the language used by text filters.
     */
    public function test_config_user_sets_impersonated_user_language(): void {
        $this->setAdminUser();
        $user = self::getDataGenerator()->create_user(['lang' => 'en']);

        local_appcrue_config_user($user, true);

        global $USER;
        $this->assertSame($user->id, $USER->id);
        $this->assertSame('en', current_language());
    }

    /**
     * API-key validation clears successful attempts and records failed attempts.
     */
    public function test_is_apikey_valid_updates_attempt_configuration(): void {
        set_config('api_key', 'validkey', 'local_appcrue');
        set_config('api_key_attempt', 'oldattempt', 'local_appcrue');

        $this->assertTrue(local_appcrue_is_apikey_valid('validkey'));
        $this->assertSame('', get_config('local_appcrue', 'api_key_attempt'));

        $this->assertFalse(local_appcrue_is_apikey_valid('invalidkey'));
        $this->assertSame('invalidkey', get_config('local_appcrue', 'api_key_attempt'));
    }
}
