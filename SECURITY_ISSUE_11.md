Security assessment and fix for [issue #11](https://github.com/juacas/moodle-local_appcrue/issues/11)

Both reported data flows contained XSS vulnerabilities. The changes address JavaScript injection in the autologin redirect page and stored HTML injection in the API-key settings description. The demonstrated impact is JavaScript execution in the Moodle origin with the affected browser session's privileges; this assessment does not establish server-side code execution.

1. **Autologin destination: reflected XSS and unrestricted destinations**

   `urltogo` was already read using `PARAM_URL`, but URL validation is not JavaScript string encoding. Quotes accepted in URL paths survived `moodle_url` conversion and reached a single-quoted JavaScript string in `autologin.php`. `html_writer::tag('script', ...)` does not escape its contents. A local, harmless marker confirmed that the resulting JavaScript could execute injected code.

   Exploitation requires autologin and `use_redirection_page` to be enabled and execution to reach the automatic redirect with a non-guest user session. The victim must navigate to a crafted link. A valid token can reach this path, but a valid token is not always required: when `allow_continue` is enabled, a rejected token with `fallback=continue` preserves an already authenticated user's session. An unauthenticated visitor continuing as guest does not receive this automatic redirect script. The error fallback stops before this sink; the HTTP redirect branch does not use this inline script. Site-specific CSP restrictions may further constrain execution.

   Explicit destinations also accepted external URLs despite being documented as local deep links.

   **Applied correction:** explicit `urltogo` input is validated as a Moodle-local URL and rejected if empty, malformed, external, scheme-relative, or using backslashes, control characters in the path, or dot segments that could change the destination after browser normalization. Absolute destinations must belong to this Moodle installation, including its subdirectory. Relative paths resolve against the Moodle root. Invalid destinations raise `invalidurl` rather than being silently transformed into another destination.

   The redirect script now serializes the destination with `json_encode` and `JSON_HEX_TAG`, `JSON_HEX_AMP`, `JSON_HEX_APOS`, and `JSON_HEX_QUOT`. It uses `out(false)` so query separators are preserved. This encoding also applies to destinations produced by administrator-defined patterns. Pattern configuration remains administrator-controlled; this change restricts explicit `urltogo` input, not configured external patterns.

2. **Invalid API key: stored XSS in settings**

   The `apikey` request parameter was filtered, but the `X-API-KEY` header was accepted verbatim. An invalid key was saved as `api_key_attempt` and inserted without HTML escaping into the API-key setting description. The description renderer preserves HTML, so an administrator opening the settings could execute attacker-supplied markup. A valid API key or user account is not required to record the invalid attempt.

   `appcrue.php` checks the caller's allowed network before authentication, limiting that entry point. The legacy `usercalendar.php` and `avatar.php` endpoints also invoke API-key authentication when enabled, without the same plugin network check. Their exposure depends on endpoint settings and deployment-level network controls. For the settings attack, the payload must remain stored until the administrator opens the page: a later valid API-key request clears the stored attempt, and another invalid request can replace it. The standard notification renderer cleans HTML, and the standard log report renders event descriptions as plain text; the confirmed sink was the settings description.

   **Applied correction:** parameter and header credentials now share the `PARAM_ALPHANUMEXT` character policy (ASCII letters, digits, hyphens and underscores). A credential that would change during cleaning is rejected with `INVALID_API_KEY`, before it reaches failed-attempt storage or authentication. Invalid input is never silently rewritten into a valid credential. Valid parameters retain precedence over headers.

   The settings page applies `s()` to the stored attempt before interpolation into the warning. This also protects against malicious values stored before the update. Historical values do not need to be deleted for this rendering fix to take effect. Network-access policies are unchanged; the input validation applies across the shared authentication paths, including the legacy endpoints.

Regression tests cover valid credentials from both sources, malformed credentials and normalization collisions, unchanged configuration after rejection, missing credentials, rendering a previously stored malicious key through the actual settings definition, accepted local destinations with query strings and fragments, external and malformed destinations, and JavaScript serialization of quotes and HTML script delimiters.

Compatibility considerations: header keys containing characters outside the configured API-key alphabet are now rejected. Explicit external `urltogo` destinations are also rejected. Integrations relying on either behavior need to use supported keys and local deep links.

Validation on Moodle 5.3 (Build: 20261005), PHP 8.3.32, MariaDB 11.7.2 and PHPUnit 11.5.56:

- New security regression tests: 7 tests, 70 assertions, all passed.
- Complete plugin suite: 86 tests, 317 assertions, no failures or errors; `--fail-on-warning` exited successfully. PHPUnit still reports the existing deprecated XML schema and one notice.
- Moodle CodeSniffer: no errors or warnings. PHP syntax checks and `git diff --check` passed.
- An additional JavaScript VM check preserved all three test destinations without executing the injected marker.

The full suite was run from `local/appcrue` with:

```sh
../../../vendor/bin/phpunit -c phpunit.xml.dist --testsuite local_appcrue_testsuite --fail-on-warning --display-phpunit-deprecations
```

Validation is limited to this checkout and its Moodle/PHP environment; it does not establish exploitation against a deployed site, test every browser/CSP combination, or replace the supported-version CI matrix. No full authenticated browser reproduction was performed.
