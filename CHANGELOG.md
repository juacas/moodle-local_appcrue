# Changelog

## v2.2.0 - 2026-10-29

### Added

- Add an administrator troubleshooting page linked from the plugin settings. It checks published plugin updates through Moodle's update checker, API key and rotation status, official and additional source IPs, proxy-related IP information, MFA exclusions, IdP connectivity, enabled endpoints, and user matching.
- Add a configurable date filter and per-activity summaries of AppCRUE events in Moodle's standard log, including totals and limited lists of users and source IPs. Record successful API access as a new Moodle event without storing API credentials.
- Add a browser-to-Moodle endpoint probe and real HTTP tests for enabled LMS endpoints. Show the HTTP status, response time, item count, date range, and expandable JSON for each response, including expected user-not-found errors.
- Show `message_appcrue` configuration diagnostics when that plugin is installed. A real test push notification requires a separate administrator confirmation.
- Offer targeted administrator actions to correct the API source-IP list, register or remove a local test IP, enable required services and endpoints, synchronize the Moodle MFA exclusion, and update the user-matching field.

### Changed

- Make the forums endpoint's top-level `count` equal the number of forum entries in `data.forums`.
- Update the CI matrix and MariaDB health check, and add regression tests for diagnostic helpers and API access events.

### Security

- Remove old and new API key values from key-rotation debugging messages.
- Recognize any valid IPv4 or IPv6 `/0` network as unrestricted when checking the API allowlist.
- Escape the displayed endpoint URL and diagnostic response data. Configuration actions require site-administration permission and a valid Moodle session key.

## v2.1.0 - 2026-10-08

### Security

- Fix the XSS issues reported in [GitHub issue #11](https://github.com/juacas/moodle-local_appcrue/issues/11). Escape stored invalid API keys before displaying them in plugin settings and reject malformed credentials from both the `apikey` parameter and `X-API-KEY` header.
- Validate explicit autologin `urltogo` values as local Moodle destinations. Reject external URLs, malformed input, browser path-normalization tricks, and path traversal.
- Encode redirect destinations as JavaScript data before embedding the automatic redirect script, including destinations generated from administrator-configured patterns.
- Add regression coverage for API-key validation, safe rendering of stored values, `urltogo` validation, and redirect script encoding.

### Compatibility

- API keys must use ASCII letters, digits, hyphens, or underscores. Explicit `urltogo` destinations must remain within the Moodle installation; external destinations are no longer accepted.

## v2.0.10 - 2026-10-01

### Features and improvements

- Add a **Use PRE server** setting to validate Universia tokens against the pre-production endpoint when a custom IdP is not configured.
- Automatically add `/local/appcrue/autologin.php` to Moodle's MFA redirect exclusions when autologin is enabled, and remove it when autologin is disabled. Preserve other exclusions and show a settings notice with a link to `redir_exclusions` when both autologin and Moodle MFA are active.
- Enable the autologin redirection page and API key rotation by default for new installations. Support relative LMS URLs in autologin deep links.
- Apply the requested user's language and context filters to course names, activity titles, forum names and discussions, announcements, calendar events, files, grades, and feedback returned by the JSON endpoints.
- Preserve text formats and filtering contexts for forum messages and grade feedback.

### Compatibility and testing

- Set Moodle 4.5 as the minimum supported version and update external API integration for Moodle's `core_external` classes.
- Add PHPUnit tests for services, authentication, external functions, dynamic endpoints, and MFA exclusion handling. Document how to set up and run the plugin test suite.
