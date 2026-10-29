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
$string['autologin:deep_url_token_mark_appcruebearer'] = 'Token de sesión AppCRUE en el header Auth-Bearer';
$string['autologin:deep_url_token_mark_appcruetoken'] = 'Token de sesión AppCRUE en el parámetro de consulta de la URL';
$string['autologin:deep_url_token_mark_bearer'] = 'Token de la universidad en el header Auth-Bearer';
$string['autologin:deep_url_token_mark_disabled'] = 'Sin URLs de autologin';
$string['autologin:deep_url_token_mark_help'] = 'La marca de token a usar en las URLs profundas: "token" indica a la app usar el token en la URL como parámetro de consulta. "bearer" indica a la app usar el token en el header Authorization.';
$string['autologin:deep_url_token_mark_token'] = 'Token de la universidad en el parámetro de consulta de la URL';
$string['autologin:follow_metacourses'] = 'Seguir metacursos';
$string['autologin:follow_metacourses_help'] = 'Si está habilitado, al redirigir a cursos por patrón, se seguirán los metacursos para ir al curso "padre" donde el usuario está meta-inscrito. Si el curso está meta-enrolado en más de un curso padre, no se realizará ninguna redirección.';
$string['autologin:loggedasguest'] = 'Ha iniciado sesión como invitado en {$a->sitename} desde la aplicación. Esto usualmente significa que su token no era válido, por lo que tiene acceso limitado al sitio. Pulse el enlace siguiente para continuar.';
$string['autologin:loggedasuser'] = 'Ha iniciado sesión como {$a->fullname} en {$a->sitename} desde la aplicación.';
$string['autologin:mfa_exclusion_notice'] = 'Se ha añadido una excepción MFA para AppCRUE a su <a href="{$a}">configuración de Moodle</a>.';
$string['autologin:notauthenticated'] = 'Redirección no autorizada. Intente cerrar y reabrir la sesión en la aplicación y vuelva a intentarlo. Si el problema persiste, contacte al administrador del sitio.';
$string['autologin:redirecting'] = 'Redirigiendo al sitio...';
$string['autologin:use_redirection_page'] = 'Usar una página de redirección en lugar de HTTP 303';
$string['autologin:use_redirection_page_help'] = 'Si está habilitado, autologin realizará la redirección mediante una página de redirección en lugar de una redirección HTTP 303.' .
    ' Marque esta opción si su servidor tiene problemas para reconocer nuevas sesiones en las redirecciones.';
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
$string['course_pattern_help'] = 'El patrón SQL se usará para buscar un curso en el campo "idnumber" y los parámetros de solicitud "course, group, param1, param2, param3" (no necesariamente IDs de Moodle).';
$string['enable_autologin'] = 'Habilitar autologin';
$string['enable_autologin_help'] = 'El servicio de autologin toma el token de un usuario, lo verifica, inicia sesión al usuario y redirecciona a una URL interna.';
$string['enable_avatar'] = 'Habilitar servicio de avatar';
$string['enable_avatar_help'] = 'El servicio de avatar toma el token de un usuario, lo verifica y devuelve la imagen del usuario.';
$string['enable_sitemap'] = 'Habilitar servicio de mapa de sitio';
$string['enable_sitemap_help'] = 'El servicio de mapa de sitio genera una representación JSON de las categorías y cursos.';
$string['event_api_accessed_desc'] = 'El usuario {$a->userid} accedió correctamente al endpoint LMS de AppCRUE {$a->endpoint}.';
$string['event_api_accessed_name'] = 'Acceso correcto a la API LMS de AppCRUE';
$string['event_autologin_failed_desc'] = 'Intento fallido de autologin con el token {$a->token} desde la dirección IP {$a->ipaddress}. Causa: {$a->diagnosis}';
$string['event_autologin_failed_name'] = 'Intento fallido de autologin';
$string['event_autologin_login_desc'] = 'Usuario con id {$a->userid} inició sesión mediante autologin con un token válido.';
$string['event_autologin_login_name'] = 'Inicio de sesión con autologin';
$string['event_bad_apikey_failed_desc'] = 'Intento con clave API no válida {$a->apikey} desde la dirección IP {$a->ipaddress}';
$string['event_bad_apikey_failed_name'] = 'Intento con clave API no válida';
$string['event_unauthorized_ip_failed_desc'] = 'Acceso bloqueado desde una dirección IP no permitida {$a->ipaddress}';
$string['event_unauthorized_ip_failed_name'] = 'Intento de acceso con IP no autorizada';
$string['idp:client_id'] = 'import_code del API Externa AppCRUE';
$string['idp:client_id_help'] = '"import_code" del API Externa proporcionado por AppCRUE. Se usa para invocar el servicio de token de usuario.';
$string['idp:client_secret'] = 'Token del API Externa AppCRUE';
$string['idp:client_secret_help'] = '"Token" del API Externa proporcionado por AppCRUE. Se usa para invocar el servicio de token de usuario.';
$string['idp:header'] = 'Verificación de tokens';
$string['idp:header_help'] = 'Configuración del servicio de verificación de tokens de AppCrue. Esta funcionalidad es para instituciones que usan la funcionalidad de enlaces con autologin.';
$string['idp:token_url'] = 'Endpoint para resolución de token';
$string['idp:token_url_help'] = 'URL del servicio IDP para resolver la identidad del usuario desde el token.';
$string['idp:url'] = 'URL del IDP';
$string['idp:url_help'] = 'URL del IDP para invocar el servicio de token de usuario.';
$string['idp:use_custom_idp'] = 'Usar IdP personalizado';
$string['idp:use_custom_idp_help'] = 'Si está habilitado, se utilizará un endpoint de Proveedor de Identidad personalizado para validar los tokens proporcionados por AppCrue. El IdP personalizado debe implementar un endpoint de introspección de token OAuth2 que devuelva información de identidad del usuario en formato JSON. ' .
        'Si está deshabilitado, se utilizará el IdP predeterminado de AppCrue. El servicio servicio AppCRUE devolverá los campos de usuario: id, username, email, document_type, document, nia.';
$string['idp:use_pre_server'] = 'Usar servidor PRE';
$string['idp:use_pre_server_help'] = 'Si está habilitado, los tokens se validarán usando el servidor de preproducción de Universia. Si está deshabilitado, se usará el servidor de producción. Este ajuste solo se aplica cuando no se usa un IdP personalizado.';
$string['idp:user_json_path'] = 'Selector en la respuesta del IDP.';
$string['idp:user_json_path_help'] = 'Selectr como jsonpath para identificar el valor para identificar al usuario. Los valores para el Idp de AppCrue pueden ser ".id", ".username", ".email", ".document", ".nia".';
$string['lmsappcrue:announcements'] = 'Anuncios AppCRUE';
$string['lmsappcrue:announcements_help'] = 'Exponer los anuncios del usuario a AppCRUE vía API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:announcements_timewindow'] = 'Ventana de tiempo para anuncios';
$string['lmsappcrue:announcements_timewindow_help'] = 'Ventana de tiempo para recuperar anuncios. Se usa para limitar el número de anuncios devueltos por el servicio. Si vale 0, no se aplicará ningún filtro de tiempo y se devolverán todos los anuncios.';
$string['lmsappcrue:api_authorized_networks'] = 'Redes autorizadas para API';
$string['lmsappcrue:api_authorized_networks_help'] = 'Lista de redes que están permitidas para acceder a la API LMS. Cada línea con una dirección IP o notación CIDR.';
$string['lmsappcrue:api_key'] = 'Clave API';
$string['lmsappcrue:api_key_help'] = 'Clave API para acceder a estos servicios AppCrue desde fuera. Se usa para identificar la app que está invocando el servicio sin tokens de usuario.';
$string['lmsappcrue:api_key_rotated'] = 'Clave API rotada el {$a}';
$string['lmsappcrue:api_key_warning'] = 'APPCRUE: Un sistema remoto usó una clave api_key desconocida "{$a}" mientras accedía al endpoint del LMS API. Si estás configurando la clave API por primera vez o la has perdido, si confías en los servicios con acceso al endpoint (p.ej. por los filtros de red), puedes usar este valor para configurarla en los ajustes del plugin.';
$string['lmsappcrue:assignments'] = 'Tareas AppCRUE';
$string['lmsappcrue:assignments_help'] = 'Exponer las tareas del usuario a AppCRUE. Este endpoint permite a AppCRUE recuperar las tareas del usuario y sus fechas de entrega y estado.';
$string['lmsappcrue:assignments_timewindow'] = 'Ventana de tiempo para tareas';
$string['lmsappcrue:assignments_timewindow_help'] = 'Ventana de tiempo para recuperar tareas. Las tareas con fecha de entrega anterior a este período serán excluidas. Establece 0 para incluir todas las tareas sin filtro de fecha.';
$string['lmsappcrue:calendar:enable_calendar'] = 'Habilitar endpoint de calendario LMS-AppCRUE';
$string['lmsappcrue:calendar:enable_calendar_help'] = 'Habilitar endpoint de calendario para compartir el calendario del usuario con AppCRUE vía API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:calendar:timewindow_after'] = 'Ventana por defecto de calendario (después)';
$string['lmsappcrue:calendar:timewindow_after_help'] = 'Ventana de tiempo posterior al punto de referencia cuando el endpoint de calendario no recibe timestart o timeend.';
$string['lmsappcrue:calendar:timewindow_before'] = 'Ventana por defecto de calendario (antes)';
$string['lmsappcrue:calendar:timewindow_before_help'] = 'Ventana de tiempo previa al punto de referencia cuando el endpoint de calendario no recibe timestart o timeend.';
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
$string['lmsappcrue:files_timewindow'] = 'Ventana de tiempo para archivos';
$string['lmsappcrue:files_timewindow_help'] = 'Ventana de tiempo para recuperar archivos. Se usa para limitar el número de archivos devueltos por el servicio. Si es 0 entonces no se limita por fecha.';
$string['lmsappcrue:forums'] = 'Foros AppCRUE';
$string['lmsappcrue:forums_help'] = 'Exponer las conversaciones de foro del usuario a API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:forums_timewindow'] = 'Ventana de tiempo para foros';
$string['lmsappcrue:forums_timewindow_help'] = 'Ventana de tiempo para recuperar posts de foros. Se usa para limitar el número de posts devueltos por el servicio. Si es 0 entonces no se limita por fecha.';
$string['lmsappcrue:grades'] = 'Calificaciones AppCRUE';
$string['lmsappcrue:grades_help'] = 'Exponer las calificaciones del usuario a API LMS-AppCRUE (con clave API).';
$string['lmsappcrue:grades_timewindow'] = 'Ventana de tiempo para calificaciones';
$string['lmsappcrue:grades_timewindow_help'] = 'Ventana de tiempo para recuperar calificaciones. Se usa para limitar el número de calificaciones devueltas por el servicio. Si es 0 entonces no se limita por fecha.';
$string['lmsappcrue:header'] = 'Integración de Widgets LMS-AppCRUE';
$string['lmsappcrue:header_help'] = 'Configuración del servicio de integración de Widgets LMS-AppCRUE. La API LMS-AppCRUE solo es llamada desde el backend de AppCRUE usando una clave API pre-compartida.';
$string['lmsappcrue:include_legacy_files'] = 'Incluir archivos heredados del curso';
$string['lmsappcrue:include_legacy_files_help'] = 'Si está habilitado, los archivos del curso almacenados en el área heredada del curso serán incluidos en el endpoint de archivos. ' .
        'Estos archivos serán devueltos junto con los archivos de recursos y carpetas a todos los usuarios. Puede ser un riesgo de seguridad si los archivos heredados no se gestionan adecuadamente.';
$string['lmsappcrue:internalerror'] = 'Error interno';
$string['lmsappcrue:invalidtimerange'] = 'Rango de tiempo inválido';
$string['lmsappcrue:match_user_by'] = 'Campo para hacer coincidencia del perfil del usuario';
$string['lmsappcrue:match_user_by_help'] = 'El parámetro userid en la solicitud se usa para hacer coincidir el perfil del usuario. El valor de este campo se usa para hacer coincidir el perfil del usuario.';
$string['lmsappcrue:show_total_grade_as_final'] = 'Mostrar calificación total como calificación final';
$string['lmsappcrue:show_total_grade_as_final_help'] = 'Si está habilitado, la calificación total del curso se reportará como calificación final en el endpoint de calificaciones. ' .
        'Esto es útil cuando la calificación total en Moodle no es fiable debido al uso de categorías de calificación con métodos de agregación que no encajan bien con el sistema de calificaciones de AppCRUE. ' .
        'Cuando está habilitado, el itemtype de la calificación total del curso se reportará como "mod" en lugar de "course" para ser mostrado correctamente por AppCRUE como una calificación regular.';
$string['lmsappcrue:use_user_param'] = 'Parámetro de usuario para buscar.';
$string['lmsappcrue:use_user_param_help'] = 'Appcrue envía los parámetros "username" y "email" en la solicitud. El valor de este parámetro se usa para buscar en el perfil del usuario por el campo de seleccion en "match_user_by".';
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
$string['troubleshooting:addofficial'] = 'Añadir IP oficiales de AppCRUE';
$string['troubleshooting:allow_browser_ip'] = 'Registrar esta IP efectiva para pruebas locales';
$string['troubleshooting:apitest'] = 'Prueba del endpoint desde el navegador a Moodle';
$string['troubleshooting:apitestautoconfig'] = 'La prueba del navegador se ha pausado porque la autoconfiguración está habilitada. Una solicitud de prueba podría activar ese proceso; espera la solicitud del backend de AppCRUE de confianza.';
$string['troubleshooting:apitestbutton'] = 'Repetir prueba';
$string['troubleshooting:apitestdenied'] = 'La solicitud recibió HTTP 403. Revisa la IP del cliente, los proxies, WAF y cortafuegos.';
$string['troubleshooting:apitestintro'] = 'Esta prueba envía una solicitud del mismo sitio sin credenciales. La respuesta esperada es HTTP 400 con código de error 2: confirma que la solicitud llegó a un endpoint habilitado y pasó el control de red del plugin.';
$string['troubleshooting:apitestnetworkerror'] = 'El navegador no pudo completar la solicitud. Revisa la URL Moodle, TLS, la consola del navegador, WAF y el acceso de red.';
$string['troubleshooting:apitestnotfound'] = 'La solicitud recibió HTTP 404. Comprueba que el endpoint indicado esté habilitado y que Moodle sirva rutas con argumentos de ruta.';
$string['troubleshooting:apitestpassed'] = 'La solicitud llegó al plugin y pasó el control de red (HTTP 400, respuesta normal pues faltan credenciales).';
$string['troubleshooting:apitestunexpected'] = 'El endpoint respondió de forma inesperada. Revisa la respuesta y los registros de Moodle o del proxy.';
$string['troubleshooting:applyuserfield'] = 'Usar {$a} para la correspondencia de usuarios AppCRUE';
$string['troubleshooting:autoconfig'] = 'Autoconfiguración de AppCRUE';
$string['troubleshooting:autoconfigavailable'] = 'No hay clave guardada y la lista cubre las IP oficiales de AppCRUE. Puedes habilitar la autoconfiguración para la primera solicitud del backend.';
$string['troubleshooting:autoconfigenabled'] = 'La autoconfiguración está habilitada y aceptará una clave en la siguiente solicitud válida. Desactívala después de la configuración si sigue habilitada.';
$string['troubleshooting:autoconfignetworkfirst'] = 'Añade las IP oficiales de AppCRUE antes de habilitar la autoconfiguración. La comprobación de red de la API se ejecuta primero.';
$string['troubleshooting:back'] = 'Volver a los ajustes de AppCRUE';
$string['troubleshooting:button'] = 'Abrir diagnóstico';
$string['troubleshooting:changesaved'] = 'Se ha aplicado el cambio de configuración solicitado.';
$string['troubleshooting:checks'] = 'Comprobaciones de configuración';
$string['troubleshooting:checkscount'] = 'Este diagnóstico incluye hasta {$a} tipos de comprobación.';
$string['troubleshooting:clientallowed'] = 'Moodle reconoce {$a} como permitida por la lista de redes de este plugin. Es la IP efectiva de esta solicitud, no una identidad verificada del administrador o del backend.';
$string['troubleshooting:clientallowed_temporary'] = 'Moodle permite solicitudes desde {$a}, pero no es una IP oficial de AppCRUE. Si se autorizó para pruebas desde el navegador, elimina la entrada que la permite al terminar. Un NAT o proxy compartido deja pasar el filtro de IP a otros equipos; aún necesitan la clave API.';
$string['troubleshooting:clientdenied'] = 'Moodle reconoce {$a} como una IP válida, pero no está en la lista de redes de este plugin. No la añadas automáticamente: el tráfico del navegador puede pasar por NAT o proxies compartidos con otros sistemas.';
$string['troubleshooting:clientip'] = 'IP efectiva de esta solicitud del navegador';
$string['troubleshooting:clientunknown'] = 'Moodle no pudo determinar una IP válida para esta solicitud. Revisa el servidor web y la configuración de proxies de confianza; no añadas una dirección supuesta.';
$string['troubleshooting:correct_networks'] = 'Corregir lista blanca';
$string['troubleshooting:currentfield'] = 'El campo de perfil Moodle configurado es {$a}.';
$string['troubleshooting:currentparam'] = 'AppCRUE debe enviar el parámetro {$a}.';
$string['troubleshooting:description'] = 'Ejecuta comprobaciones seguras de la API LMS, el acceso de red, los endpoints, MFA y la correspondencia de usuarios. La página puede aplicar cambios concretos sin abrir el acceso a todas las IP.';
$string['troubleshooting:disableautoconfig'] = 'Deshabilitar autoconfiguración';
$string['troubleshooting:easteregg_alt'] = 'Persona mirando un móvil con iconos de Moodle y AppCRUE';
$string['troubleshooting:easteregg_close'] = 'Cerrar';
$string['troubleshooting:easteregg_message'] = 'No deberías pasar tanto tiempo en esta página. Espero arreglar esto';
$string['troubleshooting:enableautoconfig'] = 'Habilitar autoconfiguración';
$string['troubleshooting:enableendpoint'] = 'Habilitar endpoint {$a}';
$string['troubleshooting:endpointdisabled'] = 'El endpoint {$a} está deshabilitado. Habilítalo solo si AppCRUE debe recibir esos datos.';
$string['troubleshooting:endpointenabled'] = 'El endpoint {$a} está habilitado.';
$string['troubleshooting:endpoints'] = 'Endpoints de la API LMS';
$string['troubleshooting:endpointtestcount'] = 'Elementos recibidos: {$a}';
$string['troubleshooting:endpointtestcountunknown'] = 'La respuesta contiene fechas, pero no se pudo determinar el número de elementos.';
$string['troubleshooting:endpointtestcurlerror'] = 'El servidor Moodle no pudo completar la solicitud HTTP (error cURL {$a}). Comprueba la URL del sitio, DNS, certificado TLS y acceso de red del servidor.';
$string['troubleshooting:endpointtestdaterange'] = 'Rango de fechas de la respuesta: {$a->from} – {$a->to}';
$string['troubleshooting:endpointtesterror'] = 'El endpoint devolvió un error';
$string['troubleshooting:endpointtestforbidden'] = 'El endpoint devolvió una respuesta HTTP 403 vacía. La prueba permite temporalmente 127.0.0.1; si persiste el bloqueo, comprueba en el registro web si el servidor llega desde otra IP, o si lo bloquea un proxy o cortafuegos.';
$string['troubleshooting:endpointtesthttpstatus'] = 'HTTP {$a}';
$string['troubleshooting:endpointtestintro'] = 'Moodle realiza una solicitud HTTP real desde el servidor a cada endpoint público habilitado, aunque el identificador no corresponda a ningún usuario. El JSON aparece en un panel plegado. La solicitud usa la clave API guardada en la cabecera X-API-KEY sin mostrarla en la página ni en la URL. Se origina en el servidor Moodle, que permite temporalmente 127.0.0.1 en la lista blanca y la retira después si no estaba configurada antes. La IP del navegador es distinta. Una respuesta correcta puede contener datos de los cursos del usuario.';
$string['troubleshooting:endpointtestjson'] = 'Ver JSON de la respuesta';
$string['troubleshooting:endpointtestlock'] = 'No se pudo reservar la configuración temporal de red para la prueba. Vuelve a intentarlo cuando termine otra prueba de endpoints.';
$string['troubleshooting:endpointtestmissinguser'] = 'Usuario no encontrado: respuesta esperada del endpoint';
$string['troubleshooting:endpointtestmissinguserexplanation'] = 'Un HTTP 404 con el código de usuario no encontrado es normal cuando el identificador no coincide con ningún usuario de Moodle en el campo configurado. Otros errores 4xx pueden indicar un problema distinto.';
$string['troubleshooting:endpointtestnoitems'] = 'La respuesta no contiene elementos del endpoint.';
$string['troubleshooting:endpointtestnokey'] = 'No hay una clave API válida configurada, por lo que no se pueden probar los endpoints.';
$string['troubleshooting:endpointtestresponsetime'] = 'Tiempo de respuesta: {$a} s';
$string['troubleshooting:endpointtestresponsetime_unknown'] = 'Tiempo de respuesta: no disponible; no se realizó la llamada HTTP.';
$string['troubleshooting:endpointtestsuccess'] = 'Respuesta generada';
$string['troubleshooting:endpointtesttitle'] = 'Prueba de endpoint {$a}';
$string['troubleshooting:endpointtestwarning'] = 'Respuesta recibida sin elementos o con un resumen incompleto';
$string['troubleshooting:endpointtitle'] = 'Endpoint {$a}';
$string['troubleshooting:extra_networks'] = 'Redes adicionales en la lista blanca';
$string['troubleshooting:extra_networks_browser'] = '{$a} coincide con la IP efectiva de este navegador. Aunque se haya añadido para las pruebas, es una excepción de seguridad temporal: elimínala al terminarlas.';
$string['troubleshooting:extra_networks_critical'] = 'Estas entradas no deben estar en la lista blanca: {$a}. No son IP exactas oficiales de AppCRUE ni IP registradas expresamente para pruebas locales. Amplían el acceso a la API y suponen un problema crítico de seguridad.';
$string['troubleshooting:extra_networks_ok'] = 'La lista solo incluye IP exactas oficiales de AppCRUE e IP registradas expresamente para pruebas locales.';
$string['troubleshooting:identifier'] = 'Valor recibido desde AppCRUE';
$string['troubleshooting:identifierhelp'] = 'Introduce el correo o nombre de usuario que envía AppCRUE, según el ajuste actual “Use user parameter for matching”.';
$string['troubleshooting:idp406'] = 'Se encontró un error HTTP 406 en una verificación de token registrada el {$a}. Revisa que el token de API, el App ID, el entorno del servidor y el token de usuario pertenezcan al mismo entorno.';
$string['troubleshooting:idp406_not_found'] = 'No se encontró ningún error HTTP 406 en las verificaciones de autologin registradas desde {$a}. Esta comprobación histórica no realiza una llamada nueva al IdP.';
$string['troubleshooting:idp_environment'] = 'Entorno del IdP de UNIVERSIA';
$string['troubleshooting:idp_log_unavailable'] = 'No está disponible el registro estándar de Moodle, así que no se pueden comprobar las respuestas anteriores del IdP.';
$string['troubleshooting:idp_pre_hint'] = 'El App ID contiene «pre», pero «Usar servidor PRE» está desactivado. Si este App ID pertenece a preproducción, activa el servidor PRE. La coincidencia en el nombre es solo un indicio; comprueba a qué entorno pertenecen las credenciales.';
$string['troubleshooting:idp_probe_expected'] = 'El endpoint de UNIVERSIA configurado devolvió el HTTP 406 esperado para un token de usuario aleatorio no válido en {$a->seconds} s. Esto confirma que la solicitud llegó a un servicio HTTP; no demuestra que las credenciales de aplicación sean válidas.';
$string['troubleshooting:idp_probe_unexpected'] = 'La solicitud con un token de usuario aleatorio no válido devolvió HTTP {$a->code} en {$a->seconds} s; se esperaba HTTP 406. Revisa el entorno seleccionado y la respuesta del IdP. No se muestran el cuerpo de la respuesta ni las credenciales.';
$string['troubleshooting:idp_probe_unreachable'] = 'La solicitud con un token de usuario aleatorio no válido no obtuvo una respuesta HTTP utilizable (error cURL {$a->curlerror}; {$a->seconds} s). Revisa la conectividad saliente, DNS, TLS y el entorno seleccionado.';
$string['troubleshooting:idpcredentials'] = 'Credenciales del IdP de UNIVERSIA';
$string['troubleshooting:idpcredentials_missing'] = 'Falta el App ID o el token del API externo. Configura ambos valores en los ajustes de verificación de tokens; sus valores no se muestran aquí.';
$string['troubleshooting:idpcredentials_present'] = 'El App ID y el token del API externo están configurados.';
$string['troubleshooting:idpresponse'] = 'Respuesta registrada del IdP de UNIVERSIA';
$string['troubleshooting:interpretation_apikey'] = 'La clave API es un secreto compartido entre Moodle y el backend de AppCRUE. Mantén su valor privado y coordina los cambios con quien administre el backend.';
$string['troubleshooting:interpretation_apitest'] = 'Esta prueba del navegador en el mismo sitio comprueba si la solicitud llega a un endpoint habilitado y supera el control de red del plugin. No envía credenciales API ni demuestra que el backend remoto pueda acceder a Moodle.';
$string['troubleshooting:interpretation_autoconfig'] = 'La autoconfiguración puede guardar una clave API a partir de una solicitud válida después de comprobar la red. Úsala solo durante la configuración inicial y desactívala después.';
$string['troubleshooting:interpretation_badapikey'] = 'Una clave no coincidente desde una IP oficial de Universia indica de forma crítica que Moodle y el backend podrían tener claves distintas. Si procede de otra IP, podría ser una prueba técnica, pero conviene confirmar su origen.';
$string['troubleshooting:interpretation_endpoint'] = 'Un endpoint habilitado ofrece datos LMS mediante la API a las solicitudes que superan las comprobaciones de autenticación y red. Habilita solo los datos que usa la integración.';
$string['troubleshooting:interpretation_endpointtest'] = 'Cada prueba llama por HTTP al endpoint habilitado con la clave API configurada y el identificador indicado. La notificación muestra el estado, el número de elementos, el rango de fechas y el tiempo de respuesta. El JSON completo se puede abrir debajo.';
$string['troubleshooting:interpretation_extranetworks'] = 'La dirección del navegador puede cambiar por NAT o proxies compartidos, así que no demuestra que pertenezca solo al administrador. La corrección conserva las direcciones exactas oficiales y las IP registradas expresamente para pruebas locales. Si una IP local de prueba es una puerta de enlace compartida, todas las solicitudes que pasen por ella superan la lista blanca de red.';
$string['troubleshooting:interpretation_general'] = 'Este resultado muestra el estado observado por esta comprobación. Compáralo con los requisitos reales de la integración antes de cambiar ajustes.';
$string['troubleshooting:interpretation_idpdiagnostics'] = 'La prueba de credenciales envía un token de usuario aleatorio no válido al endpoint de UNIVERSIA configurado. Se espera HTTP 406: confirma una respuesta HTTP, pero no valida el App ID ni el token de aplicación. La comprobación histórica aparte consulta los errores de verificación del registro estándar de Moodle posteriores a la fecha seleccionada. El aviso de entorno se basa únicamente en que el App ID contiene «pre» mientras «Usar servidor PRE» está desactivado; no depende de que haya un error 406 registrado.';
$string['troubleshooting:interpretation_ipdiagnostics'] = 'REMOTE_ADDR es el equipo de red conectado a PHP. HTTP_CLIENT_IP y HTTP_X_FORWARDED_FOR pueden describir saltos anteriores y se pueden falsificar si un proxy de confianza no elimina los valores entrantes y establece los suyos. La IP efectiva sigue el orden de fuentes mostrado. reverseproxyignore indica las direcciones de proxy que Moodle debe ignorar al procesar la cadena reenviada. Una IP observada en el navegador no identifica a un administrador. La acción de prueba local añade solo la IP efectiva exacta que se muestra. Si es una puerta de enlace NAT o proxy compartida, también pasarán el filtro de red las solicitudes de otros sistemas que entren por ella; aún se requieren las credenciales API.';
$string['troubleshooting:interpretation_label'] = 'Interpretación';
$string['troubleshooting:interpretation_logs'] = 'Los eventos se agrupan por tipo de actividad. Cada grupo tiene su propio total y listas de usuarios e IP de origen; las listas se limitan a 50 valores. Moodle puede no guardar el usuario o la IP en todos los eventos.';
$string['troubleshooting:interpretation_messagebuffer'] = 'Estos totales muestran mensajes en cola guardados localmente. Revisa los pendientes o fallidos en la salida de tareas de message_appcrue y en los registros de tareas programadas de Moodle.';
$string['troubleshooting:interpretation_messagecredentials'] = 'Esta comprobación solo confirma si los campos de credenciales requeridos tienen un valor. No puede validarlos con TwinPush y nunca muestra sus valores.';
$string['troubleshooting:interpretation_messageprocessor'] = 'Indica si Moodle tiene habilitado el plugin de salida independiente message_appcrue. El envío push también depende de sus ajustes y de TwinPush.';
$string['troubleshooting:interpretation_messagepush'] = 'Este es el interruptor local de notificaciones push en message_appcrue. No comprueba si TwinPush las entrega.';
$string['troubleshooting:interpretation_messagetest'] = 'El enlace de prueba envía una notificación push real al usuario Moodle seleccionado. Úsalo solo si corresponde enviar una notificación de prueba.';
$string['troubleshooting:interpretation_messageuserfield'] = 'message_appcrue utiliza este campo Moodle para buscar destinatarios push. El campo debe contener el identificador que espera TwinPush.';
$string['troubleshooting:interpretation_mfa'] = 'Si Moodle MFA está activo, la URL de autologin de AppCRUE debe estar en las excepciones de redirección de Moodle para que termine el inicio de sesión. Mantén esta excepción sincronizada con el ajuste de autologin.';
$string['troubleshooting:interpretation_networks'] = 'Esta lista blanca controla qué IP de origen pueden llamar a la API LMS. Conserva las direcciones exactas oficiales de AppCRUE y las IP registradas expresamente para pruebas locales; los rangos CIDR amplios permiten el acceso de más sistemas.';
$string['troubleshooting:interpretation_rotation'] = 'Una rotación registrada confirma que este sitio Moodle recibió una solicitud de rotación del backend. El endpoint y el servicio de rotación deben estar habilitados para futuras rotaciones.';
$string['troubleshooting:interpretation_testuser'] = 'Esta comprobación busca una cuenta mediante el campo identificador configurado e inspecciona sus matrículas activas. No cambia la cuenta, las matrículas ni los permisos.';
$string['troubleshooting:interpretation_versions'] = 'La comparación usa el código instalado en este sitio y la versión más reciente que informa el comprobador de actualizaciones de plugins de Moodle. Si no hay resultado, puede que aún no se haya realizado una consulta o que el servicio de actualizaciones no esté disponible.';
$string['troubleshooting:intro'] = 'Las comprobaciones se ejecutan desde Moodle o desde tu navegador. No pueden confirmar que el backend remoto de AppCRUE acceda a Moodle; consulta sus registros de red o pide a su administrador que pruebe la URL del endpoint mostrada.';
$string['troubleshooting:ip_effective'] = 'IP efectiva que utiliza Moodle';
$string['troubleshooting:ip_header_order'] = 'Orden de fuentes IP de Moodle';
$string['troubleshooting:key'] = 'Clave de API';
$string['troubleshooting:keyinvalid'] = 'La clave de API guardada contiene caracteres no admitidos. Coordina una clave nueva con el backend de AppCRUE antes de cambiarla.';
$string['troubleshooting:keymissing'] = 'No hay una clave de API guardada. Si es una integración nueva, añade primero las IP oficiales de AppCRUE y luego habilita la autoconfiguración para que la primera solicitud de un backend de confianza registre su clave.';
$string['troubleshooting:keyok'] = 'Hay una clave de API guardada y utiliza caracteres admitidos.';
$string['troubleshooting:keyrotation'] = 'Rotación de clave API';
$string['troubleshooting:lastsuccessfulautologin'] = 'Autologins válidos completados';
$string['troubleshooting:lastsuccessfulautologin_found'] = 'Hay inicios de sesión con token válido en el periodo consultado. El último se registró el {$a}. Es un indicio de que el autologin funcionó en esa ocasión.';
$string['troubleshooting:loadfailed'] = 'No se pudo cargar el informe de diagnóstico. Vuelve a intentarlo.';
$string['troubleshooting:localtestip_value'] = '{$a} está permitida para pruebas desde el navegador. Es una excepción de seguridad temporal: elimínala al terminar. Si corresponde a un NAT o proxy compartido, otros equipos también pasarán el filtro de IP.';
$string['troubleshooting:localtestips'] = 'IPs de administrador registradas para pruebas locales';
$string['troubleshooting:log_api_accessed'] = 'Acceso correcto a la API LMS';
$string['troubleshooting:log_autologin_failed'] = 'Autologin fallido o uso de fallback';
$string['troubleshooting:log_autologin_login'] = 'Autologin correcto';
$string['troubleshooting:log_bad_apikey'] = 'Clave API LMS no válida';
$string['troubleshooting:log_badkey_official_critical'] = 'Se detectaron claves no coincidentes desde IP oficiales de Universia. Es un indicio crítico: compara la clave configurada en Moodle con la del backend.';
$string['troubleshooting:log_badkey_other_info'] = 'Los intentos con claves no coincidentes proceden de IP no oficiales; podrían corresponder a pruebas de técnicos. Confirma su origen.';
$string['troubleshooting:log_badkey_prefix'] = 'Accesos registrados con ';
$string['troubleshooting:log_badkey_source_summary'] = 'Clave API no válida: {$a->official} intento(s) desde IP oficial de Universia [{$a->officialips}] y {$a->other} desde otras IP [{$a->otherips}].';
$string['troubleshooting:log_endpoint'] = 'Endpoint';
$string['troubleshooting:log_event'] = 'Actividad';
$string['troubleshooting:log_ip'] = 'IP de origen';
$string['troubleshooting:log_ips'] = 'Direcciones IP registradas';
$string['troubleshooting:log_list_truncated'] = '(lista limitada a 50 elementos)';
$string['troubleshooting:log_noendpoint'] = '-';
$string['troubleshooting:log_time'] = 'Fecha y hora';
$string['troubleshooting:log_unauthorized_ip'] = 'Solicitud desde una IP no autorizada';
$string['troubleshooting:log_user'] = 'Usuario';
$string['troubleshooting:log_users'] = 'Usuarios registrados';
$string['troubleshooting:logdetails'] = 'Los registros de Moodle guardan los fallos de clave API y los resultados de autologin. Los accesos correctos a la API LMS se registrarán desde esta versión del plugin; las solicitudes correctas anteriores no se guardaban como eventos de AppCRUE.';
$string['troubleshooting:lognone'] = 'No se encontraron registros coincidentes en este periodo. Puede que el registro estándar de Moodle esté desactivado o tenga un periodo de retención menor.';
$string['troubleshooting:logs'] = 'Actividad de AppCRUE en los registros de Moodle';
$string['troubleshooting:logsince'] = 'Mostrar registros posteriores a esta fecha y hora (opcional)';
$string['troubleshooting:logsince_apply'] = 'Aplicar filtro de fecha';
$string['troubleshooting:logsince_clear'] = 'Quitar filtro de fecha';
$string['troubleshooting:logsince_invalid'] = 'Fecha y hora no válidas. Se muestran los últimos 30 días.';
$string['troubleshooting:logsince_timezone'] = 'Zona horaria: {$a}. Deja el campo vacío para consultar los últimos 30 días.';
$string['troubleshooting:logsintro'] = 'Resumen de los eventos coincidentes de los últimos 30 días. Cada tipo de actividad muestra su total y sus listas de usuarios e IP; las listas se limitan a 50 valores.';
$string['troubleshooting:logsintro_custom'] = 'Resumen de los eventos coincidentes desde {$a}. Cada tipo de actividad muestra su total y sus listas de usuarios e IP; las listas se limitan a 50 valores.';
$string['troubleshooting:logunavailable'] = 'La tabla de registros estándar de Moodle no está disponible; esta página no puede consultar los eventos guardados.';
$string['troubleshooting:messageappidmissing'] = 'El identificador de aplicación TwinPush está vacío. Configúralo en los ajustes de message_appcrue.';
$string['troubleshooting:messageappidok'] = 'Hay un identificador de aplicación TwinPush configurado.';
$string['troubleshooting:messagebuffer'] = 'Mensajes en cola: {$a->pending} pendientes y {$a->failed} fallidos.';
$string['troubleshooting:messagebuffermissing'] = 'La tabla de mensajes en cola de message_appcrue no está disponible; ejecuta la actualización de Moodle o desactiva el modo de cola.';
$string['troubleshooting:messagedisabled'] = 'Las notificaciones push están deshabilitadas en message_appcrue.';
$string['troubleshooting:messageenabled'] = 'Las notificaciones push están habilitadas.';
$string['troubleshooting:messagefieldinvalid'] = 'El campo {$a} configurado para buscar destinatarios push no está disponible. Selecciona un campo estándar o de perfil en los ajustes de message_appcrue.';
$string['troubleshooting:messagefieldok'] = 'El campo {$a} para buscar destinatarios push está disponible en Moodle.';
$string['troubleshooting:messageheading'] = 'Diagnóstico de notificaciones push (message_appcrue)';
$string['troubleshooting:messageintro'] = 'Estas comprobaciones revisan ajustes locales y el número de mensajes en cola. No muestran credenciales ni se conectan a TwinPush automáticamente.';
$string['troubleshooting:messagekeymissing'] = 'La clave API de TwinPush está vacía. Configúrala en los ajustes de message_appcrue.';
$string['troubleshooting:messagekeyok'] = 'Hay una clave API de TwinPush configurada. Su valor permanece oculto.';
$string['troubleshooting:messagelognote'] = 'message_appcrue escribe los detalles de entrega de TwinPush en la salida de tareas/cron de Moodle; no son eventos del registro estándar.';
$string['troubleshooting:messageprocessor_disabled'] = 'El plugin de salida de mensajes está deshabilitado. Habilítalo en los ajustes de salida de mensajes de Moodle si deben enviarse notificaciones AppCRUE.';
$string['troubleshooting:messageprocessor_enabled'] = 'El plugin de salida de mensajes está habilitado en Moodle.';
$string['troubleshooting:messagetest_configmissing'] = 'No están configurados la clave API o el App ID de message_appcrue.';
$string['troubleshooting:messagetest_deliveryerror'] = 'TwinPush notificó errores de entrega para los ID de usuario Moodle: {$a}.';
$string['troubleshooting:messagetest_exception'] = 'Falló la solicitud de message_appcrue: {$a}';
$string['troubleshooting:messagetest_noalias'] = 'El usuario encontrado no tiene un alias de dispositivo en el campo de correspondencia de message_appcrue «{$a}».';
$string['troubleshooting:messagetest_sent'] = 'Se envió una notificación push de prueba a {$a}.';
$string['troubleshooting:messagetest_title'] = 'Prueba de entrega de message_appcrue';
$string['troubleshooting:messagetestcodeunavailable'] = 'message_appcrue aparece instalado en Moodle, pero no está disponible su código; no se pudo ejecutar la prueba de entrega.';
$string['troubleshooting:messagetestconfirm_button'] = 'Confirmar y enviar push de prueba';
$string['troubleshooting:messagetestconfirm_prompt'] = 'No se ha enviado ninguna notificación push. Confirma abajo si quieres enviar una notificación de prueba real a este usuario.';
$string['troubleshooting:messagetestlink'] = 'Abrir la prueba de conexión de message_appcrue';
$string['troubleshooting:messagetestwarning'] = 'La prueba envía una notificación push real al usuario Moodle que indiques.';
$string['troubleshooting:mfa'] = 'Exclusión de redirección de Moodle MFA';
$string['troubleshooting:mfadisable'] = 'Moodle MFA está deshabilitado; no se necesita una exclusión para autologin de AppCRUE.';
$string['troubleshooting:mfamismatch'] = 'La exclusión de autologin de AppCRUE no coincide con el ajuste actual. Sincronízala conservando las demás exclusiones.';
$string['troubleshooting:mfaok'] = 'La exclusión de autologin de AppCRUE coincide con el ajuste actual de autologin.';
$string['troubleshooting:networks'] = 'Redes de origen de la API';
$string['troubleshooting:networksempty'] = 'No hay redes de origen configuradas. Se rechazarán las solicitudes a la API de widgets LMS.';
$string['troubleshooting:networkunrestricted'] = 'La lista contiene un rango que permite todas las direcciones. Elimínalo y conserva solo las IP o redes CIDR de confianza necesarias.';
$string['troubleshooting:noendpoint'] = 'No hay endpoints de widgets LMS habilitados, por lo que no se puede ejecutar la prueba del navegador. Habilita un endpoint solo si quieres compartir sus datos.';
$string['troubleshooting:officialmissing'] = 'La lista no cubre algunas IP oficiales de AppCRUE: {$a}. Añádelas si el sitio recibe solicitudes desde esos servidores.';
$string['troubleshooting:officialpresent'] = 'La lista configurada cubre las IP oficiales conocidas de AppCRUE.';
$string['troubleshooting:opensetting'] = 'Abrir el ajuste de {$a}';
$string['troubleshooting:proxyheaders'] = 'Cabeceras IP reenviadas y proxy inverso';
$string['troubleshooting:proxyheaders_review'] = 'Moodle está configurado para tener en cuenta cabeceras de IP reenviadas. Confirma que un proxy inverso de confianza elimina los valores enviados por el cliente y establece sus propias cabeceras. Configura Moodle para confiar solo en los proxies reales de la cadena; esta página no puede verificar qué equipo de red proporcionó estas cabeceras.';
$string['troubleshooting:pushnote'] = 'Las credenciales de notificaciones push pertenecen al plugin independiente message_appcrue. Esta página no puede validarlas ni modificarlas.';
$string['troubleshooting:remove_local_test_ip'] = 'Eliminar esta IP de pruebas locales';
$string['troubleshooting:retry'] = 'Reintentar';
$string['troubleshooting:rotation_disabled'] = 'deshabilitado';
$string['troubleshooting:rotation_enable_button'] = 'Habilitar endpoint y servicio';
$string['troubleshooting:rotation_enabled'] = 'habilitado';
$string['troubleshooting:rotation_not_seen'] = 'No hay una rotación registrada, así que esta instalación no confirma actividad del backend mediante este mecanismo. Endpoint: {$a->endpoint}; servicio de rotación: {$a->service}.';
$string['troubleshooting:rotation_seen'] = 'Hay una rotación registrada el {$a->date}, lo que implica actividad recibida del backend. Endpoint: {$a->endpoint}; servicio de rotación: {$a->service}.';
$string['troubleshooting:runningtests'] = 'Ejecutando tests... (hasta {$a} comprobaciones)';
$string['troubleshooting:settingsbutton'] = 'Abrir diagnóstico de conexión';
$string['troubleshooting:statuserror'] = 'Problema detectado';
$string['troubleshooting:statusinfo'] = 'Información';
$string['troubleshooting:statusok'] = 'Correcto';
$string['troubleshooting:statuswarning'] = 'Requiere atención';
$string['troubleshooting:syncmfa'] = 'Sincronizar exclusión MFA';
$string['troubleshooting:tasklogslink'] = 'Abrir registros de tareas programadas de Moodle';
$string['troubleshooting:testuser'] = 'Prueba de correspondencia de usuario';
$string['troubleshooting:testuserbutton'] = 'Comprobar usuario';
$string['troubleshooting:title'] = 'Diagnóstico de conexión AppCRUE';
$string['troubleshooting:usercandidate'] = 'El valor coincide con una cuenta activa en el campo Moodle {$a}. Puedes establecer ese campo como correspondencia global de usuarios de AppCRUE.';
$string['troubleshooting:userfieldchanged'] = 'El campo de correspondencia de AppCRUE se ha actualizado a {$a}.';
$string['troubleshooting:userfound'] = 'El valor coincide con una cuenta Moodle activa. La cuenta está matriculada en {$a} curso(s) activo(s).';
$string['troubleshooting:userinactive'] = 'El valor coincide con una cuenta eliminada o suspendida. Reactívala en Moodle antes de usarla con AppCRUE.';
$string['troubleshooting:usernocourses'] = 'La cuenta existe, pero no tiene matrículas activas. Matricúlala en los cursos necesarios; esta herramienta no modifica el acceso a cursos.';
$string['troubleshooting:usernotfound'] = 'Ninguna cuenta coincide con el valor en el campo configurado. Comprueba el parámetro de usuario y la correspondencia del campo de perfil.';
$string['troubleshooting:version_installed'] = 'Versión instalada en la base de datos:';
$string['troubleshooting:version_no_update'] = 'Código desplegado: {$a->code} ({$a->release}). Moodle no encontró una versión publicada más reciente a fecha de {$a->checked}.';
$string['troubleshooting:version_norelease'] = 'sin versión de publicación declarada';
$string['troubleshooting:version_not_checked'] = 'Todavía no se ha completado una consulta remota. Fuerza la comprobación para saber si hay una versión más reciente publicada.';
$string['troubleshooting:version_unavailable'] = 'Moodle no puede comparar versiones publicadas porque no está disponible la versión del código de este plugin.';
$string['troubleshooting:version_update_found'] = 'Código desplegado: {$a->code} ({$a->release}). Moodle informa de una versión publicada más reciente: {$a->remote} ({$a->remoterelease}).';
$string['troubleshooting:versioncheckdisabled'] = 'La comprobación de actualizaciones de plugins está deshabilitada en config.php; no se puede consultar una versión remota nueva.';
$string['troubleshooting:versioncheckdisabled_item'] = 'No se puede verificar si hay una actualización porque la comprobación de plugins de Moodle está deshabilitada.';
$string['troubleshooting:versioncheckintro'] = 'Moodle consulta su servicio de actualizaciones para buscar versiones publicadas. Al forzar la comprobación se renueva la información almacenada en caché por Moodle.';
$string['troubleshooting:versionfetchfailed'] = 'Moodle no pudo actualizar las versiones publicadas. Revisa el acceso HTTPS saliente y los ajustes de actualizaciones de Moodle; se muestra el resultado anterior almacenado.';
$string['troubleshooting:versionfetchsuccess'] = 'Moodle ha actualizado la información de versiones publicadas de los plugins.';
$string['troubleshooting:versionrefresh'] = 'Forzar comprobación ahora';
$string['troubleshooting:versions'] = 'Versiones instaladas y disponibles de los plugins';
$string['welcome_message'] = '<p>Estás usando la versión {$a->installedversion} del plugin.</p>' .
    '<p>Este plugin permite la integración de Moodle con las aplicaciones móviles de AppCrue. ' .
    'Proporciona servicios para autologin, recuperación de avatar, generación de mapas de sitio e integración de la API LMS-AppCrue.</p>' .
    '<p>Por favor, configure los ajustes del plugin para conectar su sitio Moodle con los servicios de AppCrue.</p>' .
    '<h3>Autoconfiguración de la API LMS-AppCrue</h3>' .
    '<div class="alert alert-danger">Nunca habilite la configuración de autoconfiguración si no está seguro sobre la seguridad de su red o si no espera recibir solicitudes desde los servidores de AppCrue, ya que podría permitir el acceso no autorizado a su sitio Moodle.</div>' .
    '<p>Para autoconfigurar la integración de la API LMS-AppCrue, siga estos pasos:</p>' .
    '<ol>' .
    '<li>Asegúrese de que su sitio Moodle esté registrado en UNIVERSIA y sea accesible desde los servidores de AppCrue.</li>' .
    '<li>Habilite la configuración "Habilitar procedimiento de autoconfiguración de AppCRUE" en la configuración del plugin.</li>' .
    '<li>Desde la aplicación móvil AppCrue, inicie una petición de un widget con información de Moodle.</li>' .
    '<li>El plugin agregará automáticamente las IP oficiales de los servidores de AppCrue, habilitará el servicio de rotación de claves y almacenará la primera clave API recibida de AppCrue.</li>' .
    '<li>La configuración de autoconfiguración se deshabilitará automáticamente después de la primera configuración exitosa para evitar cambios no deseados. Puede volver a habilitarla si necesita restablecer la configuración.</li>' .
    '</ol>';
