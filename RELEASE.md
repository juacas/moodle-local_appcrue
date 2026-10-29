# AppCRUE v2.2.0 release notes (2026-10-29)

## Connection troubleshooting

Site administrators can open a new troubleshooting page from the AppCRUE settings. It checks installed and available plugin versions, API credentials and key rotation, the source-IP allowlist, Moodle MFA exclusions, UNIVERSIA IdP connectivity, enabled LMS endpoints, user matching, and recent AppCRUE activity in Moodle's standard log. Results explain what was observed and offer specific configuration actions where appropriate.

The page can test an enabled endpoint from the browser without credentials or call each enabled endpoint over HTTP with the configured API key and a supplied user identifier. Endpoint results include HTTP status, item count, date range, response time, and expandable JSON. When `message_appcrue` is installed, the page also reports its configuration and can send a test push notification after explicit confirmation.

During server-side endpoint tests, the page temporarily adds `127.0.0.1` to the API source-IP allowlist and removes it afterward. Administrators should review the allowlist after testing, particularly if a test is interrupted.

## Other changes

- Record successful LMS API accesses as Moodle events without logging API credentials. Log summaries now group activity by type and show the users and source IPs involved.
- Fix the forums endpoint's top-level `count` so it matches the number of entries in `data.forums`.
- Stop including old and new API keys in key-rotation debugging messages.
- Detect any valid IPv4 or IPv6 `/0` network as an unrestricted API allowlist entry.
- Expand automated tests and update the CI compatibility matrix.

The minimum supported Moodle version remains 4.5.
