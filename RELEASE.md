## Release Notes for Appcrue Plugin
### Version 2.1.0 (2026-10-08)
#### Security
- Fix the XSS vulnerabilities reported in [issue #11](https://github.com/juacas/moodle-local_appcrue/issues/11). Escape stored invalid API keys in the settings page and reject malformed API keys from both request parameters and headers.
- Validate explicit autologin `urltogo` destinations as local Moodle URLs. Reject external, malformed, and path-traversal destinations, and safely encode redirect URLs embedded in JavaScript.
- Add regression tests for API-key validation and rendering, local URL checks, and JavaScript redirect encoding.

#### Compatibility
- API keys may contain ASCII letters, digits, hyphens, and underscores. Explicit `urltogo` links must resolve within the Moodle site.

Other notes
- Full commit history is available in the repository. To view the complete git log run:

	git -C local/appcrue log --oneline --decorate --graph

Contributors (from git commits): Juan Pablo de Castro (and variants), Alberto Otero Mato,  and others.
