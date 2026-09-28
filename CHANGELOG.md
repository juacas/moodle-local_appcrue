# Changelog

## v2.0.10 - 2026-09-28

- Apply user-context filters to text returned by the AppCrue JSON endpoints, including course names, activity titles, forum titles, announcements, calendar events, and grade item names.
- Set the requested user's language while impersonating them so filters such as `mlang` return the appropriate language variant.
- Preserve the correct format and filtering context for forum messages and grade feedback.
