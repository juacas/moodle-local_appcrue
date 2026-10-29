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
 * @author  Juan Pablo de Castro, Alberto Otero Mato
 * @copyright 2021 onwards juanpablo.decastro@uva.es, alberto.otero@altia.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['allow_continue'] = 'Allow continue as guest';
$string['allow_continue_help'] = 'Allows fallback=continue that does not error when the token is not valid. It logs in the user as guest and redirects to the URL anyway.';
$string['assignments:dates'] = 'Assignment dates';
$string['assignments:dates_desc'] = 'Map each activity type considered as an assignment with the dates fields (due date, cut-off date, etc.) in the database. In format "mod_activityname|table|duedate|cutoffdate".';
$string['autologin:deep_url_token_mark'] = 'Autologin URL token mark';
$string['autologin:deep_url_token_mark_appcruebearer'] = 'AppCRUE session token in Auth-Bearer header';
$string['autologin:deep_url_token_mark_appcruetoken'] = 'AppCRUE session token in URL query parameter';
$string['autologin:deep_url_token_mark_bearer'] = 'University\'s token in Auth-Bearer header';
$string['autologin:deep_url_token_mark_disabled'] = 'No autologin URLs';
$string['autologin:deep_url_token_mark_help'] = 'The token mark to use in the deep URLs: "token" tells the app to use the token in the URL as a query parameter. "bearer" tells the app to use the token in the Authorization header.';
$string['autologin:deep_url_token_mark_token'] = 'Univesrsity\'s token in URL query parameter';
$string['autologin:err_deepurltokenmark_customidp'] = 'The selected token mark is not compatible with the AppCRUE autologin configuration. Please select a AppCRUE tokens only.';
$string['autologin:follow_metacourses'] = 'Follow metacourses';
$string['autologin:follow_metacourses_help'] = 'If enabled, when sarching courses by pattern, metacourses will be followed to go to the "parent" course where the user is meta-enrolled. If the course is meta-enrolled in more than one parent course, no redirection will be done.';
$string['autologin:loggedasguest'] = 'You have been logged in as guest in {$a->sitename} from the App. This usuaally means that your token was not valid, so you have limited access to the site. Please click the following link to continue.';
$string['autologin:loggedasuser'] = 'You have been logged in as {$a->fullname} in {$a->sitename} from the App.';
$string['autologin:mfa_exclusion_notice'] = 'An MFA exception for AppCRUE has been added to your <a href="{$a}">Moodle configuration</a>.';
$string['autologin:notauthenticated'] = 'Redirection not authorized. Try to reopen the session in the App and try again. If the problem persists, contact the site administrator.';
$string['autologin:redirecting'] = 'Redirecting to the site...';
$string['autologin:use_redirection_page'] = 'Use a redirection page instead of HTTP 303';
$string['autologin:use_redirection_page_help'] = 'If enabled, autologin will use a redirection page instead of an HTTP 303 redirect. ';
$string['autologinheader'] = 'Auto-login service';
$string['autologinheader_help'] = 'Allows the users to jump into Moodle from the AppCrue.';
$string['avatarheader'] = 'Avatar service';
$string['avatarheader_help'] = 'Allows any app with a valid token to get the picture of the user.';
$string['cache_sitemap'] = 'Cache sitemaps';
$string['cache_sitemap_help'] = 'Use Moodle cache system with SiteMaps.';
$string['cache_sitemap_ttl'] = 'Cache sitemaps TTL';
$string['cache_sitemap_ttl_help'] = 'Time to live for the sitemap cache.';
$string['cachedef_sitemap'] = 'Stores json maps of categories and courses';
$string['cachedef_sitemaps'] = 'Stores json maps of categories and courses';
$string['calendar:enable_calendar'] = 'Enable legacy calendar service';
$string['calendar:enable_calendar_help'] = 'Calendar service checks a <b>personal token</b> and generates a JSON representation of user events.';
$string['calendar:event_imgdetail'] = 'Default image';
$string['calendar:event_imgdetail_help'] = 'Default image associated with the event. It is used for decorating the user interface.';
$string['calendar:examen_event_type'] = '"Exam" activities.';
$string['calendar:examen_event_type_help'] = 'Activities that may generate "Exam events" in the "user calendar" and in "Assignments LMS API".';
$string['calendar:share_course_events'] = 'Return course events';
$string['calendar:share_course_events_help'] = 'Include course-level events in the response.';
$string['calendar:share_site_events'] = 'Return the institution\'s events';
$string['calendar:share_site_events_help'] = 'Include institution\'s events in the response.';
$string['calendar:share_user_events'] = 'Return personal events';
$string['calendar:share_user_events_help'] = 'Include personal events in the response.';
$string['calendarheader'] = 'User calendar';
$string['calendarheader_help'] = 'Integration of the user\'s calendar in AppCrue by the legacy API (token-based) or the LMS-AppCRUE (Api key based)';
$string['continue_not_allowed'] = 'fallback=continue not allowed. {$a}';
$string['course_pattern'] = 'SQL pattern to search a course with';
$string['course_pattern_help'] = 'The SQL pattern will be used to search a course by the "idnumber" field and request parameters "course, group, param1, param2, param3" (not necessarily moodle IDs)';
$string['enable_autologin'] = 'Enable autologin';
$string['enable_autologin_help'] = 'Autologin service takes a user\'s token, verifies it, logs in the user and redirects to an internal URL.';
$string['enable_avatar'] = 'Enable avatar service';
$string['enable_avatar_help'] = 'Avatar service takes a user\'s token, verifies it and returns the user picture.';
$string['enable_sitemap'] = 'Enable sitemap service';
$string['enable_sitemap_help'] = 'Sitemap service generates a JSON representation of the categories and courses.';
$string['event_autologin_failed_desc'] = 'Failed autologin attempt for token {$a->token} from IP address {$a->ipaddress}. Cause: {$a->diagnosis}';
$string['event_autologin_failed_name'] = 'Autologin Failed Attempt';
$string['event_autologin_login_desc'] = 'User with id {$a->userid} logged in using autologin with valid token.';
$string['event_autologin_login_name'] = 'Autologin Login';
$string['event_bad_apikey_failed_desc'] = 'Bad API key attempt with key {$a->apikey} from IP address {$a->ipaddress}';
$string['event_bad_apikey_failed_name'] = 'Bad API Key Attempt';
$string['event_unauthorized_ip_failed_desc'] = 'Access from non-whitelisted IP address {$a->ipaddress}';
$string['event_unauthorized_ip_failed_name'] = 'Unauthorized IP Access Attempt';
    'Mark this option if your server has issues with recognizing new sessions in redirects.';
$string['idp:client_id'] = 'import_code of the external API AppCRUE';
$string['idp:client_id_help'] = 'External API "import_code" provided AppCrue. Used to invoke the user token service.';
$string['idp:client_secret'] = 'Token of the external API AppCRUE';
$string['idp:client_secret_help'] = 'External API "token" provided by AppCrue. Used to invoke the user token service.';
$string['idp:header'] = 'Verification of tokens';
$string['idp:header_help'] = 'AppCrue token verification configuration. This functionality is only for institutions that use autologin links. ';
$string['idp:token_url'] = 'Endpoint for token resolution';
$string['idp:token_url_help'] = 'URL of the IDP service to resolve user identity from the token.';
$string['idp:url'] = 'URL of the IDP';
$string['idp:url_help'] = 'URL of the IDP to invoke the user token service.';
$string['idp:use_custom_idp'] = 'Use custom IdP';
$string['idp:use_custom_idp_help'] = 'If enabled, a custom Identity Provider endpoint will be used to validate the tokens provided by AppCrue. The custom IdP must implement an OAuth2 token introspection endpoint that returns user identity information in JSON format. ' .
                                     'If disabled, the default AppCrue IdP will be used. AppCRUE service will return user fields: .id, .username, .email, .document, .nia.';
$string['idp:use_pre_server'] = 'Use PRE server';
$string['idp:use_pre_server_help'] = 'If enabled, tokens will be validated using the Universia pre-production server. If disabled, the production server will be used. This setting only applies when a custom IdP is not used.';
$string['idp:user_json_path'] = 'Selector in the IDP response.';
$string['idp:user_json_path_help'] = 'Selector like jsonpath to identify the value to identify the user. Values for AppCrue Idp can be ".id", ".username", ".email", ".document", ".nia".';

$string['lmsappcrue:announcements'] = 'AppCRUE announcements';
$string['lmsappcrue:announcements_help'] = 'Expose the user announcements to AppCRUE via LMS-AppCRUE API (with Apikey).';
$string['lmsappcrue:announcements_timewindow'] = 'Time window for announcements';
$string['lmsappcrue:announcements_timewindow_help'] = 'Time window for retrieving announcements. It is used to limit the number of announcements returned by the service. Set to 0 to include all announcements regardless of time.';
$string['lmsappcrue:api_authorized_networks'] = 'API authorized networks';
$string['lmsappcrue:api_authorized_networks_help'] = 'List of networks that are allowed to access the LMS API. Each line with an IP address or CIDR notation.';
$string['lmsappcrue:api_key'] = 'Local API key';
$string['lmsappcrue:api_key_help'] = 'API key to access this AppCrue services from outside. It is used to identify the app that is invoking the service without user tokens.';
$string['lmsappcrue:api_key_rotated'] = 'API key rotated at {$a}';
$string['lmsappcrue:api_key_warning'] = 'AppCRUE: A remote system used an unknown api_key "{$a}" while accessing the endpoints. If you are setting up the API key for the first time or have lost it, if you trust the services with access to the endpoint (e.g. due to network filters), you can use this value to configure it in the plugin settings.';
$string['lmsappcrue:assignments'] = 'AppCRUE assignments';
$string['lmsappcrue:assignments_help'] = 'Expose the user assignments to AppCRUE. This endpoint allows AppCRUE to retrieve the user assignments and their due dates and status.';
$string['lmsappcrue:assignments_timewindow'] = 'Time window for assignments';
$string['lmsappcrue:assignments_timewindow_help'] = 'Time window for retrieving assignments. Assignments with a due date older than this will be excluded. Set to 0 to include all assignments regardless of date.';
$string['lmsappcrue:calendar:enable_calendar'] = 'Enable calendar LMS-AppCRUE endpoint';
$string['lmsappcrue:calendar:enable_calendar_help'] = 'Enable calendar endpoint to share the user calendar with AppCRUE via LMS-APPCRUE API (with Apikey).';
$string['lmsappcrue:calendar:timewindow_after'] = 'Calendar default window after';
$string['lmsappcrue:calendar:timewindow_after_help'] = 'Time window after the reference point when the calendar endpoint does not receive timestart or timeend.';
$string['lmsappcrue:calendar:timewindow_before'] = 'Calendar default window before';
$string['lmsappcrue:calendar:timewindow_before_help'] = 'Time window before the reference point when the calendar endpoint does not receive timestart or timeend.';
$string['lmsappcrue:enable_announcements'] = 'Enable announcements endpoint';
$string['lmsappcrue:enable_announcements_help'] = 'Enable announcements endpoint to share the user announcements with AppCRUE.';
$string['lmsappcrue:enable_api_key_rotation'] = 'Enable API rotation endpoint';
$string['lmsappcrue:enable_api_key_rotation_help'] = 'API rotation endpoint allows AppCRUE to change periodically the API key used by AppCRUE.';
$string['lmsappcrue:enable_assignments'] = 'Enable assignments endpoint';
$string['lmsappcrue:enable_assignments_help'] = 'Enable assignments endpoint to share the user assignments with LMS-AppCRUE API (with API key).';
$string['lmsappcrue:enable_autoconfig_appcrue'] = 'Enable AppCRUE autoconfig procedure';
$string['lmsappcrue:enable_autoconfig_appcrue_help'] = '<p>The autoconfig procedure eases the initial connection setup with AppCRUE: ' .
    'It adds the official IPs of AppCRUE servers, enables the key rotation service, and stores the first API key received from AppCRUE.' .
    ' Only keys from official AppCRUE servers or those added by the local admin are accepted.</p><p> To complete the setup, some request from the LMS components of the AppCRUE mobile app must be made.</p>';
$string['lmsappcrue:enable_files'] = 'Enable files endpoint';
$string['lmsappcrue:enable_files_help'] = 'Enable files endpoint to notify files and share download links.';
$string['lmsappcrue:enable_forums'] = 'Enable forums endpoint';
$string['lmsappcrue:enable_forums_help'] = 'Enable forums endpoint to share the user forum conversations with AppCRUE.';
$string['lmsappcrue:enable_grades'] = 'Enable grades endpoint';
$string['lmsappcrue:enable_grades_help'] = 'Enable grades endpoint to share the user grades with LMS-AppCRUE API (with Apikey).';
$string['lmsappcrue:files'] = 'AppCRUE files';
$string['lmsappcrue:files_help'] = 'Expose the user files to LMS-AppCRUE API (with API key).';
$string['lmsappcrue:files_timewindow'] = 'Time window for files';
$string['lmsappcrue:files_timewindow_help'] = 'Time window for retrieving files. It is used to limit the number of files returned by the service. Set to 0 to include all files regardless of time.';
$string['lmsappcrue:forums'] = 'AppCRUE forums';
$string['lmsappcrue:forums_help'] = 'Expose the user forum conversations to LMS-AppCRUE API (with API key).';
$string['lmsappcrue:forums_timewindow'] = 'Time window for forums';
$string['lmsappcrue:forums_timewindow_help'] = 'Time window for retrieving forum posts. It is used to limit the number of posts returned by the service. Set to 0 to include all posts regardless of time.';
$string['lmsappcrue:grades'] = 'AppCRUE grades';
$string['lmsappcrue:grades_help'] = 'Expose the user grades to LMS-AppCRUE API (with API key).';
$string['lmsappcrue:grades_timewindow'] = 'Time window for grades';
$string['lmsappcrue:grades_timewindow_help'] = 'Time window for retrieving grades. It is used to limit the number of grades returned by the service. Set to 0 to include all grades regardless of time.';
$string['lmsappcrue:header'] = 'LMS-AppCRUE Widgets integration';
$string['lmsappcrue:header_help'] = 'LMS-AppCRUE Widgets integration service configuration. LMS-AppCRUE API is only called from AppCRUE\'s backend using a pre-shared API key.';
$string['lmsappcrue:include_legacy_files'] = 'Include legacy course files';
$string['lmsappcrue:include_legacy_files_help'] = 'If enabled, course files stored in the legacy course area will be included in the files endpoint. This files will be returned along with files from resources and folders to all users. It may be a security risk if legacy files are not properly managed.';
$string['lmsappcrue:internalerror'] = 'Internal error';
$string['lmsappcrue:invalidtimerange'] = 'Invalid time range';
$string['lmsappcrue:match_user_by'] = 'Field for matching user\'s profile';
$string['lmsappcrue:match_user_by_help'] = 'The parameter in the request is used to match the user\'s profile. This value selects what user profile field is used to match the user\'s profile.';
$string['lmsappcrue:show_total_grade_as_final'] = 'Show total grade as final grade';
$string['lmsappcrue:show_total_grade_as_final_help'] = 'If enabled, the total grade of the course will be reported as final grade in the grades endpoint. This is useful when the total grade in Moodle is not reliable due to the use of grade categories with aggregation methods that do not fit well with the grading system of AppCRUE. When enabled, the itemtype of the course total grade will be reported as "mod" instead of "course" to be properly shown by AppCRUE as a regular grade.';
$string['lmsappcrue:use_user_param'] = 'Use user parameter for matching';
$string['lmsappcrue:use_user_param_help'] = 'Appcrue sends "username" and "email" parameters in the request. The value of this parameter is used to match the user\'s profile selected in the "match_user_by" setting.';
$string['match_user_by'] = 'Field for matching user\'s profile';
$string['match_user_by_help'] = 'The authorization token returns an identification that needs to be matched to a user field.';
$string['missingtoken'] = 'Missing token';
$string['notify_grade_anyteacher'] = 'Any teacher';
$string['notify_grade_header'] = 'Web service for notifying grades';
$string['notify_grade_header_help'] = 'This web service allows to notify grades to students.';
$string['notify_grade_revdate'] = 'The review will be next {$a->revdateformat}';
$string['notify_grade_revdate_null'] = 'Review date is not specified.';
$string['notify_grade_sender'] = 'Sender';
$string['notify_grade_sender_help'] = 'Sender of the message.';
$string['notify_grade_webserviceuser'] = 'User that invokes the web service';
$string['notify_new_grade_message'] = '### New grade for {$a->subjectname}' . "\n" . 'Your grade is {$a->gradealpha} ({$a->grade}).' . "\n" . '{$a->revdateformat}.' . "\n" . '{$a->comment}';
$string['pattern_lib'] = 'List of URL patterns to generate redirect URLs.';
$string['pattern_lib_help'] = 'List of URL patterns to generate redirect URLs. Each line defines a pattern that can be requested for generating redirect URLs. The allowed parameters are: course, group, param1, param2';
$string['pluginname'] = 'AppCrue connection services';
$string['privacy:metadata'] = 'The "AppCrue connection services" plugin does not store any personal data.';

$string['sitemapheader'] = 'Sitemap service';
$string['sitemapheader_help'] = 'Generates a JSON structure of categories and courses with many options. This can be used for feeding navigation widget in AppCRUE.';
$string['welcome_message'] = '<p>You are using version {$a->installedversion} of the plugin.</p>' .
    '<p>This plugin allows the integration of Moodle with AppCrue mobile application. ' .
    'It provides services for autologin, avatar retrieval, sitemap generation, and LMS-AppCrue API integration.</p>' .
    '<p>Please configure the plugin settings to connect your Moodle site with AppCrue services.</p>' .
    '<h3>Autoconfiguring LMS-AppCrue API</h3>' .
    '<div class="alert alert-danger">Never enable the autoconfig setting if you are not sure about the security of your network or if you are not expecting to receive requests from AppCrue servers, as it may allow unauthorized access to your Moodle site.</div>' .
    '<p>To autoconfigure the LMS-AppCrue API integration, please follow these steps:</p>' .
    '<ol>' .
    '<li>Ensure that your Moodle site is registered with UNIVERSIA and accessible from the AppCrue servers.</li>' .
    '<li>Enable the "Enable AppCRUE autoconfig procedure" setting in the plugin configuration.</li>' .
    '<li>From the AppCrue mobile app, click on any widget with Moodle information to initiate connection to your Moodle site.</li>' .
    '<li>The plugin will automatically add the official AppCrue server IPs, enable the key rotation service, and store the first API key received from AppCrue.</li>' .
    '<li>The autoconfig setting will be automatically disabled after the first successful configuration to prevent unwanted changes. You can re-enable it if you need to reset the configuration.</li>' .
    '</ol>';

$string['troubleshooting:title'] = 'AppCRUE connection troubleshooting';
$string['troubleshooting:button'] = 'Open troubleshooting';
$string['troubleshooting:settingsbutton'] = 'Open connection troubleshooting';
$string['troubleshooting:description'] = 'Run safe checks for the LMS API, network access, endpoint settings, MFA and user matching. The page can apply selected, narrow fixes without opening access to all IP addresses.';
$string['troubleshooting:intro'] = 'Checks run from this Moodle site or your browser. They cannot confirm that the remote AppCRUE backend can reach Moodle; use its network logs or ask its administrator to run the endpoint URL shown below.';
$string['troubleshooting:back'] = 'Back to AppCRUE settings';
$string['troubleshooting:checks'] = 'Configuration checks';
$string['troubleshooting:statusok'] = 'OK';
$string['troubleshooting:statuswarning'] = 'Needs attention';
$string['troubleshooting:statuserror'] = 'Problem detected';
$string['troubleshooting:statusinfo'] = 'Information';
$string['troubleshooting:key'] = 'API key';
$string['troubleshooting:keyok'] = 'An API key is configured and uses the supported character set.';
$string['troubleshooting:keymissing'] = 'No API key is saved. If this is a new integration, add the official AppCRUE IPs first, then enable autoconfiguration so the first trusted backend request can register its key.';
$string['troubleshooting:keyinvalid'] = 'The saved API key contains unsupported characters. Coordinate a replacement with the AppCRUE backend before changing it.';
$string['troubleshooting:networks'] = 'API source networks';
$string['troubleshooting:networksempty'] = 'No source networks are configured. Requests to the LMS widget API will be denied.';
$string['troubleshooting:networkunrestricted'] = 'The network list contains an all-address range. Remove it and keep only the required trusted IP addresses or CIDR ranges.';
$string['troubleshooting:officialmissing'] = 'Some official AppCRUE source addresses are not covered: {$a}. Add the known addresses if this site is called from those servers.';
$string['troubleshooting:officialpresent'] = 'The configured list covers the known official AppCRUE source addresses.';
$string['troubleshooting:clientip'] = 'Effective IP for this browser request';
$string['troubleshooting:clientallowed'] = 'Moodle recognizes {$a} as allowed by this plugin network list. This is the effective IP for this request, not a verified administrator or backend identity.';
$string['troubleshooting:clientdenied'] = 'Moodle recognizes {$a} as a valid IP, but it is not in this plugin network list. Do not add it automatically: browser traffic can pass through NAT or proxies shared with other systems.';
$string['troubleshooting:clientunknown'] = 'Moodle could not determine a valid IP for this request. Review the web server and trusted-proxy configuration; do not add a guessed address.';
$string['troubleshooting:allow_browser_ip'] = 'Register this effective IP for local tests';
$string['troubleshooting:ip_effective'] = 'Effective IP Moodle uses';
$string['troubleshooting:ip_header_order'] = 'Moodle IP source order';
$string['troubleshooting:proxyheaders'] = 'Forwarded IP headers and reverse proxy';
$string['troubleshooting:proxyheaders_review'] = 'Moodle is configured to consider forwarded client headers. Confirm that a trusted reverse proxy removes client-supplied values and writes its own headers. Configure Moodle to trust only the real proxy chain; this page cannot verify which network peer supplied these headers.';
$string['troubleshooting:addofficial'] = 'Add official AppCRUE IPs';
$string['troubleshooting:autoconfig'] = 'AppCRUE autoconfiguration';
$string['troubleshooting:autoconfigenabled'] = 'Autoconfiguration is enabled and will accept a key on the next eligible request. Disable it after setup if it remains enabled.';
$string['troubleshooting:autoconfigavailable'] = 'No key is saved and the official AppCRUE addresses are covered. You can enable autoconfiguration for the first backend request.';
$string['troubleshooting:enableautoconfig'] = 'Enable autoconfiguration';
$string['troubleshooting:autoconfignetworkfirst'] = 'Add the official AppCRUE addresses before enabling autoconfiguration. The API network check runs first.';
$string['troubleshooting:endpoints'] = 'LMS API endpoints';
$string['troubleshooting:endpointenabled'] = 'The {$a} endpoint is enabled.';
$string['troubleshooting:endpointdisabled'] = 'The {$a} endpoint is disabled. Enable it only if AppCRUE is intended to receive this data.';
$string['troubleshooting:enableendpoint'] = 'Enable {$a} endpoint';
$string['troubleshooting:mfa'] = 'Moodle MFA redirect exclusion';
$string['troubleshooting:mfadisable'] = 'Moodle MFA is disabled; no AppCRUE autologin exclusion is needed.';
$string['troubleshooting:mfaok'] = 'The AppCRUE autologin exclusion matches the current autologin setting.';
$string['troubleshooting:mfamismatch'] = 'The AppCRUE autologin exclusion does not match the current setting. Synchronize it while preserving other exclusions.';
$string['troubleshooting:syncmfa'] = 'Synchronize MFA exclusion';
$string['troubleshooting:apitest'] = 'Browser-to-Moodle endpoint test';
$string['troubleshooting:apitestintro'] = 'This test sends a same-origin request without credentials. HTTP 400 with error code 2 is the expected response: it confirms the request reached an enabled endpoint and passed the plugin network check.';
$string['troubleshooting:apitestbutton'] = 'Run test again';
$string['troubleshooting:apitestpassed'] = 'The request reached the plugin and passed its network check (HTTP 400, missing credentials).';
$string['troubleshooting:apitestdenied'] = 'The request received HTTP 403. Check the client IP above, proxy settings, WAF and firewall rules.';
$string['troubleshooting:apitestnotfound'] = 'The request received HTTP 404. Check that the shown endpoint is enabled and that Moodle serves the slash-argument route.';
$string['troubleshooting:apitestunexpected'] = 'The endpoint responded unexpectedly. Review the response and Moodle or proxy logs.';
$string['troubleshooting:apitestnetworkerror'] = 'The browser could not complete the request. Check the Moodle URL, TLS, browser console, WAF and network access.';
$string['troubleshooting:noendpoint'] = 'No LMS widget endpoint is enabled, so the browser test cannot run. Enable an endpoint only if you intend to expose its data.';
$string['troubleshooting:testuser'] = 'Test user matching';
$string['troubleshooting:identifier'] = 'Value received from AppCRUE';
$string['troubleshooting:identifierhelp'] = 'Enter the email or username that AppCRUE sends, according to the current “Use user parameter for matching” setting.';
$string['troubleshooting:testuserbutton'] = 'Check user';
$string['troubleshooting:currentfield'] = 'The configured Moodle profile field is {$a}.';
$string['troubleshooting:userfound'] = 'The value matches an active Moodle account. The account is enrolled in {$a} active course(s).';
$string['troubleshooting:userinactive'] = 'The value matches a deleted or suspended account. Reactivate the account in Moodle before using it with AppCRUE.';
$string['troubleshooting:usernocourses'] = 'The account exists but has no active course enrolments. Enrol it in the required courses; this tool will not change course access.';
$string['troubleshooting:usernotfound'] = 'No account matches the value using the configured field. Check the user parameter and profile-field mapping.';
$string['troubleshooting:usercandidate'] = 'The value matches an active account in the Moodle {$a} field. You can set this field as the global AppCRUE matching field.';
$string['troubleshooting:applyuserfield'] = 'Use {$a} for AppCRUE user matching';
$string['troubleshooting:userfieldchanged'] = 'The AppCRUE matching field was updated to {$a}.';
$string['troubleshooting:changesaved'] = 'The requested configuration change has been applied.';
$string['troubleshooting:pushnote'] = 'Push notification credentials belong to the separate message_appcrue plugin. This page cannot validate or change those credentials.';
$string['troubleshooting:currentparam'] = 'AppCRUE should send the {$a} parameter.';
$string['troubleshooting:apitestautoconfig'] = 'The browser test is paused because autoconfiguration is enabled. A test request could trigger its setup flow; wait for the trusted AppCRUE backend request instead.';
$string['troubleshooting:disableautoconfig'] = 'Disable autoconfiguration';
$string['event_api_accessed_name'] = 'Successful AppCRUE LMS API access';
$string['event_api_accessed_desc'] = 'User {$a->userid} successfully accessed the AppCRUE LMS endpoint {$a->endpoint}.';
$string['troubleshooting:versions'] = 'Installed and available plugin versions';
$string['troubleshooting:version_norelease'] = 'release not declared';
$string['troubleshooting:versioncheckintro'] = 'Moodle checks its plugin update service for published releases. Refreshing replaces Moodle’s cached update information.';
$string['troubleshooting:versionrefresh'] = 'Force update check now';
$string['troubleshooting:versioncheckdisabled'] = 'Moodle plugin update checks are disabled in config.php, so no fresh remote check can run.';
$string['troubleshooting:versioncheckdisabled_item'] = 'No update can be verified because Moodle plugin update checks are disabled.';
$string['troubleshooting:versionfetchsuccess'] = 'Moodle fetched the latest published plugin update information.';
$string['troubleshooting:versionfetchfailed'] = 'Moodle could not refresh published plugin versions. Check outbound HTTPS access and Moodle update-check settings; the previous cached result is shown.';
$string['troubleshooting:version_not_checked'] = 'No remote update check has completed yet. Force a check to find out whether a newer version is published.';
$string['troubleshooting:version_no_update'] = 'Installed database version {$a->installed}; deployed code version {$a->code} ({$a->release}). Moodle found no newer published version as of {$a->checked}.';
$string['troubleshooting:version_update_found'] = 'Installed database version {$a->db}; deployed code version {$a->code} ({$a->release}). Moodle reports newer published version {$a->remote} ({$a->remoterelease}).';
$string['troubleshooting:version_unavailable'] = 'Moodle cannot compare a published update because this plugin’s code version is unavailable.';
$string['troubleshooting:messageheading'] = 'Push notification diagnostics (message_appcrue)';
$string['troubleshooting:messageprocessor_enabled'] = 'The Moodle message output plugin is enabled.';
$string['troubleshooting:messageprocessor_disabled'] = 'The Moodle message output plugin is disabled. Enable it in Moodle message output settings if AppCRUE pushes should be sent.';
$string['troubleshooting:messageenabled'] = 'Push notifications are enabled.';
$string['troubleshooting:messagedisabled'] = 'Push notifications are disabled in message_appcrue.';
$string['troubleshooting:messagekeyok'] = 'A TwinPush API key is configured. Its value is hidden.';
$string['troubleshooting:messagekeymissing'] = 'The TwinPush API key is empty. Configure it in message_appcrue settings.';
$string['troubleshooting:messageappidok'] = 'A TwinPush application ID is configured.';
$string['troubleshooting:messageappidmissing'] = 'The TwinPush application ID is empty. Configure it in message_appcrue settings.';
$string['troubleshooting:messagefieldok'] = 'The push recipient matching field {$a} is available in Moodle.';
$string['troubleshooting:messagefieldinvalid'] = 'The configured push recipient matching field {$a} is not available. Select a standard or profile field in message_appcrue settings.';
$string['troubleshooting:messagebuffer'] = 'Buffered messages: {$a->pending} pending and {$a->failed} failed.';
$string['troubleshooting:messagebuffermissing'] = 'The message_appcrue buffer table is unavailable; run the Moodle upgrade or disable buffered mode.';
$string['troubleshooting:messagetestlink'] = 'Open the message_appcrue connection test';
$string['troubleshooting:messagetestwarning'] = 'The connection test sends a real push notification to the Moodle user you enter.';
$string['troubleshooting:messageintro'] = 'These checks inspect local settings and buffered-message counts. They do not reveal credential values or contact TwinPush automatically.';
$string['troubleshooting:logs'] = 'AppCRUE activity in Moodle logs';
$string['troubleshooting:logsintro'] = 'Summary of matching events from the last 30 days. Each activity shows its total and its own lists of users and IPs; lists are limited to 50 values.';
$string['troubleshooting:log_api_accessed'] = 'Successful LMS API access';
$string['troubleshooting:log_bad_apikey'] = 'Invalid LMS API key';
$string['troubleshooting:log_badkey_prefix'] = 'Recorded access with ';
$string['troubleshooting:log_unauthorized_ip'] = 'Request from an unauthorized IP';
$string['troubleshooting:log_autologin_login'] = 'Successful autologin';
$string['troubleshooting:log_autologin_failed'] = 'Failed autologin or fallback';
$string['troubleshooting:lognone'] = 'No matching records were found for this period. The standard Moodle log store may be disabled or have a shorter retention period.';
$string['troubleshooting:logunavailable'] = 'The Moodle standard log table is not available, so this page cannot read stored events.';
$string['troubleshooting:log_user'] = 'User';
$string['troubleshooting:log_ip'] = 'Source IP';
$string['troubleshooting:log_endpoint'] = 'Endpoint';
$string['troubleshooting:log_event'] = 'Activity';
$string['troubleshooting:log_time'] = 'Time';
$string['troubleshooting:logdetails'] = 'Moodle logs store API key failures and autologin outcomes. Successful LMS API reads will be logged from this plugin version onward; earlier successful requests were not recorded as AppCRUE events.';
$string['troubleshooting:log_users'] = 'Recorded users';
$string['troubleshooting:log_ips'] = 'Recorded IP addresses';
$string['troubleshooting:log_list_truncated'] = '(list limited to 50 entries)';
$string['troubleshooting:log_badkey_source_summary'] = 'Invalid API key: {$a->official} attempt(s) from official Universia IPs [{$a->officialips}] and {$a->other} from other IPs [{$a->otherips}].';
$string['troubleshooting:log_badkey_official_critical'] = 'Mismatched keys were received from official Universia IPs. This is a critical indication: compare the Moodle key with the backend key.';
$string['troubleshooting:log_badkey_other_info'] = 'Mismatched-key attempts came from non-official IPs; they may be technical tests. Verify their source.';
$string['troubleshooting:extra_networks'] = 'Additional networks in the allowlist';
$string['troubleshooting:extra_networks_critical'] = 'The list contains entries other than exact official AppCRUE IPs and explicitly registered local-test IPs: {$a}. This expands API access and is a critical security issue.';
$string['troubleshooting:extra_networks_ok'] = 'The list contains only exact official AppCRUE IPs and explicitly registered local-test IPs.';
$string['troubleshooting:correct_networks'] = 'Correct allowlist';
$string['troubleshooting:keyrotation'] = 'API key rotation';
$string['troubleshooting:rotation_enabled'] = 'enabled';
$string['troubleshooting:rotation_disabled'] = 'disabled';
$string['troubleshooting:rotation_seen'] = 'A key rotation was recorded at {$a->date}, which implies activity received from the backend. Endpoint: {$a->endpoint}; rotation service: {$a->service}.';
$string['troubleshooting:rotation_not_seen'] = 'No key rotation is recorded, so this installation cannot confirm backend activity through this mechanism. Endpoint: {$a->endpoint}; rotation service: {$a->service}.';
$string['troubleshooting:rotation_enable_button'] = 'Enable endpoint and service';
$string['troubleshooting:interpretation_label'] = 'Interpretation';
$string['troubleshooting:interpretation_general'] = 'This result shows the state observed by this check. Compare it with the actual integration requirements before changing settings.';
$string['troubleshooting:interpretation_versions'] = 'The comparison uses the code installed on this site and the latest release reported by Moodle’s plugin update checker. A missing result can mean that no update check has completed or the update service is unavailable.';
$string['troubleshooting:interpretation_logs'] = 'Events are grouped by activity type. Each group has its own total, user list and source IP list; lists are capped at 50 values. Moodle may not record a user or IP for every event.';
$string['troubleshooting:interpretation_badapikey'] = 'A key mismatch from an official Universia source IP is a critical sign that Moodle and the backend may have different keys. A mismatch from another IP may be a technical test, but its source should be confirmed.';
$string['troubleshooting:interpretation_apikey'] = 'The API key is a shared secret between Moodle and the AppCRUE backend. Keep its value private and coordinate changes with the backend administrator.';
$string['troubleshooting:interpretation_networks'] = 'This allowlist controls which source IPs can call the LMS API. Keep exact official AppCRUE addresses and explicitly registered local-test IPs; broad CIDR ranges allow additional systems.';
$string['troubleshooting:interpretation_extranetworks'] = 'The browser address may be changed by NAT or shared proxies, so it does not prove that an address belongs only to the administrator. The correction keeps exact official addresses and explicitly registered local-test IPs. If a local-test IP is a shared gateway, all requests through that gateway pass the network allowlist.';
$string['troubleshooting:localtestips'] = 'Administrator IPs registered for local tests';
$string['troubleshooting:localtestip_value'] = '{$a} is explicitly allowed for local browser tests. This may be a shared proxy or NAT gateway.';
$string['troubleshooting:remove_local_test_ip'] = 'Remove this local-test IP';
$string['troubleshooting:interpretation_rotation'] = 'A recorded key rotation confirms that this Moodle site received a rotation request from the backend. The key-rotation endpoint and its service must both be enabled for future rotations.';
$string['troubleshooting:interpretation_ipdiagnostics'] = 'REMOTE_ADDR is the network peer connected to PHP. HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR may describe earlier hops and can be forged unless a trusted proxy strips incoming values and sets its own. The effective IP follows the source order shown above. reverseproxyignore lists proxy addresses Moodle should ignore when processing the forwarded chain. An observed browser IP does not identify an administrator.';
$string['troubleshooting:interpretation_ipdiagnostics'] .= ' The local-test action adds only the exact effective IP shown here. If it is a shared NAT or proxy gateway, requests from other systems through that gateway also pass the network allowlist; API credentials are still required.';
$string['troubleshooting:interpretation_autoconfig'] = 'Autoconfiguration can save an API key from an eligible request after network checks pass. Use it only for initial setup, then disable it.';
$string['troubleshooting:interpretation_endpoint'] = 'An enabled endpoint makes its LMS data available through the API to requests that pass authentication and network checks. Enable only the data feeds the integration uses.';
$string['troubleshooting:interpretation_mfa'] = 'When Moodle MFA is active, the AppCRUE autologin URL must be in Moodle’s redirect exclusions so the login flow can finish. Keep this exclusion aligned with the autologin setting.';
$string['troubleshooting:interpretation_apitest'] = 'This same-origin browser probe checks whether the request reaches an enabled endpoint and passes the plugin network check. It sends no API credentials and does not prove that the remote backend can reach Moodle.';
$string['troubleshooting:interpretation_testuser'] = 'This check uses the configured identifier field to find an account and inspect its active enrolments. It does not change the account, enrolments or permissions.';
$string['troubleshooting:interpretation_endpointtest'] = 'Each test calls an enabled endpoint over HTTP with the configured API key and identifier. The notification shows the status, item count, date range and response time. The full JSON can be opened below.';
$string['troubleshooting:endpointtestheading'] = 'Enabled endpoint responses for this user';
$string['troubleshooting:endpointtestintro'] = 'For an active matching user, Moodle makes a real HTTP request from the server to each enabled public endpoint and shows the JSON response in a collapsed panel. It uses the saved API key in the X-API-KEY header and does not expose it in the page or URL. Each request originates from the Moodle server. The test temporarily allows 127.0.0.1 in the network list and removes it afterward if it was not already configured. The browser IP is separate. The response may contain the user’s course data.';
$string['troubleshooting:endpointtesttitle'] = 'Endpoint {$a} test';
$string['troubleshooting:endpointtestsuccess'] = 'Response generated';
$string['troubleshooting:endpointtestwarning'] = 'Response received without items or with an incomplete summary';
$string['troubleshooting:endpointtesterror'] = 'Endpoint returned an error';
$string['troubleshooting:endpointtesthttpstatus'] = 'HTTP {$a}';
$string['troubleshooting:endpointtestcurlerror'] = 'The Moodle server could not complete the HTTP request (cURL error {$a}). Check the Moodle site URL, DNS, TLS certificate and server network access.';
$string['troubleshooting:endpointtestlock'] = 'The temporary network configuration could not be reserved for this test. Try again after another endpoint test has finished.';
$string['troubleshooting:endpointtestforbidden'] = 'The endpoint returned an empty HTTP 403 response. This test temporarily allows 127.0.0.1; if the block continues, check the web log for another server source IP or a proxy or firewall rule.';
$string['troubleshooting:endpointtestcount'] = 'Elements received: {$a}';
$string['troubleshooting:endpointtestcountunknown'] = 'The response contains date fields, but no item count could be determined.';
$string['troubleshooting:endpointtestdaterange'] = 'Date range in the response: {$a->from} – {$a->to}';
$string['troubleshooting:endpointtestnoitems'] = 'The response contains no endpoint items.';
$string['troubleshooting:endpointtestresponsetime'] = 'Response time: {$a} s';
$string['troubleshooting:endpointtestresponsetime_unknown'] = 'Response time: unavailable; no HTTP request was made.';
$string['troubleshooting:endpointtestjson'] = 'View response JSON';
$string['troubleshooting:endpointtestnone'] = 'No enabled endpoints are available to test.';
$string['troubleshooting:endpointtestnokey'] = 'No valid API key is configured, so the endpoints cannot be tested.';
$string['troubleshooting:messagetest_title'] = 'message_appcrue delivery test';
$string['troubleshooting:messagetestconfirm_prompt'] = 'No push notification has been sent. Confirm below if you want to send a real test notification to this matched user.';
$string['troubleshooting:messagetestconfirm_button'] = 'Confirm and send test push';
$string['troubleshooting:messagetestcodeunavailable'] = 'message_appcrue is installed in Moodle but its plugin code is unavailable, so the delivery test could not run.';
$string['troubleshooting:messagetest_sent'] = 'A test push notification was sent to {$a}.';
$string['troubleshooting:messagetest_configmissing'] = 'The message_appcrue API key or App ID is not configured.';
$string['troubleshooting:messagetest_noalias'] = 'The matched user has no device alias in the message_appcrue matching field “{$a}”.';
$string['troubleshooting:messagetest_deliveryerror'] = 'TwinPush reported delivery errors for Moodle user ID(s): {$a}.';
$string['troubleshooting:messagetest_exception'] = 'The message_appcrue request failed: {$a}';
$string['troubleshooting:interpretation_messageprocessor'] = 'This reports whether Moodle has enabled the separate message_appcrue output plugin. Push delivery also depends on that plugin’s settings and TwinPush.';
$string['troubleshooting:interpretation_messagepush'] = 'This is the local push-notification enable switch in message_appcrue. It does not verify delivery to TwinPush.';
$string['troubleshooting:interpretation_messagecredentials'] = 'This check confirms only whether required credential fields are present. It cannot validate the values with TwinPush, and it never displays them.';
$string['troubleshooting:interpretation_messageuserfield'] = 'message_appcrue uses this Moodle field to match push recipients. The field must contain the identifier expected by TwinPush.';
$string['troubleshooting:interpretation_messagebuffer'] = 'These counts show buffered messages stored locally. Failed or pending entries need review in the message_appcrue task output and Moodle scheduled-task logs.';
$string['troubleshooting:interpretation_messagetest'] = 'The linked test sends a real push notification to the selected Moodle user. Use it only when a test notification is appropriate.';
$string['troubleshooting:messagelognote'] = 'TwinPush delivery details are written to Moodle task/cron output by message_appcrue and are not standard event-log records.';
$string['troubleshooting:tasklogslink'] = 'Open Moodle scheduled task logs';
$string['troubleshooting:log_noendpoint'] = '-';
$string['troubleshooting:idpcredentials'] = 'UNIVERSIA IdP credentials';
$string['troubleshooting:idpcredentials_missing'] = 'The external API App ID or token is missing. Configure both values in the token verification settings; their values are not shown here.';
$string['troubleshooting:idpcredentials_present'] = 'The external API App ID and token are configured. This check only confirms they are non-empty; it does not validate them directly with UNIVERSIA.';
$string['troubleshooting:idpresponse'] = 'Recorded UNIVERSIA IdP response';
$string['troubleshooting:idp406_pre_mismatch'] = 'An HTTP 406 error was found in a token verification recorded on {$a}. The App ID contains “pre”, but “Use PRE server” is disabled. Enable that setting if these credentials belong to the preproduction environment.';
$string['troubleshooting:idp406'] = 'An HTTP 406 error was found in a token verification recorded on {$a}. Check that the API token, App ID, server environment, and user token all belong to the same environment.';
$string['troubleshooting:idp406_not_found'] = 'No HTTP 406 error was found in autologin verifications recorded in the last 30 days. This does not make a new request to the IdP.';
$string['troubleshooting:idp_log_unavailable'] = 'Moodle’s standard log is unavailable, so previous IdP responses cannot be checked.';
$string['troubleshooting:interpretation_idpdiagnostics'] = 'This check confirms that the external API App ID and token are configured, but it does not reveal or validate these secrets. The IdP response is inferred from token verification errors stored in Moodle’s standard log during the last 30 days. If an HTTP 406 appears and the App ID contains “pre” while “Use PRE server” is disabled, the credentials likely belong to preproduction while Moodle is calling the production endpoint. The absence of HTTP 406 errors does not confirm that the IdP is available now.';
