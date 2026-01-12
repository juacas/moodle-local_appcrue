
AppCRUE — Release notes
=======================

v2.0.1 — 2026-01-12
- Fix API key extraction from headers to handle different casing and server variables.
- 2025-10-30 — Add API key support in header `X-API-KEY` and adjust HTTP status handling.
- 2025-10-24 — Auto configuration mode added.

v2.0.0 — 2025-10-17
- Official AppCRUE integration and platform-ready improvements.
- Enforced IP filtering for API endpoints and improved redirect handling to avoid MFA issues.
- Added file and assignment services, assignment dates mapping and related settings.
- PHPCS fixes, documentation and GitHub Actions CI added.

v1.0.0 — 2025-08-19
- Stable 1.0.0 release: API key support and key-rotation endpoint.
- Define `AJAX_SCRIPT` constant and other reliability fixes.

v0.1.4 — 2022-10-28
- Configurable default field name for webservices and several small improvements.

v0.1.3 — 2022-05-31
- Log token errors; fixes to `notifygrades` message format.

v0.0.8 — 2021-10-08
- Early stable features: user calendar support, messaging web services, grade notifications.

Other notes
- Full commit history is available in the repository. To view the complete git log run:

	git -C local/appcrue log --oneline --decorate --graph

Contributors (from git commits): Juan Pablo de Castro (and variants), Alberto Otero Mato, AlbertoOM71, and others.

