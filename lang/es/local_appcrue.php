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
$string['enable_calendar'] = 'Activar el servicio de calendario';
$string['enable_calendar_help'] = 'El servicio de calendario comprueba un token personal y genera una representación JSON de los eventos del usuario.';
$string['share_site_events'] = 'Devolver los eventos de la institución';
$string['share_site_events_help'] = 'Devolver los eventos de la institución';
$string['share_course_events'] = 'Devolver los eventos de los cursos';
$string['share_course_events_help'] = 'Devolver los eventos de los cursos';
$string['share_user_events'] = 'Devolver los eventos personales';
$string['share_user_events_help'] = 'Devolver los eventos personales';
$string['examen_event_type'] = 'Tipo "Examen"';
$string['examen_event_type_help'] = 'Actividades que se marcarán como "Examen" en el calendario';
$string['event_imgdetail'] = 'Imagen por defecto';
$string['event_imgdetail_help'] = 'Imagen por defecto asociada al evento. Se utiliza para decorar la interfaz de usuario.';


$string['privacy:metadata'] = 'El plugin "AppCrue connection services" no almacena ningún dato personal.';

$string['autologinheader'] = 'Servicio Auto-login';
$string['autologinheader_help'] = 'Permite a los usuarios saltar a Moodle desde el AppCrue. También implementa redirecciones predefinidas.';
$string['enable_autologin'] = 'Habilita Autologin';
$string['enable_autologin_help'] = 'El servicio Autologin toma el token de un usuario, lo verifica (opcionalmente) y lo redirige a una url interna. Si no se usa un token se puede redirigir a otras URLs sin afectar a la sesión.';
$string['allow_continue'] = 'Permitir fallback continue';
$string['allow_continue_help'] = 'Permitir fallback=continue que si el token es invalido, no genera error, loguea como invitado y redirige a la URL de continuación.';
$string['pattern_lib'] = 'List of URL pattens to generate redirect urls.';
$string['pattern_lib_help'] = 'Lista de patrones de URL para generar urls de redirección. Cada línea define un patrón que puede ser solicitado para generar urls de redirección. Los parámetros permitidos son: curso, grupo, param1, param2';
$string['match_user_by'] = 'Campo de coincidencia con el perfil del usuario';
$string['match_user_by_help'] = 'El token de autorización devuelve una identificación que debe coincidir con un campo de usuario.';
$string['idp_user_json_path'] = 'Selector del identificador';
$string['idp_user_json_path_help'] = 'Selector tipo jsonpath para identificar el valor para identificar al usuario.';
$string['course_pattern'] = 'Patrón SQL para buscar un curso';
$string['course_pattern_help'] = 'El patrón SQL se utilizará para buscar un curso utilizando el campo "idnumber" y los parámetros de la petición autologin "course, group, param1, param2, param3" (no necesariamente ids de moodle)';
$string['pattern_lib'] = 'List of URL pattens to generate redirect urls.';
$string['pattern_lib_help'] = 'List of URL pattens to generate redirect urls. Each line defines a pattern that can be requested for generating redirect urls. The allowed parameters are: course, group, param1, param2';

$string['avatarheader'] = 'Servicio Avatar';
$string['avatarheader_help'] = 'Permite a cualquier aplicación con un token válido obtener la imagen de ese usuario.';
$string['enable_avatar'] = 'Activar el servicio de avatar';
$string['enable_avatar_help'] = 'El servicio de avatar toma el token de un usuario, lo verifica y devuelve la imagen del usuario.';
$string['sitemapheader'] = 'Servicio de mapa del sitio';
$string['sitemapheader_help'] = 'Genera una estructura JSON de categorías y cursos.';
$string['enable_sitemap'] = 'Habilitar el servicio de mapa del sitio';
$string['enable_sitemap_help'] = 'El servicio Sitemap genera una representación JSON de las categorías y los cursos.';
$string['cache_sitemap'] = 'Cache sitemaps';
$string['cache_sitemap_help'] = 'Utilizar el sistema de caché de Moodle con SiteMaps.';

$string['new_grade_message'] = '### Nota para {$a->subjectname}' . "\n" . 'Tu nota es {$a->gradealpha} ({$a->grade}).' . "\n" . '{$a->revdateformat}.' . "\n" . '{$a->comment}';
$string['notify_grade_revdate_null'] = 'No se ha especificado la fecha de revisión.';
$string['notify_grade_revdate'] = 'La revisión será el {$a->revdateformat}';
