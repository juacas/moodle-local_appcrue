# AppCRUE services

Implements services to extract different types of information for a user identified by a token or an API key.

The token must be validated and authenticated by a configurable external identity provider (IdP).

This plugin was developed to enable Moodle to publish information for the AppCRUE application.

# Functionality

This plugin implements a set of simple REST endpoints that provide a backend with calendar events, forum posts, files, grades, and announcements accessible by a particular user.  
It authenticates the caller, identifies the queried user, impersonates that user and queries Moodle internal APIs.  
It implements endpoints compatible with the classical AppCRUE API (calendar events, autologin, sitemap generation, avatar retrieval) and web services to send notifications from external systems.

## Classical AppCRUE API

This local plugin provides the following services following the AppCRUE API:

- usercalendar: reports calendar events for a user. It accepts the parameters fromDate, toDate, token.

Example response:
```json
{
  "calendar": [
    {
      "date": "2020-04-21",
      "events": [
        {
          "id": 1033992237,
          "title": "Tutoring",
          "description": "Tutoring in Mathematics",
          "url": "http://universidad.es/tuperfil/tutorias",
          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "TUTORIA",
          "startsAt": "1575990139",
          "endsAt": "1575990139"
        },
        {
          "id": 1033992247,
          "title": "Clase",
          "description": "Clase asignatura Inglés",
          "url": "http://universidad.es/tuperfil/tutorias",
          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "HORARIO",
          "startsAt": "1575990139",
          "endsAt": "1575990139"
        }
      ]
    },
    {
      "date": "2020-04-22",
      "events": [
        {
          "id": 1033945237,
          "title": "Tutoria",
          "description": "Tutoria asignatura Matemáticas",
          "url": "http://universidad.es/tuperfil/tutorias",
          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "TUTORIA",
          "startsAt": "157593453",
          "endsAt": "157593453"
        },
        {
          "id": 1033992449,
          "title": "Clase",
          "description": "Clase asignatura Inglés",
          "url": "http://universidad.es/tuperfil/tutorias",
          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "HORARIO",
          "startsAt": "157593453",
          "endsAt": "157593453"
        }
      ]
    }
  ]
}
```

## Utility endpoints

These endpoints simplify integration with the AppCRUE mobile app and make navigation from the mobile device to Moodle easier. Most endpoints can also be reused for URL redirection, course dereferencing, and other purposes.

- autologin: logs in a user with an external token and redirects them to a deep link. It can also build redirect URLs from a library of configured URL patterns and parameters. Parameters: token, fallback, urltogo, course, group, pattern, param1, param2, param3.
  - token: auth token.
  - pattern: if specified, parameters are used to generate the URL by replacing placeholders in a registered pattern.
  - fallback: response when token is absent or invalid. Values: "ignore", "error", "logout".
  - urltogo: deep link relative to the Moodle site to visit after token validation.
  - course, group, param1, param2, param3: general-purpose parameters for pattern-based URL generation or course lookup (see local_appcrue/pattern_lib and local_appcrue/course_pattern settings).

- sitemap: generates a JSON representation of categories and courses. Parameters: token, category, includecourses, hiddencats, urlsonlyonends.
  - token: auth token.
  - includecourses: whether to include courses (do not stop at category level).
  - hiddencats: omit a list of categories from the sitemap using PHP array form parameters. Example: hiddencats[0]=2&hiddencats[1]=34.
  - urlsonlyonends: if true, only the last element of each branch has a URL; otherwise every node has a URL.

Example sitemap:
```json
{
  "navegable": [
    {
      "name": "Miscellaneous",
      "description": "A bit of everything",
      "id": "1",
      "navegable": [
        {
          "name": "Ingenier\u00eda",
          "description": "<p dir=\"ltr\" style=\"text-align: left;\">Categoria xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</p>",
          "id": "3",
          "url": "https://XXXXX/moodle310/course/index.php?categoryid=3"
        }
      ]
    },
    {
      "name": "empty",
      "description": "<p>&nbsp;vccc</p>",
      "id": "2",
      "url": "https://XXXXX/moodle310/course/index.php?categoryid=2"
    }
  ]
}
```

- avatar: identifies a user with an external token and returns their avatar image in raw or base64 format.
- notifygrades web service: receives a webhook from an external academic management system, composes a localized message with the grade and other details, and sends it via the messaging API (may be routed to AppCRUE as well).
- send_instant_message web service: sends a private message to a user via the messaging API.

## New LMS integration API

This plugin implements a set of REST endpoints to provide information for a specific user. They accept an API key to authorize the AppCRUE backend, a user identifier (for example, email), then match the identifier against user fields, impersonate the user, and query Moodle internal APIs.

- local/appcrue/appcrue.php/calendar: provides calendar events for a user. Parameters: apikey, studentemail, timestart, timeend.
- local/appcrue/appcrue.php/forums: provides forum posts for a user. Parameters: apikey, studentemail, optional timestart.
- local/appcrue/appcrue.php/files: provides files downloadable by a user. Parameters: apikey, studentemail.
- local/appcrue/appcrue.php/grades: provides grades for a user. Parameters: apikey, studentemail.
- local/appcrue/appcrue.php/announcements: provides announcements for a user (news forums). Parameters: apikey, user.

## Web services

This plugin provides web services to perform actions from external systems:

- notifygrades: receives a webhook from an external academic management system, composes a localized message with the grade and other details, and sends it via the messaging API.
- send_instant_message: sends a private message to a user via the messaging API.

Activate the web services following the instructions at:
https://[SERVER]/admin/settings.php?section=webservicesoverview

## What is AppCRUE?

AppCRUE (https://tic.crue.org/app-crue/) is a mobile app developed by CRUE (Conference of Rectors of Spanish Universities) and Santander Bank. It is used by 44 Spanish universities and more than 150,000 students.

## License

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
