# Servicios AppCRUE

Este complemento local de Moodle ofrece al backend y a la aplicación móvil AppCRUE datos del LMS asociados a cada usuario. También incluye inicio de sesión mediante token, endpoints de calendario, avatar y mapa del sitio, y servicios web de Moodle para enviar mensajes y notificaciones de calificaciones.

## Instalación y solución de problemas

### Instalación

1. Instala el complemento en el directorio `local/appcrue` de Moodle, desde un paquete de una versión publicada o mediante Git.
2. Inicia sesión como administrador del sitio y abre **Administración del sitio → Notificaciones** para completar la actualización.
3. Abre **Administración del sitio → Plugins → Plugins locales → AppCrue Connection Services**, o accede directamente a `/admin/settings.php?section=local_appcrue`.
4. Configura la clave de API, las redes de origen permitidas, la correspondencia de usuarios y los endpoints que necesite tu integración con AppCRUE. Consulta [Ajustes](#ajustes) y [APIs de integración con el LMS](#apis-de-integracion-con-el-lms).
5. Configura el backend de AppCRUE con la URL base de Moodle y la clave de API mediante un sistema seguro de gestión de secretos. Usa HTTPS.

Los endpoints del complemento dependen de las bibliotecas de Moodle y requieren una versión de Moodle compatible con la versión del complemento. Al actualizar, sigue el procedimiento habitual de actualización de plugins de Moodle y revisa las notas de la versión.

### Comprobaciones de la primera conexión

Antes de probar, comprueba que:

- El backend de AppCRUE puede acceder al sitio Moodle mediante HTTPS. Si hay tiempos de espera o errores del servidor, revisa DNS, certificados TLS, proxies inversos, reglas WAF y cortafuegos.
- La IP de origen del servidor de AppCRUE está incluida en **API authorized networks** para las solicitudes a `/local/appcrue/appcrue.php/*`. Añade únicamente las IP o redes CIDR necesarias; no uses un rango comodín como atajo de diagnóstico.
- Está habilitado el endpoint de la API LMS que se va a probar.
- El usuario de AppCRUE existe en Moodle y el identificador de la solicitud coincide con el campo de perfil seleccionado. Para obtener datos, el usuario también necesita acceso a cursos con contenido relevante.
- La solicitud utiliza el parámetro de identidad configurado (`studentemail` o `username`) y una clave de API válida, preferiblemente en la cabecera `X-API-KEY`.

Una solicitud a `/local/appcrue/appcrue.php/forums` sin credenciales sirve para comprobar la instalación si el cliente está en una red permitida: debe llegar a Moodle y devolver un error estructurado de credenciales ausentes. Un `403` suele indicar que la solicitud no ha superado el control de red; un `404` puede significar que el endpoint está deshabilitado o que la ruta no está disponible. Un error de autenticación indica que la solicitud ha llegado al plugin, pero las credenciales necesitan revisión. El código HTTP y los detalles exactos pueden variar según el endpoint y la configuración de Moodle.

### Autoconfiguración de AppCRUE

El ajuste **Enable AppCRUE autoconfig procedure** está pensado para la configuración inicial. Ante una solicitud válida desde un servidor de AppCRUE aceptado, puede añadir las IP oficiales de AppCRUE a la lista de redes, guardar la primera clave de API, habilitar la rotación de claves y desactivarse automáticamente. Revisa después la clave y la lista de redes en los ajustes. Limita la lista a las direcciones de confianza del backend.

### Diagnóstico

| Síntoma | Comprobaciones |
| --- | --- |
| Tiempo de espera o error HTTP 5xx | Comprueba que Moodle esté disponible y revisa TLS, proxy inverso, WAF y cortafuegos de entrada. Confirma que el backend de AppCRUE usa la URL base y la ruta correctas. |
| HTTP 403 en `appcrue.php` | Comprueba **API authorized networks** y la dirección del cliente que recibe Moodle. Si Moodle está detrás de un proxy, configura los proxies de confianza de Moodle para que se identifique correctamente la IP del cliente. |
| Clave de API ausente o no válida | Confirma la **Local API key** actual, usa la cabecera `X-API-KEY` y comprueba que no haya espacios ni codificación accidental de la URL. Las claves admiten letras ASCII, números, guiones y guiones bajos. No las incluyas en tickets ni registros. |
| No se encuentra al usuario | Comprueba si **Use user parameter for matching** está configurado como `studentemail` o `username`, y si **Field for matching user's profile** apunta al campo de Moodle que contiene ese valor. Revisa mayúsculas, minúsculas y valores duplicados. |
| El calendario, las calificaciones, los archivos, los foros, los anuncios o las tareas aparecen vacíos | Habilita el endpoint correspondiente, comprueba que el usuario tenga acceso a cursos con esos datos y revisa el periodo de consulta. En los servicios cuyo ajuste lo indica, el valor `0` elimina el límite de antigüedad. |
| Falla el inicio de sesión o aparece MFA | Comprueba el modo del IdP, el endpoint de validación del token y la correspondencia con el campo de usuario. Si Moodle MFA está activo y autologin de AppCRUE está habilitado, el plugin añade su ruta a las exclusiones de redirección de Moodle MFA; la página de ajustes incluye un enlace a la búsqueda correspondiente. |
| Falla la prueba de notificaciones push | Revisa el plugin independiente `message_appcrue`, las credenciales del proveedor push y el registro del usuario en AppCRUE. La clave de API LMS de este plugin no es la credencial del proveedor push. |

Para probar el filtro de IP, añade temporalmente la IP de origen conocida del equipo de pruebas como una entrada `/32` (IPv4) o `/128` (IPv6), haz la prueba y luego elimínala. No uses `0.0.0.0/0`, `::/0` ni otro rango que permita todas las direcciones para diagnosticar un sistema en producción. Evita enviar claves de API en parámetros de URL: los proxies, navegadores y aplicaciones pueden guardar esas URL en registros.

## APIs de integración con el LMS

El backend de AppCRUE llama al endpoint con argumento de ruta `/local/appcrue/appcrue.php/{endpoint}`. La solicitud se autentica con una clave de API y un identificador de usuario, o con un token de un proveedor de identidad cuya respuesta validada identifica al usuario. Para las solicitudes con clave de API, el plugin busca al usuario usando el parámetro configurado y el campo de perfil de Moodle.

El controlador `appcrue.php` comprueba que el cliente esté en **API authorized networks** antes de procesar las solicitudes. Envía la clave de API en la cabecera HTTP `X-API-KEY`. El parámetro de consulta `apikey` sigue disponible por compatibilidad, pero puede dejar la clave expuesta en los registros.

| Endpoint | Identificador requerido con clave de API | Parámetros opcionales | Respuesta |
| --- | --- | --- | --- |
| `/calendar` | `studentemail` o `username` | `timestart`, `timeend` (marcas de tiempo Unix) | Eventos del calendario en el intervalo solicitado o en el periodo predeterminado. |
| `/forums` | `studentemail` o `username` | `timestart` (marca de tiempo Unix) | Publicaciones de foros visibles para el usuario, agrupadas por debate. |
| `/grades` | `studentemail` o `username` | `timestart` (marca de tiempo Unix) | Calificaciones del usuario, limitadas por el periodo configurado. |
| `/announcements` | `studentemail` o `username` | `timestart` (marca de tiempo Unix) | Anuncios visibles para el usuario en los foros de noticias de sus cursos. |
| `/files` | `studentemail` o `username` | `timestart` (marca de tiempo Unix) | Archivos de cursos visibles para el usuario y enlaces de descarga. Los archivos antiguos de curso son opcionales. |
| `/assignments` | `studentemail` o `username` | `timestart` (marca de tiempo Unix) | Tareas de actividades compatibles, con fechas de entrega y estado. |
| `/keyrotation` | Ninguno; solo requiere la clave de API | `newapikey` | Sustituye la clave actual. El ajuste **Enable API rotation endpoint** publica la ruta; la autoconfiguración inicial también habilita el indicador interno de rotación. |

Ejemplo de solicitud usando la búsqueda por correo electrónico:

```bash
curl --get 'https://moodle.example.edu/local/appcrue/appcrue.php/forums' \
  --data-urlencode 'studentemail=student@example.edu' \
  --header "X-API-KEY: ${APPCRUE_API_KEY}"
```

Si **Use user parameter for matching** está configurado como `username`, envía `username` en lugar de `studentemail`. El ajuste **Field for matching user's profile** selecciona el campo de Moodle que se compara con ese valor. Guarda la clave en un gestor de secretos o en una variable de entorno protegida; no la incluyas en el repositorio ni en el historial de comandos compartido.

Otros endpoints de AppCRUE:

| Endpoint | Función y parámetros | Autenticación / control de red |
| --- | --- | --- |
| `/local/appcrue/usercalendar.php` | Respuesta del calendario clásico. Acepta `lang` (obligatorio), `fromDate` y `toDate` (`YYYYMMDD`), y `category` (opcional). | Token de usuario o clave de API; no utiliza la lista de redes de `appcrue.php`. |
| `/local/appcrue/avatar.php` | Imagen del usuario; `mode=base64` (predeterminado) o `mode=raw`. | Token de usuario o clave de API; no utiliza la lista de redes de `appcrue.php`. |
| `/local/appcrue/sitemap.php` | Árbol de cursos y categorías; acepta `category`, `courses`, `hidden[]` y `endurls`. | No requiere clave de API. Habilita el ajuste del mapa del sitio y, si es necesario, limita el acceso desde la red o la infraestructura de Moodle. |
| `/local/appcrue/autologin.php` | Valida un token del proveedor de identidad y redirige a un enlace profundo de Moodle. Admite `fallback`, `urltogo`, `course`, `group`, `pattern` y `param1`–`param3`. | Token del IdP en el parámetro configurado o en una cabecera Bearer. `urltogo` se restringe a este sitio Moodle. |

Las rutas independientes `usercalendar.php` y `avatar.php` aceptan la clave compartida por compatibilidad, pero no aplican la lista de redes de origen de `/appcrue.php`. Si estas rutas están expuestas a redes no confiables, aplica controles de acceso adecuados en el proxy inverso o el cortafuegos.

## Servicios web de Moodle

El plugin registra el servicio externo `external_notifications` (nombre corto `external_notifications`). Está deshabilitado de forma predeterminada y restringido a usuarios asociados explícitamente por un administrador de Moodle. Habilita los servicios web de Moodle y este servicio, vincula un usuario dedicado y genera un token para él. El usuario necesita la capacidad `moodle/site:sendmessage` para las funciones registradas.

Funciones registradas:

- `local_appcrue_send_instant_message`
- `local_appcrue_send_instant_messages`
- `local_appcrue_notify_grade`

Llámalas mediante el endpoint REST de Moodle, normalmente `/webservice/rest/server.php`, con los parámetros estándar `wstoken`, `wsfunction` y `moodlewsrestformat=json`. Usa una cuenta dedicada y restringida, y mantén secreto su token de servicio web.

## Ajustes

Abre **Administración del sitio → Plugins → Plugins locales → AppCrue Connection Services** o `/admin/settings.php?section=local_appcrue`. Moodle muestra un icono de ayuda junto a cada ajuste. Las capturas muestran los controles actuales con valores predeterminados; las claves y direcciones de red específicas del sitio están ocultas.

### Conexión y clave de API

![Ajustes de conexión y redes](pix/screenshots/settings-connection.png)

- **Enable AppCRUE autoconfig procedure**: inicia el proceso de configuración descrito anteriormente.
- **Local API key**: secreto compartido para las solicitudes con clave de API. Admite letras ASCII, números, guiones y guiones bajos.
- **API authorized networks**: direcciones IP o redes CIDR autorizadas para llamar a la API de widgets LMS mediante `appcrue.php`.
- **Enable API rotation endpoint**: publica `/keyrotation`. La autoconfiguración inicial activa el indicador interno que utiliza el servicio de rotación de claves.

### Proveedor de identidad y correspondencia de usuarios

![Ajustes del proveedor de identidad](pix/screenshots/settings-identity-provider.png)

- **Use custom IdP**: utiliza el endpoint de validación de tokens de la institución en lugar del IdP de AppCRUE.
- **Use PRE server**: si no se usa un IdP personalizado, valida los tokens en el servidor de preproducción de Universia en lugar del de producción.
- **AppCrue AppId / AppCrue API token**: credenciales de cliente del IdP de AppCRUE; mantén el token en secreto.
- **IdP token endpoint URL** y **IdP user JSON path**: configuran la respuesta de validación del token personalizado y el campo JSON que contiene el identificador del usuario Moodle.
- **Field for matching user's profile**: campo del perfil Moodle que se usa para encontrar la cuenta identificada por la validación del token.

### Autologin

![Ajustes de autologin](pix/screenshots/settings-autologin.png)

- **Enable autologin**: habilita el inicio de sesión mediante token y las redirecciones a enlaces profundos.
- **Deep URL token mark**: selecciona el marcador del token que se utilizará en los enlaces profundos generados.
- **Allow continue**: permite o impide continuar como invitado cuando falla o falta el token y se solicita `fallback=continue`.
- **Use redirection page**, **Course pattern**, **Follow metacourses** y **List of URL patterns**: controlan el flujo de redirección y la generación de URL a partir de cursos o patrones.
- Si Moodle MFA está activo, al habilitar autologin el plugin añade `/local/appcrue/autologin.php` a `tool_mfa | redir_exclusions`; al deshabilitarlo, elimina esa entrada. La página muestra un aviso con un enlace a la búsqueda del ajuste `redir_exclusions` de Moodle.

### Otros servicios AppCRUE

![Ajustes de otros servicios AppCRUE](pix/screenshots/settings-services.png)

- **Avatar service** y **Sitemap service** habilitan los endpoints clásicos correspondientes.
- **Cache sitemap** y **Sitemap cache TTL** configuran la caché del mapa del sitio.
- **Web service for notifying grades** selecciona el remitente de las notificaciones de calificaciones.

### APIs de widgets LMS

![Ajustes del calendario de la API LMS](pix/screenshots/settings-lms-api-calendar.png)

- **Use user parameter for matching** selecciona si AppCRUE identifica al usuario por correo electrónico o por nombre de usuario.
- **Field for matching user's profile** selecciona el campo de Moodle que se compara con el valor recibido.
- **Calendar** habilita el endpoint de widgets, establece el periodo predeterminado anterior y posterior a la fecha actual, selecciona si se comparten eventos del sitio, de cursos y personales, y marca los tipos de actividad que se consideran exámenes. El endpoint clásico de calendario de usuario tiene su propio ajuste de activación.

![Ajustes de datos de la API LMS](pix/screenshots/settings-lms-api-data.png)

- **Grades**, **Forums**, **Announcements**, **Files** y **Assignments** habilitan cada endpoint y configuran su periodo de consulta. En estos periodos, `0` incluye todos los elementos, independientemente de su antigüedad.
- **Show total grade as final grade** indica que la calificación total del curso se informe como calificación final.
- **Include legacy course files** incluye archivos del área antigua de archivos del curso de Moodle. Habilítalo solo si esos archivos se gestionan adecuadamente.
- **Assignments activity mapping** define una correspondencia por línea con el formato `mod_name|table|start-date-field|due-date-field`. El ajuste muestra la correspondencia predeterminada.

## Pruebas

Ejecuta las pruebas desde la raíz del repositorio Moodle (el directorio que contiene `composer.json`, `vendor/` y `phpunit.xml`). Por ejemplo:

```bash
cd /var/www/moodle
```

### Configuración inicial

Instala las dependencias de desarrollo de Moodle con `composer install`. Configura un directorio de datos PHPUnit y un prefijo de tablas dedicados en `config.php` de Moodle, antes de cargar `lib/setup.php`:

```php
$CFG->phpunit_prefix = 'phpu_';
$CFG->phpunit_dataroot = '/path/to/moodle-phpunit-data';
```

Usa un directorio de datos con permisos de escritura y un prefijo distinto del que utiliza la instalación normal de Moodle. Moodle crea y reinicia las tablas y los datos de prueba. Después, inicializa el entorno de pruebas y genera el `phpunit.xml` de la raíz de Moodle:

```bash
php public/admin/tool/phpunit/cli/init.php --disable-composer
```

Vuelve a ejecutar la inicialización después de instalar o actualizar plugins si PHPUnit indica que debe actualizarse el entorno de pruebas. No es necesario repetir este paso antes de cada ejecución si el entorno ya está inicializado. `--disable-composer` conserva las versiones de dependencias instaladas.

### Ejecutar las pruebas del plugin

Ejecuta todas las pruebas de AppCRUE mediante la suite registrada en la configuración raíz de Moodle:

```bash
vendor/bin/phpunit -c phpunit.xml --testsuite local_appcrue_testsuite
```

Para mostrar nombres de pruebas más legibles:

```bash
vendor/bin/phpunit -c phpunit.xml --testsuite local_appcrue_testsuite --testdox
```

Para ejecutar un archivo de pruebas concreto, por ejemplo las pruebas de exclusión de MFA en autologin:

```bash
vendor/bin/phpunit -c phpunit.xml public/local/appcrue/tests/mfa_helper_test.php
```

Para ejecutar un método de prueba concreto:

```bash
vendor/bin/phpunit -c phpunit.xml public/local/appcrue/tests/mfa_helper_test.php \
  --filter test_disable_removes_only_autologin
```

Para ver los detalles de los avisos de obsolescencia de PHPUnit:

```bash
vendor/bin/phpunit -c phpunit.xml --testsuite local_appcrue_testsuite \
  --display-phpunit-deprecations
```

Usa el `phpunit.xml` de la raíz de esta instalación. El archivo `phpunit.xml.dist` del plugin contiene opciones antiguas de PHPUnit y no incluye la extensión de pruebas actual de Moodle. Si la suite no aparece en la configuración raíz, vuelve a generarla con el comando de inicialización anterior.

Ejecuta las pruebas de forma secuencial si comparten una base de datos PHPUnit: Moodle reinicia su estado entre pruebas. Si está instalado `multilang2`, las pruebas de integración comprueban también el texto traducido; si no, comprueban que se conserve el texto original. Consulta [tests/README.md](tests/README.md) para ver el inventario de pruebas.

## ¿Qué es AppCRUE?

AppCRUE (https://tic.crue.org/app-crue/) es una aplicación móvil desarrollada por CRUE (Conferencia de Rectores de las Universidades Españolas) y Banco Santander. La plataforma indica que cuenta con:

- Más de 200 instituciones educativas públicas y privadas, con aplicaciones personalizables en 7 países.
- 128 aplicaciones distribuidas en las tiendas de aplicaciones de España (Apple Store y Google Play).
- Más de 3 millones de usuarios registrados.
- Más de 500 millones de páginas vistas en 2025.
- Más de 50 millones de visitas en 2025.
- Más de 60 millones de notificaciones push enviadas en 2025.
- Más de 1 millón de usuarios mensuales en los últimos 30 días.
- Más de 110 servicios disponibles.

## Licencia

Este programa es software libre: puedes redistribuirlo o modificarlo según los términos de la Licencia Pública General GNU publicada por la Free Software Foundation, versión 3 o (a tu elección) cualquier versión posterior.

Este programa se distribuye con la esperanza de que sea útil, pero SIN NINGUNA GARANTÍA; ni siquiera la garantía implícita de COMERCIABILIDAD o IDONEIDAD PARA UN PROPÓSITO PARTICULAR. Consulta la Licencia Pública General GNU para obtener más detalles.

Debes haber recibido una copia de la Licencia Pública General GNU junto con este programa. Si no es así, consulta <http://www.gnu.org/licenses/>.

## Notas de versión

Consulta [RELEASE.md](RELEASE.md) para ver las notas de versión.

## Registro de cambios

Consulta [CHANGELOG.md](CHANGELOG.md) para ver el historial de cambios.
