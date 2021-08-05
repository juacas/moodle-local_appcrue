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

$string['calendarheader'] = 'User calendar';
$string['calendarheader_help'] = 'Integration of the user\'s calendar in AppCrue';
$string['share_site_events'] = 'Return the institution\'s events';
$string['share_site_events_help'] = 'Devolver los eventos de la institución';
$string['share_course_events'] = 'Return course events';
$string['share_course_events_help'] = 'Devolver los eventos de los cursos';
$string['share_user_events'] = 'Return personal events';
$string['share_user_events_help'] = 'Devolver los eventos personales';
$string['examen_event_type'] = '"Exam" activities.';
$string['examen_event_type_help'] = 'Activities that may generate "Exam events" in the calendar';