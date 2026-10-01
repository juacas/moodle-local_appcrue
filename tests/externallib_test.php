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
require_once(__DIR__ . '/appcrue_test_base.php');
require_once($CFG->dirroot . '/local/appcrue/externallib.php');

/**
 * Tests for the AppCrue external web-service functions.
 *
 * @package local_appcrue
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\local_appcrue_external::class)]
final class externallib_test extends appcrue_test_base {
    /**
     * Message parameters expose the expected user key and format defaults.
     */
    public function test_send_instant_message_parameters_validate_defaults(): void {
        $description = \local_appcrue_external::send_instant_message_parameters();
        $validated = \core_external\external_api::validate_parameters(
            $description,
            ['touserkey' => 'student01', 'text' => 'Hello']
        );

        $this->assertSame('student01', $validated['touserkey']);
        $this->assertSame('Hello', $validated['text']);
        $this->assertEquals(FORMAT_MOODLE, $validated['textformat']);
        $this->assertNull($validated['field']);
    }

    /**
     * Batch message parameters accept client ids and multiple recipients.
     */
    public function test_send_instant_messages_parameters_validate_batch(): void {
        $description = \local_appcrue_external::send_instant_messages_parameters();
        $validated = \core_external\external_api::validate_parameters(
            $description,
            [
                'messages' => [[
                    'touserkey' => 'student01',
                    'text' => 'Hello',
                    'textformat' => FORMAT_PLAIN,
                    'clientmsgid' => 'client1',
                ]],
            ]
        );

        $this->assertCount(1, $validated['messages']);
        $this->assertSame('client1', $validated['messages'][0]['clientmsgid']);
        $this->assertNull($validated['field']);
    }

    /**
     * The single-message wrapper resolves the configured user field and sends a message.
     */
    public function test_send_instant_message_resolves_recipient(): void {
        global $CFG;
        $this->setAdminUser();
        $recipient = self::getDataGenerator()->create_user(['username' => 'message_recipient']);
        set_config('match_user_by', 'username', 'local_appcrue');
        $previousmessaging = $CFG->messaging;
        $CFG->messaging = 1;

        try {
            $result = \local_appcrue_external::send_instant_message(
                $recipient->username,
                'AppCrue test message',
                FORMAT_PLAIN,
                'username'
            );
        } finally {
            $CFG->messaging = $previousmessaging;
        }

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('msgid', $result[0]);
        $this->assertGreaterThan(0, $result[0]['msgid']);
    }

    /**
     * Unknown recipients are rejected with the AppCrue not-enrolled error code.
     */
    public function test_send_instant_message_rejects_unknown_recipient(): void {
        $this->expectExceptionCode(appcrue_service::USER_NOT_ENROLLED);

        \local_appcrue_external::send_instant_message('does-not-exist', 'Hello', FORMAT_PLAIN, 'username');
    }

    /**
     * Grade notification parameters validate the full webhook payload.
     */
    public function test_notify_grade_parameters_validate_payload(): void {
        $description = \local_appcrue_external::notify_grade_parameters();
        $params = [
            'idusuario' => 'student01',
            'nip' => 'nip01',
            'useremail' => 'student@example.com',
            'subject' => 'SUBJ',
            'subjectname' => 'Subject',
            'course' => '2026',
            'grade' => '8.5',
            'comment' => 'Good work',
        ];

        $validated = \core_external\external_api::validate_parameters($description, $params);

        $this->assertSame('SUBJ', $validated['subject']);
        $this->assertSame('Subject', $validated['subjectname']);
        $this->assertNull($validated['group']);
        $this->assertNull($validated['revdate']);
    }

    /**
     * Return descriptions are available for all declared web-service methods.
     */
    public function test_external_return_descriptions_are_defined(): void {
        $this->assertNotNull(\local_appcrue_external::send_instant_messages_returns());
        $this->assertNotNull(\local_appcrue_external::send_instant_message_returns());
        $this->assertNotNull(\local_appcrue_external::notify_grade_returns());
    }
}
