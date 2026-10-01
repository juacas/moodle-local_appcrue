# Changelog

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
