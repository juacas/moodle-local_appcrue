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
 * @author  Juan Pablo de Castro
 * @copyright 2021 onwards juanpablo.decastro@uva.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_appcrue', get_string('pluginname', 'local_appcrue'));
    $ADMIN->add('localplugins', $settings);
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_idp_header',
            get_string('idpheader', 'local_appcrue'),
            get_string('idpheader_help', 'local_appcrue')
        )
    );
    $settings->add(new admin_setting_configtext('local_appcrue_idp_url', get_string('idp_url', 'local_appcrue'), get_string('idp_url_help', 'local_appcrue'), 'https://idp.uva.es/api/adas/oauth2/tokendata', PARAM_URL));
    $settings->add(new admin_setting_configtext('local_appcrue_idp_token_url', get_string('idp_token_url', 'local_appcrue'), get_string('idp_token_url_help', 'local_appcrue'), 'https://idp.uva.es/OAUTH2/authserver.php', PARAM_URL));
    $settings->add(new admin_setting_configtext('local_appcrue_idp_client_id', get_string('idp_client_id', 'local_appcrue'), get_string('idp_client_id_help', 'local_appcrue'), '', PARAM_RAW_TRIMMED));
    $settings->add(new admin_setting_configtext('local_appcrue_idp_client_secret', get_string('idp_client_secret', 'local_appcrue'), get_string('idp_client_secret_help', 'local_appcrue'), '', PARAM_RAW_TRIMMED));
    $settings->add(
        new admin_setting_heading(
            'local_appcrue_calendar_header',
            get_string('calendarheader', 'local_appcrue'),
            get_string('calendarheader_help', 'local_appcrue')
        )
    );
    $settings->add(new admin_setting_configcheckbox(
                        'local_appcrue_share_site_events',
                        get_string('share_site_events', 'local_appcrue'),
                        get_string('share_site_events_help', 'local_appcrue'), true));
    $settings->add(new admin_setting_configcheckbox(
                        'local_appcrue_share_course_events',
                        get_string('share_course_events', 'local_appcrue'),
                        get_string('share_course_events_help', 'local_appcrue'), true));
    $settings->add(new admin_setting_configcheckbox('local_appcrue_share_personal_events',
                        get_string('share_user_events', 'local_appcrue'),
                        get_string('share_user_events_help', 'local_appcrue'), true));
}
