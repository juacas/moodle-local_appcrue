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
 * AppCrue services plugin version information.
 *
 * @package local_appcrue
 * @category admin
 * @author  Juan Pablo de Castro, Alberto Otero Mato
 * @copyright 2021 onwards juanpablo.decastro@uva.es, alberto.otero@altia.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_appcrue\appcrue_service;

if ($hassiteconfig) {
    global $CFG;
    $settings = new admin_settingpage('local_appcrue', get_string('pluginname', 'local_appcrue'));
    $ADMIN->add('localplugins', $settings);
    /**
     * AppCRUE release information.
     */
    $installedversion = get_config('local_appcrue', 'version');
    $settings->add(new admin_setting_description(
        'local_appcrue/releaseinfo',
        get_string('pluginname', 'local_appcrue'),
        get_string('welcome_message', 'local_appcrue', ['installedversion' => $installedversion])
    ));
    // Switch to activate autoconfiguration mode of the APIKey.
    // If activated  the next apikey request will set the api_key.
    // After a new API key is stored, the switch will be disabled.
    $settings->add(
        new admin_setting_configcheckbox(
            'local_appcrue/lmsappcrue_enable_autoconfig',
            get_string('lmsappcrue:enable_autoconfig_appcrue', 'local_appcrue'),
            get_string('lmsappcrue:enable_autoconfig_appcrue_help', 'local_appcrue'),
            false
        )
    );

    // API KEY for directa access without token.
    // Generate a default one as an example.
    if (!get_config('local_appcrue', 'api_key')) {
        $defaultkey = bin2hex(random_bytes(16));
    } else {
        $defaultkey = "";
    }
    // If an invalid apikey was used, show it here for easy recovery.
    $attemptkey = get_config('local_appcrue', 'api_key_attempt');
    $apikeyhelp = get_string('lmsappcrue:api_key_help', 'local_appcrue');
    if ($attemptkey) {
        $warning = get_string('lmsappcrue:api_key_warning', 'local_appcrue', $attemptkey);
        $apikeyhelp .= '<div class="alert alert-danger">' .
                       $warning .
                       '</div>';
        core\notification::error($warning);
    }
    $rotatedate = get_config('local_appcrue', 'api_key_last_rotation');
    if ($rotatedate) {
        // Bootstrap success notification about API key rotation with the date of the last rotation.
        $apikeyhelp .= '<div class="alert alert-success">' .
                       get_string('lmsappcrue:api_key_rotated', 'local_appcrue', userdate($rotatedate)) .
                       "</div>";
    }

    // API Key configuration.
    $settings->add(new admin_setting_configtext(
        'local_appcrue/api_key',
        get_string('lmsappcrue:api_key', 'local_appcrue'),
        $apikeyhelp,
        $defaultkey,
        PARAM_ALPHANUMEXT
    ));
    // Define network filtering.
    $settings->add(new admin_setting_configmixedhostiplist(
        'local_appcrue/api_authorized_networks',
        get_string('lmsappcrue:api_authorized_networks', 'local_appcrue'),
        get_string('lmsappcrue:api_authorized_networks_help', 'local_appcrue'),
        join("\n", appcrue_service::APPCRUE_SERVERS),
        PARAM_TEXT,
        60,
        2
    ));

    // API Rotation endpoint.
    $settings->add(
        new admin_setting_configcheckbox(
            'local_appcrue/lmsappcrue_enable_keyrotation',
            get_string('lmsappcrue:enable_api_key_rotation', 'local_appcrue'),
            get_string('lmsappcrue:enable_api_key_rotation_help', 'local_appcrue'),
            false
        )
    );
    $settings->add(
        new admin_setting_heading(
            'local_appcrue/idp_header',
            get_string('idp:header', 'local_appcrue'),
            get_string('idp:header_help', 'local_appcrue')
        )
    );
    // IdP configuration.
    // By default use AppCRUE IdP service.

    // Offer to use custom IdP.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/use_custom_idp',
        get_string('idp:use_custom_idp', 'local_appcrue'),
        get_string('idp:use_custom_idp_help', 'local_appcrue'),
        false
    ));
    // Appcrue AppId.
    $settings->add(new admin_setting_configtext(
        'local_appcrue/appcrue_appid',
        get_string('idp:client_id', 'local_appcrue'),
        get_string('idp:client_id_help', 'local_appcrue'),
        'MoodleAppCrue',
        PARAM_ALPHANUMEXT
    ));
    $settings->hide_if('local_appcrue/appcrue_appid', 'local_appcrue/use_custom_idp', 'eq', 1);
    // Appcrue API Token.
    $settings->add(new admin_setting_configtext(
        'local_appcrue/appcrue_apptoken',
        get_string('idp:client_secret', 'local_appcrue'),
        get_string('idp:client_secret_help', 'local_appcrue'),
        '',
        PARAM_ALPHANUMEXT
    ));
    $settings->hide_if('local_appcrue/appcrue_apptoken', 'local_appcrue/use_custom_idp', 'eq', 1);

    $settings->add(new admin_setting_configtext(
        'local_appcrue/idp_token_url',
        get_string('idp:token_url', 'local_appcrue'),
        get_string('idp:token_url_help', 'local_appcrue'),
        'https://idp.uva.es/api/adas/oauth2/tokendata',
        PARAM_URL
    ));
    // Hide if not using custom IdP.
    $settings->hide_if('local_appcrue/idp_token_url', 'local_appcrue/use_custom_idp', 'eq', 0);

    // Select mapping field from json.
    $settings->add(new admin_setting_configtext(
        'local_appcrue/idp_user_json_path',
        get_string('idp:user_json_path', 'local_appcrue'),
        get_string('idp:user_json_path_help', 'local_appcrue'),
        '.username',
        PARAM_RAW_TRIMMED
    ));

    // Select mapping field in user's profile.
    $fields = get_user_fieldnames();
    require_once($CFG->dirroot . '/user/profile/lib.php');
    $customfields = profile_get_custom_fields();
    $userfields = [];
    // Make the keys string values and not indexes.
    foreach ($fields as $field) {
        $userfields[$field] = $field;
    }
    foreach ($customfields as $field) {
        $userfields["profile_field_{$field->shortname}"] = $field->name;
    }
    $settings->add(new admin_setting_configselect(
        'local_appcrue/match_user_by',
        get_string('match_user_by', 'local_appcrue'),
        get_string('match_user_by_help', 'local_appcrue'),
        'id',
        $userfields
    ));

    // Autologin.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_autologin_header',
            get_string('autologinheader', 'local_appcrue'),
            get_string('autologinheader_help', 'local_appcrue')
        )
    );
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/enable_autologin',
        get_string('enable_autologin', 'local_appcrue'),
        get_string('enable_autologin_help', 'local_appcrue'),
        false
    ));
    // Select "bearer" or "token" mark in deep urls.
    // Note: As february 2026, Android and iOS CustomTabs only support "token" mark.
    $tokenmarksetting = new admin_setting_configselect(
        'local_appcrue/deep_url_token_mark',
        get_string('autologin:deep_url_token_mark', 'local_appcrue'),
        get_string('autologin:deep_url_token_mark_help', 'local_appcrue'),
        'appcrue_token',
        [
            '' => get_string('autologin:deep_url_token_mark_disabled', 'local_appcrue'),
            // phpcs:ignore
            // Temporally disabled: 'appcrue_bearer' => get_string('autologin:deep_url_token_mark_appcruebearer', 'local_appcrue'),
            'appcrue_token' => get_string('autologin:deep_url_token_mark_appcruetoken', 'local_appcrue'),
            // phpcs:ignore
            // Temporally disabled: 'bearer' => get_string('autologin:deep_url_token_mark_bearer', 'local_appcrue'),
            'token' => get_string('autologin:deep_url_token_mark_token', 'local_appcrue'),
        ]
    );
    // Add validation rule: bearer and token can be used only if custom IdP is not used.
    $tokenmarksetting->set_validate_function(function ($value) {
        // If custom IdP is enabled, disallow the generic 'bearer'/'token' marks.
        if (get_config('local_appcrue', 'use_custom_idp') == false && in_array($value, ['bearer', 'token'], true)) {
            return get_string('autologin:err_deepurltokenmark_customidp', 'local_appcrue');
        }
        return "";
    });
    $settings->add($tokenmarksetting);

    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/allow_continue',
        get_string('allow_continue', 'local_appcrue'),
        get_string('allow_continue_help', 'local_appcrue'),
        true
    ));
    // Check for forcing page redirection.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/use_redirection_page',
        get_string('autologin:use_redirection_page', 'local_appcrue'),
        get_string('autologin:use_redirection_page_help', 'local_appcrue'),
        false
    ));

    $settings->add(new admin_setting_configtext(
        'local_appcrue/course_pattern',
        get_string('course_pattern', 'local_appcrue'),
        get_string('course_pattern_help', 'local_appcrue'),
        '%-{course}-{group}-%',
        PARAM_RAW_TRIMMED
    ));
    // Select whether to follow metacourses.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/follow_metacourses',
        get_string('autologin:follow_metacourses', 'local_appcrue'),
        get_string('autologin:follow_metacourses_help', 'local_appcrue'),
        false
    ));
    $settings->add(new admin_setting_configtextarea(
        'local_appcrue/pattern_lib',
        get_string('pattern_lib', 'local_appcrue'),
        get_string('pattern_lib_help', 'local_appcrue'),
        "course=/course/view.php?id={course}\nguia=https://docserver/grades/{param1}/{param2}/{course}/doc.pdf",
        PARAM_RAW_TRIMMED
    ));
    // Avatar service.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_avatar_header',
            get_string('avatarheader', 'local_appcrue'),
            get_string('avatarheader_help', 'local_appcrue')
        )
    );
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/enable_avatar',
        get_string('enable_avatar', 'local_appcrue'),
        get_string('enable_avatar_help', 'local_appcrue'),
        false
    ));
    // SiteMap service.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_sitemap_header',
            get_string('sitemapheader', 'local_appcrue'),
            get_string('sitemapheader_help', 'local_appcrue')
        )
    );
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/enable_sitemap',
        get_string('enable_sitemap', 'local_appcrue'),
        get_string('enable_sitemap_help', 'local_appcrue'),
        false
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/cache_sitemap',
        get_string('cache_sitemap', 'local_appcrue'),
        get_string('cache_sitemap_help', 'local_appcrue'),
        true
    ));
    $settings->add(new admin_setting_configduration(
        'local_appcrue/cache_sitemap_ttl',
        get_string('cache_sitemap_ttl', 'local_appcrue'),
        get_string('cache_sitemap_ttl_help', 'local_appcrue'),
        60 * 60,
        3600
    ));
     // Notify grade Service.
     $settings->add(
         new admin_setting_heading(
             'local_appcrue_notifygrade_header',
             get_string('notify_grade_header', 'local_appcrue'),
             get_string('notify_grade_header_help', 'local_appcrue')
         )
     );
    $settings->add(new admin_setting_configselect(
        'local_appcrue/notify_grade_sender',
        get_string('notify_grade_sender', 'local_appcrue'),
        get_string('notify_grade_sender_help', 'local_appcrue'),
        'id',
        ['anyteacher' => get_string('notify_grade_anyteacher', 'local_appcrue'),
        'webserviceuser' => get_string('notify_grade_webserviceuser', 'local_appcrue'),
        ]
    ));
    // Section for LMS AppCRUE integration.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_lms_header',
            get_string('lmsappcrue:header', 'local_appcrue'),
            get_string('lmsappcrue:header_help', 'local_appcrue')
        )
    );
    // Select appcrue field to search the user.
    $paramoptions = [
        'email' => get_string('email'),
        'username' => get_string('username'),
    ];
    $settings->add(new admin_setting_configselect(
        'local_appcrue/lmsappcrue_use_user_param',
        get_string('lmsappcrue:use_user_param', 'local_appcrue'),
        get_string('lmsappcrue:use_user_param_help', 'local_appcrue'),
        'email',
        $paramoptions
    ));
    // Select Moodle's mapping field.
    $settings->add(new admin_setting_configselect(
        'local_appcrue/lmsappcrue_match_user_by',
        get_string('lmsappcrue:match_user_by', 'local_appcrue'),
        get_string('lmsappcrue:match_user_by_help', 'local_appcrue'),
        'email',
        $userfields
    ));
    global $DB;
    $modules = $DB->get_records("modules");
    $modulelist = [];
    foreach ($modules as $mod) {
        $modulelist[$mod->name] = get_string("modulename", "$mod->name", null, true);
    }
    uasort($modulelist, function ($a, $b) {
        return strcmp($a, $b);
    });
    $settings->add(new admin_setting_configmultiselect(
        'local_appcrue/calendar_examen_event_type',
        get_string('calendar:examen_event_type', 'local_appcrue'),
        get_string('calendar:examen_event_type_help', 'local_appcrue'),
        ['quiz', 'quest', 'assign', 'workshop'],
        $modulelist
    ));
    // Calendar.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_calendar_header',
            get_string('calendarheader', 'local_appcrue'),
            get_string('calendarheader_help', 'local_appcrue')
        )
    );
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/enable_usercalendar',
        get_string('calendar:enable_calendar', 'local_appcrue'),
        get_string('calendar:enable_calendar_help', 'local_appcrue'),
        false
    ));
    // Enable LMS AppCRUE calendar endpoint.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/lmsappcrue_enable_calendar',
        get_string('lmsappcrue:calendar:enable_calendar', 'local_appcrue'),
        get_string('lmsappcrue:calendar:enable_calendar_help', 'local_appcrue'),
        defaultsetting: false
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/calendar_share_site_events',
        get_string('calendar:share_site_events', 'local_appcrue'),
        get_string('calendar:share_site_events_help', 'local_appcrue'),
        true
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/calendar_share_course_events',
        get_string('calendar:share_course_events', 'local_appcrue'),
        get_string('calendar:share_course_events_help', 'local_appcrue'),
        true
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/share_personal_events',
        get_string('calendar:share_user_events', 'local_appcrue'),
        get_string('calendar:share_user_events_help', 'local_appcrue'),
        true
    ));

    $settings->add(new admin_setting_configtext(
        'local_appcrue/calendar_event_imgdetail',
        get_string('calendar:event_imgdetail', 'local_appcrue'),
        get_string('calendar:event_imgdetail_help', 'local_appcrue'),
        '',
        PARAM_URL
    ));
    // LMS AppCRUE grades endpoint.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_lms_grades_header',
            get_string('lmsappcrue:grades', 'local_appcrue'),
            get_string('lmsappcrue:grades_help', 'local_appcrue')
        )
    );

    // Enable LMS AppCRUE grades endpoint.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/lmsappcrue_enable_grades',
        get_string('lmsappcrue:enable_grades', 'local_appcrue'),
        get_string('lmsappcrue:enable_grades_help', 'local_appcrue'),
        false
    ));

    // Select to show total grade as final or not-final grade.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/lmsappcrue_show_total_grade_as_final',
        get_string('lmsappcrue:show_total_grade_as_final', 'local_appcrue'),
        get_string('lmsappcrue:show_total_grade_as_final_help', 'local_appcrue'),
        true
    ));

    // LMS AppCRUE forums section.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_lms_forums_header',
            get_string('lmsappcrue:forums', 'local_appcrue'),
            get_string('lmsappcrue:forums_help', 'local_appcrue')
        )
    );
    // Enable LMS AppCRUE forums endpoint.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/lmsappcrue_enable_forums',
        get_string('lmsappcrue:enable_forums', 'local_appcrue'),
        get_string('lmsappcrue:enable_forums_help', 'local_appcrue'),
        false
    ));
    // Time window for forums.
    $settings->add(new admin_setting_configduration(
        'local_appcrue/lmsappcrue_forums_timewindow',
        get_string('lmsappcrue:forums_timewindow', 'local_appcrue'),
        get_string('lmsappcrue:forums_timewindow_help', 'local_appcrue'),
        WEEKSECS * 4 * 6, // Default 6 months.
        WEEKSECS * 4
    ));
    // Enable LMS AppCRUE announcements endpoint.
    $settings->add(new admin_setting_heading(
        'local_appcrue_lms_announcements_header',
        get_string('lmsappcrue:announcements', 'local_appcrue'),
        get_string('lmsappcrue:announcements_help', 'local_appcrue')
    ));
    // Enable LMS AppCRUE announcements endpoint.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/lmsappcrue_enable_announcements',
        get_string('lmsappcrue:enable_announcements', 'local_appcrue'),
        get_string('lmsappcrue:enable_announcements_help', 'local_appcrue'),
        false
    ));
    // LMS AppCRUE files section.
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_lms_files_header',
            get_string('lmsappcrue:files', 'local_appcrue'),
            get_string('lmsappcrue:files_help', 'local_appcrue')
        )
    );
    // Enable LMS AppCRUE files endpoint.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/lmsappcrue_enable_files',
        get_string('lmsappcrue:enable_files', 'local_appcrue'),
        get_string('lmsappcrue:enable_files_help', 'local_appcrue'),
        false
    ));
    // Include legacy course files.
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/includelegacyfiles',
        get_string('lmsappcrue:include_legacy_files', 'local_appcrue'),
        get_string('lmsappcrue:include_legacy_files_help', 'local_appcrue'),
        false
    ));
    // LMS AppCRUE assignments endpoint.
    $settings->add(new admin_setting_heading(
        'local_appcrue_lms_assignments_header',
        get_string('lmsappcrue:assignments', 'local_appcrue'),
        get_string('lmsappcrue:assignments_help', 'local_appcrue')
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_appcrue/lmsappcrue_enable_assignments',
        get_string('lmsappcrue:enable_assignments', 'local_appcrue'),
        get_string('lmsappcrue:enable_assignments_help', 'local_appcrue'),
        false
    ));
    // Mapping activities types to start dates.
    $settingstartdate = new admin_setting_configtextarea(
        'local_appcrue/assignments_dates',
        get_string('assignments:dates', 'local_appcrue'),
        get_string('assignments:dates_desc', 'local_appcrue'),
        'mod_assign|assign|allowsubmissionsfromdate|duedate
mod_bigbluebuttonbn|bigbluebuttonbn|openingtime
mod_chat|chat|chattime|
mod_choice|choice|timeopen|timeclose
mod_data|data|timeavailablefrom|timeavailableto
mod_feedback|feedback|timeopen|timeclose
mod_forum|forum|duedate|cutoffdate
mod_glossary|glossary|assesstimestart|assesstimefinish
mod_lesson|lesson|available|deadline
mod_quiz|quiz|timeopen|timeclose
mod_scorm|scorm|timeopen|timeclose
mod_workshop|workshop|submissionstart|submissionend',
        PARAM_RAW,
        10,
        15
    );
    $settings->add($settingstartdate);
}
