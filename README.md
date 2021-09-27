# AppCRUE services #

Implements services for extracting different types of information for an user identified by a token.
The token need to be validated and authenticated by an external identity provider (IdP).

It was developed to enable Moodle to publish information for the AppCrue application.
# Functionality #

This local plugin provides the following services following the AppCRUE API:
- usercalendar: reports calendar events for a user. It takes the params fromDate, toDate, token.

```
{
  "calendar": [
    { // day item
      "date": "2020-04-21"
      "events": [
        { // event item
          "id": 1033992237,
          "title": "Tutoring",
          "description": "Tutoring in Mathematics",
          "url": "http://universidad.es/tuperfil/tutorias",
          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "TUTORIA",
          “startsAt”: “1575990139”,
             “endsAt”: “1575990139”
        },
        { "id": 1033992247,
          "title": "Clase",
          "description": "Clase asignatura Inglés",
          "url": "http://universidad.es/tuperfil/tutorias",

          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "HORARIO",
          “startsAt”: “1575990139”,
          “endsAt”: “1575990139”
        }
      ]
    },
    { // day item
      "date": "2020-04-22"
      "events": [
        { "id": 1033945237,
          "title": "Tutoria",
          "description": "Tutoria asignatura Matemáticas”,
          "url": "http://universidad.es/tuperfil/tutorias",
          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "TUTORIA,
          “startsAt”: “157593453”,
          “endsAt”: “157593453”,
        },
        { // event item
          "id": 1033992449,
          "title": "Clase",
          "description": "Clase asignatura Inglés",
          "url": "http://universidad.es/tuperfil/tutorias",
          "nameAuthor": "Autor",
          "imgDetail": "http://test.host/uploads/event/logo/1033992237/example.png",
          "type": "HORARIO,
          “startsAt”: “157593453”,
          “endsAt”: “157593453”,
        }
      ]
    }
  ]
}

```
- autologin: logs-in a user with an external token and redirects him to a deep link. Also can build redirection URLs from a library of configured url patterns and some parameters. It takes the params token, fallback, urltogo, course, group, pattern, param1, param2, param3.
- sitemap: generates a JSON representation of the categories and courses. It takes params token, category, includecourses, hiddencats, urlsonlyonends.
```
{
    "navegable": [
        {
            "name": "Miscellaneous",
            "description": "A bit of everything",
            "id": "1",
            "navegable": [
                {
                    "name": "Ingenier\u00eda",
                    "description": "&lt;p dir=&quot;ltr&quot; style=&quot;text-align: left;&quot;&gt;Categoria xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx&lt;\/p&gt;",
                    "id": "3",
                    "url": "https:\/\/XXXXX\/moodle310\/course\/index.php?categoryid=3"
                }
            ]
        },
        {
            "name": "empty",
            "description": "&lt;p&gt;&amp;nbsp;vccc&lt;\/p&gt;",
            "id": "2",
            "url": "https:\/\/XXXXX\/moodle310\/course\/index.php?categoryid=2"
        }
    ]
}
```
- avatar: identifies a user witn an external token and returns his avatar picture in raw or base64 format.

TODO Provide more detailed description here.

## What is AppCRUE? ##

AppCRUE (https://tic.crue.org/app-crue/) is a mobile App develop by the CRUE (Conference of Rectors of Spanish Universities) and Santander Bank. It is used by 44 spanish universities and more than 150 000 students.

## License ##

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
