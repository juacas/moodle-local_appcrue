## Release Notes for Appcrue Plugin
### Version 2.0.10 (2026-10-01)
#### Features and Improvements
- Add a setting to validate Universia tokens against the PRE endpoint when a custom IdP is not used.
- Add and remove the AppCrue autologin URL from Moodle MFA redirect exclusions when autologin is enabled or disabled. When both autologin and Moodle MFA are active, show a notice linking to the exclusion setting.
- Use the redirection page and API key rotation by default for new installations. Support relative LMS URLs in autologin deep links.
- Apply the requested user's language and context filters to course names, activity titles, forum and discussion titles, announcements, calendar events, files, grades, and feedback returned by the JSON endpoints.
- Require Moodle 4.5 or later and update the external API integration for current Moodle `core_external` classes.
- Add PHPUnit coverage for services, authentication, dynamic endpoints, external functions, and MFA configuration, with instructions for running the tests.

### Version 2.0.9 (2026-06-18)
#### Changes and Improvements
- Change order of network restrictions check.

Other notes
- Full commit history is available in the repository. To view the complete git log run:

	git -C local/appcrue log --oneline --decorate --graph

Contributors (from git commits): Juan Pablo de Castro (and variants), Alberto Otero Mato,  and others.
