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
 * AppCrue message plugin version information.
 *
 * @package local_appcrue
 * @category admin
 * @author  Juan Pablo de Castro
 * @copyright 2021 onwards juanpablo.decastro@uva.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'AppCrue connection services';
$string['idpheader'] = 'Verification of tokens';
$string['idpheader_help'] = 'AppCrue token verification service configuration';
$string['idp_token_url'] = 'URL tokenof the IDP';
$string['idp_token_url_help'] = 'URL token of the IDP to invoke the usertoken service.';
$string['idp_url'] = 'URL of the IDP';
$string['idp_url_help'] = 'URL of the IDP to invoke the usertoken service.';
$string['idp_client_id'] = 'Clientid for the IDP';
$string['idp_client_id_help'] = 'Client id for the IDP to invoke the usertoken service.';
$string['idp_client_secret'] = 'Client secret for the IDP';
$string['idp_client_secret_help'] = 'Client secret for the IDP to invoke the usertoken service.';
$string['api_key'] = 'API key';
$string['api_key_help'] = 'API key to access the AppCrue services. It is used to identify the app that is invoking the service.';

$string['cachedef_sitemaps'] = 'Stores json maps of categories and courses';
$string['calendarheader'] = 'User calendar';
$string['calendarheader_help'] = 'Integration of the user\'s calendar in AppCrue';
$string['enable_calendar'] = 'Enable calendar service';
$string['enable_calendar_help'] = 'Calendar service checks a personal token and generates a JSON representation of user events.';
$string['share_site_events'] = 'Return the institution\'s events';
$string['share_site_events_help'] = 'Devolver los eventos de la institución';
$string['share_course_events'] = 'Return course events';
$string['share_course_events_help'] = 'Devolver los eventos de los cursos';
$string['share_user_events'] = 'Return personal events';
$string['share_user_events_help'] = 'Devolver los eventos personales';
$string['examen_event_type'] = '"Exam" activities.';
$string['examen_event_type_help'] = 'Activities that may generate "Exam events" in the calendar';
$string['event_imgdetail'] = 'Default image';
$string['event_imgdetail_help'] = 'Default image associated with the event. It is used for decorating the user interface.';
$string['privacy:metadata'] = 'The "AppCrue connection services" plugin does not store any personal data.';

$string['autologinheader'] = 'Auto-login service';
$string['autologinheader_help'] = 'Allows the users to jump into Moodle from the AppCrue.';
$string['enable_autologin'] = 'Enable autologin';
$string['enable_autologin_help'] = 'Autologin service takes a user\'s token, verifies itm log-in him and redirects to an internal url.';
$string['allow_continue'] = 'Allow continue as guest';
$string['continue_not_allowed'] = 'fallback=continue not allowed';
$string['allow_continue_help'] = 'Allows fallback=continue that does not error when the token is not valid. It logon the user as guest and redirects to the URL anyway.';
$string['pattern_lib'] = 'List of URL pattens to generate redirect urls.';
$string['pattern_lib_help'] = 'List of URL pattens to generate redirect urls. Each line defines a pattern that can be requested for generating redirect urls. The allowed parameters are: course, group, param1, param2';
$string['match_user_by'] = 'Field for matching user\'s profile';
$string['match_user_by_help'] = 'The authorization token returns a identifcation that need to be matched to a user field.';
$string['idp_user_json_path'] = 'Selector in the IDP response.';
$string['idp_user_json_path_help'] = 'Selector like jsonpath to identify the value to identify the user.';
$string['course_pattern'] = 'SQL pattern to search a course with';
$string['course_pattern_help'] = 'The SQL pattern will be used to search a course using "idnumber" field and request parameters "course, group, param1, param2, param3" (not neccessarily moodle ids)';
$string['cachedef_sitemap'] = 'Stores json maps of categories and courses';
$string['avatarheader'] = 'Avatar service';
$string['avatarheader_help'] = 'Allows any app with a valid token to get the picture of the user.';
$string['enable_avatar'] = 'Enable avatar service';
$string['enable_avatar_help'] = 'Avatar service takes a user\'s token, verifies it and return the user picture.';
$string['sitemapheader'] = 'Sitemap service';
$string['sitemapheader_help'] = 'Generates a JSON structure of categories and courses with many options.';
$string['enable_sitemap'] = 'Enable sitemap service';
$string['enable_sitemap_help'] = 'Sitemap service generates a JSON representation of the categories and courses.';
$string['cache_sitemap'] = 'Cache sitemaps';
$string['cache_sitemap_help'] = 'Use Moodle cache system with SiteMaps.';
$string['cache_sitemap_ttl'] = 'Cache sitemaps TTL';
$string['cache_sitemap_ttl_help'] = 'Time to live for the sitemap cache.';

$string['new_grade_message'] = '### New grade for {$a->subjectname}'. "\n" . 'Your grade is {$a->gradealpha} ({$a->grade}).' . "\n" . '{$a->revdateformat}.' . "\n" . '{$a->comment}';
$string['notify_grade_revdate_null'] = 'Review date is not specified.';
$string['notify_grade_revdate'] = 'The review will be next {$a->revdateformat}';
$string['notify_grade_header'] = 'Web service for notifying grades';
$string['notify_grade_header_help'] = 'This web service allows to notify grades to students.';
$string['notify_grade_sender'] = 'Sender';
$string['notify_grade_sender_help'] = 'Sender of the message.';
$string['notify_grade_anyteacher'] = 'Any teacher';
$string['notify_grade_webserviceuser'] = 'User that invokes the web service';