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
 * Plugin AppCrue información de versión.
 *
 * @package local_appcrue
 * @category admin
 * @author  Juan Pablo de Castro, Alberto Otero Mato
 * @copyright 2021 onwards juanpablo.decastro@uva.es, alberto.otero@altia.es
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allow_continue'] = 'Permitir continuar como invitado';
$string['allow_continue_help'] = 'Permite fallback=continue que no genera error cuando el token no es válido. Inicia sesión al usuario como invitado y redirecciona a la URL de todas formas.';
$string['assignments:dates'] = 'Fechas de tareas';
$string['assignments:dates_desc'] = 'Mapea cada tipo de actividad considerada como tarea con los campos de fechas (fecha de entrega, fecha límite, etc.) en la base de datos en formato mod_nombreactividad|tabla|fechainicio|fechalimite.';
$string['autologin:deep_url_token_mark'] = 'Marca de token en URL de autologin';
$string['autologin:deep_url_token_mark_disabled'] = 'Sin URLs de autologin';
$string['autologin:deep_url_token_mark_help'] = 'La marca de token a usar en las URLs profundas: "token" indica a la app usar el token en la URL como parámetro de consulta. "bearer" indica a la app usar el token en el header Authorization.';
$string['autologinheader'] = 'Servicio de auto-login';
$string['autologinheader_help'] = 'Permite a los usuarios saltar a Moodle desde AppCrue.';
$string['avatarheader'] = 'Servicio de avatar';
$string['avatarheader_help'] = 'Permite a cualquier app con un token válido obtener la imagen del usuario.';
$string['cache_sitemap'] = 'Cachear mapas de sitio';
$string['cache_sitemap_help'] = 'Usar el sistema de caché de Moodle con los mapas de sitio.';
$string['cache_sitemap_ttl'] = 'TTL de caché de mapas de sitio';
$string['cache_sitemap_ttl_help'] = 'Tiempo de vida para el caché del mapa de sitio.';
$string['cachedef_sitemap'] = 'Almacena mapas json de categorías y cursos';
$string['cachedef_sitemaps'] = 'Almacena mapas json de categorías y cursos';
$string['calendar:enable_calendar'] = 'Habilitar servicio de calendario heredado';
$string['calendar:enable_calendar_help'] = 'El servicio de calendario verifica un <b>token personal</b> y genera una representación JSON de los eventos del usuario.';
$string['calendar:event_imgdetail'] = 'Imagen por defecto';
$string['calendar:event_imgdetail_help'] = 'Imagen por defecto asociada con el evento. Se usa para decorar la interfaz de usuario.';
$string['calendar:examen_event_type'] = 'Actividades de "Examen".';
$string['calendar:examen_event_type_help'] = 'Actividades que pueden generar "Eventos de examen" en el "calendario del usuario" y en "API LMS de Tareas".';
$string['calendar:share_course_events'] = 'Devolver eventos del curso';
$string['calendar:share_course_events_help'] = 'Incluir eventos a nivel de curso en la respuesta.';
$string['calendar:share_site_events'] = 'Devolver eventos de la institución';
$string['calendar:share_site_events_help'] = 'Incluir eventos de la institución en la respuesta.';
$string['calendar:share_user_events'] = 'Devolver eventos personales';
$string['calendar:share_user_events_help'] = 'Incluir eventos personales en la respuesta.';
$string['calendarheader'] = 'Calendario del usuario';
$string['calendarheader_help'] = 'Integración del calendario del usuario en AppCrue por la API heredada (basada en token) o la LMS-AppCRUE (basada en clave API).';
$string['continue_not_allowed'] = 'fallback=continue no permitido';
$string['course_pattern'] = 'Patrón SQL para buscar un curso';
$string['course_pattern_help'] = 'El patrón SQL se usará para buscar un curso usando el campo "idnumber" y los parámetros de solicitud "course, group, param1, param2, param3" (no necesariamente IDs de Moodle).';
$string['enable_autologin'] = 'Habilitar autologin';
$string['enable_autologin_help'] = 'El servicio de autologin toma el token de un usuario, lo verifica, inicia sesión al usuario y redirecciona a una URL interna.';
$string['enable_avatar'] = 'Habilitar servicio de avatar';
$string['enable_avatar_help'] = 'El servicio de avatar toma el token de un usuario, lo verifica y devuelve la imagen del usuario.';
$string['enable_sitemap'] = 'Habilitar servicio de mapa de sitio';
$string['enable_sitemap_help'] = 'El servicio de mapa de sitio genera una representación JSON de las categorías y cursos.';
$string['idp_client_id'] = 'Client ID para el IDP';
$string['idp_client_id_help'] = 'Client ID para el IDP para invocar el servicio de token de usuario.';
$string['idp_client_secret'] = 'Client secret para el IDP';
$string['idp_client_secret_help'] = 'Client secret para el IDP para invocar el servicio de token de usuario.';
$string['idp_token_url'] = 'Endpoint para resolución de token';
$string['idp_token_url_help'] = 'URL del servicio IDP para resolver la identidad del usuario desde el token.';
$string['idp_url'] = 'URL del IDP';
$string['idp_url_help'] = 'URL del IDP para invocar el servicio de token de usuario.';
$string['idp_user_json_path'] = 'Selector en la respuesta del IDP.';
$string['idp_user_json_path_help'] = 'Selector como jsonpath para identificar el valor para identificar al usuario.';
$string['idpheader'] = 'Verificación de tokens';
$string['idpheader_help'] = 'Configuración del servicio de verificación de tokens de AppCrue. Esta funcionalidad es para instituciones que usan un IdP OAuth.';
$string['lmsappcrue:announcements'] = 'Anuncios AppCRUE';
$string['lmsappcrue:announcements_help'] = 'Exponer los anuncios del usuario a AppCRUE vía API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:api_authorized_networks'] = 'Redes autorizadas para API';
$string['lmsappcrue:api_authorized_networks_help'] = 'Lista de redes que están permitidas para acceder a la API LMS. Cada línea con una dirección IP o notación CIDR.';
$string['lmsappcrue:api_key'] = 'Clave API';
$string['lmsappcrue:api_key_help'] = 'Clave API para acceder a los servicios AppCrue. Se usa para identificar la app que está invocando el servicio sin tokens de usuario.';
$string['lmsappcrue:api_key_rotated'] = 'Clave API rotada el {$a}';
$string['lmsappcrue:api_key_warning'] = 'Un sistema remoto usó una clave api_key desconocida "{$a}" mientras accedía al endpoint del LMS API. Si estás configurando la clave API por primera vez o la has perdido, si confías en los servicios con acceso al endpoint (p.ej. por los filtros de red), puedes usar este valor para configurarla en los ajustes del plugin.';
$string['lmsappcrue:assignments'] = 'Tareas AppCRUE';
$string['lmsappcrue:assignments_help'] = 'Exponer las tareas del usuario a AppCRUE. Este endpoint permite a AppCRUE recuperar las tareas del usuario y sus fechas de entrega y estado.';
$string['lmsappcrue:calendar:enable_calendar'] = 'Habilitar endpoint de calendario LMS-AppCRUE';
$string['lmsappcrue:calendar:enable_calendar_help'] = 'Habilitar endpoint de calendario para compartir el calendario del usuario con AppCRUE vía API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:enable_announcements'] = 'Habilitar endpoint de anuncios';
$string['lmsappcrue:enable_announcements_help'] = 'Habilitar endpoint de anuncios para compartir los anuncios del usuario con AppCRUE.';
$string['lmsappcrue:enable_api_key_rotation'] = 'Habilitar endpoint de rotación de clave API';
$string['lmsappcrue:enable_api_key_rotation_help'] = 'El endpoint de rotación de clave API permite a AppCRUE cambiar periódicamente la clave API usada por AppCRUE.';
$string['lmsappcrue:enable_assignments'] = 'Habilitar endpoint de tareas';
$string['lmsappcrue:enable_assignments_help'] = 'Habilitar endpoint de tareas para compartir las tareas del usuario con API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:enable_autoconfig_appcrue'] = 'Habilitar procedimiento de autoconfiguración AppCRUE';
$string['lmsappcrue:enable_autoconfig_appcrue_help'] = '<p>El procedimiento de autoconfiguración facilita la configuración inicial de conexión con AppCRUE: ' .
    'Añade las IPs oficiales de los servidores de AppCRUE, activa el servicio de rotación de claves y almacena la primera clave API recibida desde AppCRUE.' .
    ' Solo se aceptan claves desde los servidores oficiales de AppCRUE o añadidos manualmente por el administrador.</p><p> Para completar la configuración, debe hacerse alguna solicitud de los componentes de LMS de la aplicación móvil AppCRUE.</p>';
$string['lmsappcrue:enable_files'] = 'Habilitar endpoint de archivos';
$string['lmsappcrue:enable_files_help'] = 'Habilitar endpoint de archivos para notificar archivos y compartir enlaces de descarga.';
$string['lmsappcrue:enable_forums'] = 'Habilitar endpoint de foros';
$string['lmsappcrue:enable_forums_help'] = 'Habilitar endpoint de foros para compartir las conversaciones de foro del usuario con AppCRUE.';
$string['lmsappcrue:enable_grades'] = 'Habilitar endpoint de calificaciones';
$string['lmsappcrue:enable_grades_help'] = 'Habilitar endpoint de calificaciones para compartir las calificaciones del usuario con API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:files'] = 'Archivos AppCRUE';
$string['lmsappcrue:files_help'] = 'Exponer los archivos del usuario a API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:forums'] = 'Foros AppCRUE';
$string['lmsappcrue:forums_help'] = 'Exponer las conversaciones de foro del usuario a API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:forums_timewindow'] = 'Ventana de tiempo para foros';
$string['lmsappcrue:forums_timewindow_help'] = 'Ventana de tiempo para recuperar posts de foros. Se usa para limitar el número de posts devueltos por el servicio.';
$string['lmsappcrue:grades'] = 'Calificaciones AppCRUE';
$string['lmsappcrue:grades_help'] = 'Exponer las calificaciones del usuario a API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:header'] = 'Integración de Widgets LMS-AppCRUE';
$string['lmsappcrue:header_help'] = 'Configuración del servicio de integración de Widgets LMS-AppCRUE. La API LMS-AppCRUE solo es llamada desde el backend de AppCRUE usando una clave API pre-compartida.';
$string['lmsappcrue:internalerror'] = 'Error interno';
$string['lmsappcrue:invalidtimerange'] = 'Rango de tiempo inválido';
$string['lmsappcrue:match_user_by'] = 'Campo para hacer coincidencia del perfil del usuario';
$string['lmsappcrue:match_user_by_help'] = 'El parámetro userid en la solicitud se usa para hacer coincidir el perfil del usuario. El valor de este campo se usa para hacer coincidir el perfil del usuario.';
$string['match_user_by'] = 'Campo para hacer coincidencia del perfil del usuario';
$string['match_user_by_help'] = 'El token de autorización devuelve una identificación que necesita ser emparejada con un campo de usuario.';
$string['missingtoken'] = 'Token faltante';
$string['notify_grade_anyteacher'] = 'Cualquier profesor';
$string['notify_grade_header'] = 'Servicio web para notificar calificaciones';
$string['notify_grade_header_help'] = 'Este servicio web permite notificar calificaciones a estudiantes.';
$string['notify_grade_revdate'] = 'La revisión será el próximo {$a->revdateformat}';
$string['notify_grade_revdate_null'] = 'La fecha de revisión no está especificada.';
$string['notify_grade_sender'] = 'Remitente';
$string['notify_grade_sender_help'] = 'Remitente del mensaje.';
$string['notify_grade_webserviceuser'] = 'Usuario que invoca el servicio web';
$string['notify_new_grade_message'] = '### Nueva calificación para {$a->subjectname}' . "\n" . 'Tu calificación es {$a->gradealpha} ({$a->grade}).' . "\n" . '{$a->revdateformat}.' . "\n" . '{$a->comment}';
$string['pattern_lib'] = 'Lista de patrones de URL para generar URLs de redirección.';
$string['pattern_lib_help'] = 'Lista de patrones de URL para generar URLs de redirección. Cada línea define un patrón que puede ser solicitado para generar URLs de redirección. Los parámetros permitidos son: course, group, param1, param2';
$string['pluginname'] = 'Servicios de conexión AppCrue';
$string['privacy:metadata'] = 'El plugin "Servicios de conexión AppCrue" no almacena ningún dato personal.';
$string['sitemapheader'] = 'Servicio de mapa de sitio';
$string['sitemapheader_help'] = 'Genera una estructura JSON de categorías y cursos con muchas opciones. Esto puede ser usado para alimentar el widget de navegación en AppCRUE.';
