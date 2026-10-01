# Tests de AppCrue

Esta carpeta contiene la batería PHPUnit del plugin `local_appcrue`. Los tests
usan `advanced_testcase`, los generadores de datos de Moodle y una base de
datos PHPUnit; cada caso restaura el estado de la base de datos al terminar.

## Qué se prueba

- `appcrue_service_test.php`: utilidades comunes de los servicios, resolución
  de endpoints y formateo contextual de textos.
- `locallib_test.php`: recorrido de JSON, sitemap, clasificación de eventos,
  API key, identificación de usuario y configuración del idioma al suplantar.
- `assignments_service_test.php`: ventanas temporales, asignaturas inscritas
  y filtrado de nombres, descripciones y cursos.
- `files_service_test.php`: recursos y carpetas visibles, URLs de descarga y
  exclusión por fecha.
- `grades_service_test.php`: notas finales, feedback, nombres filtrados y
  configuración del total del curso.
- `calendar_service_test.php`: rango temporal, categorías EXAMEN/HORARIO,
  agrupación diaria, URLs y filtrado de textos del evento.
- `forums_service_test.php`: árbol de respuestas, discusiones, permisos y
  foros sin discusiones.
- `announcements_service_test.php`: anuncios del foro de novedades, texto
  del primer post y ventana temporal.
- `externallib_test.php`: validación de parámetros, envío de mensajes,
  destinatarios desconocidos y descripciones de retorno de las funciones
  externas.
- `endpoints_test.php`: resolución de los siete endpoints REST dinámicos,
  errores de endpoint, mapa de códigos y las tres declaraciones de
  `db/services.php`.
- `mfa_helper_test.php`: alta y retirada de la excepción MFA de autologin,
  conservación de otras URLs y prevención de duplicados.
- `autologin_logic_test.php`, `keyrotation_service_test.php`,
  `network_security_helper_test.php` y `privacy_provider_test.php`: pruebas
  existentes de autologin, rotación de claves, seguridad de red y privacidad.

Los casos de integración crean cursos, usuarios, matrículas, actividades,
foros, recursos, eventos y calificaciones. También habilitan `multilang2` y
comprueban que una cadena como `{mlang en}English{mlang}{mlang es}Español{mlang}`
se devuelve en el idioma del usuario, sin las etiquetas `{mlang}`.

## Requisitos

1. Moodle debe estar instalado y el plugin debe estar actualizado.
2. Debe existir una base de datos PHPUnit configurada en `config.php`.
3. Las dependencias de Moodle deben estar instaladas, incluido
   `vendor/bin/phpunit`.

## Cómo ejecutarlos

Desde la raíz de Moodle, usando su `phpunit.xml`.
Para preparar PHPUnit por primera vez, consulta
[Tests en el README principal](../README.md#tests).

Toda la suite:

```bash
vendor/bin/phpunit -c phpunit.xml --testsuite local_appcrue_testsuite
```

Para ver los nombres de los casos:

```bash
vendor/bin/phpunit -c phpunit.xml --testsuite local_appcrue_testsuite --testdox
```

Para ejecutar un grupo concreto:

```bash
vendor/bin/phpunit -c phpunit.xml --testsuite local_appcrue_testsuite --filter assignments_service_test
```

También se puede filtrar por método, por ejemplo:

```bash
vendor/bin/phpunit -c phpunit.xml --testsuite local_appcrue_testsuite \
  --filter test_get_items_returns_enrolled_assignment
```

No se deben lanzar varios procesos PHPUnit de este plugin en paralelo: todos
usan la misma base de datos PHPUnit y los tests de Moodle resetean su estado.
