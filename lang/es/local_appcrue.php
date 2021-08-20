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
$string['pluginname'] = 'Servicios de conexión a AppCrue';
$string['idpheader'] = 'Verificación de tokens';
$string['idpheader_help'] = 'Configuración del servico de verificación de tokens AppCrue';
$string['idp_url'] = 'URL del IDP';
$string['idp_url_help'] = 'URL del IDP para autenticar al servicio.';
$string['idp_token_url'] = 'URL de datos del token en el IDP';
$string['idp_token_url_help'] = 'URL token of the IDP invocar el servicio usertoken.';
$string['idp_client_id'] = 'Clientid for the IDP';
$string['idp_client_id_help'] = 'Id. del cliente para que el IDP invoque el servicio de token de usuario.';
$string['idp_client_secret'] = 'Client secret for the IDP';
$string['idp_client_secret_help'] = 'Client secret para que el IDP invoque el servicio de token de usuario.';

$string['calendarheader'] = 'User calendar';
$string['calendarheader_help'] = 'User calendar';
$string['share_site_events'] = 'Devolver los eventos de la institución';
$string['share_site_events_help'] = 'Devolver los eventos de la institución';
$string['share_course_events'] = 'Devolver los eventos de los cursos';
$string['share_course_events_help'] = 'Devolver los eventos de los cursos';
$string['share_user_events'] = 'Devolver los eventos personales';
$string['share_user_events_help'] = 'Devolver los eventos personales';
$string['match_user_by'] = 'Campo de coincidencia con el perfil del usuario';
$string['match_user_by_help'] = 'El token de autorización devuelve una identificación que debe coincidir con un campo de usuario.';
$string['idp_user_json_path'] = 'Selector del identificador';
$string['idp_user_json_path_help'] = 'Selector tipo jsonpath para identificar el valor para identificar al usuario.';
$string['course_pattern'] = 'Patrón SQL para buscar un curso';
$string['course_pattern_help'] = 'El patrón SQL se utilizará para buscar un curso utilizando el campo "idnumber" y los parámetros de la petición autologin "course" y "group" (no necesariamente ids de moodle)';