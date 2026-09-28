## Release Notes for Appcrue Plugin
### Version 2.0.10 (2026-09-28)
#### Fixes and Improvements
- Apply the filters enabled for the requested user context to course names, activity titles, forum names and discussion titles, announcement fields, calendar event text, and grade item names returned by the JSON endpoints.
- Set the current language when impersonating a requested user so multilang text is resolved correctly.
- Preserve the correct text format and filtering context when returning forum messages and grade feedback.

### Version 2.0.9 (2026-06-18)
#### Changes and Improvements
- Change order of network restrictions check.

Other notes
- Full commit history is available in the repository. To view the complete git log run:

	git -C local/appcrue log --oneline --decorate --graph

Contributors (from git commits): Juan Pablo de Castro (and variants), Alberto Otero Mato, AlbertoOM71, and others.
