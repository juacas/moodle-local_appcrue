# Changelog

## v2.0.10 - 2026-09-28

- Apply user-context filters to text returned by the AppCrue JSON endpoints, including course names, activity titles, forum titles, announcements, calendar events, and grade item names.
- Set the requested user's language while impersonating them so filters such as `mlang` return the appropriate language variant.
- Preserve the correct format and filtering context for forum messages and grade feedback.
- Añadida una batería PHPUnit para los servicios, autenticación, funciones
  externas y endpoints dinámicos de AppCrue.
- Cubiertos los filtros de contexto e idioma para los textos multilingües
  devueltos por la API.
- Corregida la compatibilidad de la API externa con `core_external` de Moodle.
