# Current:
WIP: improving template renderer

add to scenarios function setScenario that will handle things like:
- set the record as not new
- set record as a new
- check changed/dirty fields to allow or disallow next actions
- some other things.
=====================================
- check TODO make config more usable
- Check Router for layout/file paths...

### update:
-update only changed fields
-allow timestamp auto-insert in DB

## General
-findAll($where)

## Validation:
- add collapse button to toggle all invalid fields [see](https://getbootstrap.com/docs/5.0/components/collapse/)

## Router/Route:
- add route regexp like: ('/articles/{id:\d+}[/{title}]')
- e.g.:
  // {id} must be a number (\d+)
  $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
  // The /{title} suffix is optional

## create UseMacros trait to allow use of marcos
- (Manager class + Views)


Fix "Confirm Form Resubmission" by using PRG (Post Redirect Get) php

#### ONE OF WIP:
improving project structure (modules/controllers/models/DB/query builder)