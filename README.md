# Laravel Access Control Rules: examples

A small Laravel 13 application that runs every example of the two tutorials of
[wnikk/laravel-access-rules](https://github.com/wnikk/laravel-access-rules), with the admin panel
[wnikk/laravel-access-ui](https://github.com/wnikk/laravel-access-ui) mounted on it.

- **Part one, RBAC**: rules, roles, options, the suffix `.self`, `authorizeResource()`. Examples 1 to 7.
  Published on [dev.to](https://dev.to/wnikk/how-use-access-control-rules-and-grud-in-laravel-10-tutorial-step-by-step-307a)
  and, in Russian, on [habr.com](https://habr.com/ru/articles/729414/).
- **Part two, ABAC**: permissions with conditions on columns, the user, relations, aggregates, trees, time and
  polymorphic relations, and lists filtered by the same conditions. Examples 8 to 15 here cover the eighteen examples of
  [the second tutorial](https://github.com/wnikk/laravel-access-rules/blob/main/docs/tutorial-abac-step-by-step.md).

## Run it

PHP 8.4 and SQLite are enough.

```bash
git clone https://github.com/wnikk/-laravel-access-example.git
cd -laravel-access-example
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Open `http://127.0.0.1:8000`. The front page lists every route and three accounts to sign in as, no password:

| | who | what |
|---|---|---|
| `/sign-in/1` | Ann | the root role of part one, a manager of the shop, team North; administers the panel |
| `/sign-in/2` | Bob | a manager of the shop, team South |
| `/sign-in/3` | a user that holds nothing | every 403 of the examples |

## What is where

| route | example |
|---|---|
| `/example1` … `/example7` | part one: the check in the route and in the action, options of one rule, `authorizeResource()` with global and per-controller rules, `.self`, a policy of 2.x as a condition of 3.x |
| `/example8` | one permission with a condition: the list, one record, the record against the user, an option with a condition of the rule |
| `/example9` | the scenarios of part two on the same six orders: a column and a count, the user, relations, priority, aggregates, time, arithmetic, text, tenants, trees, pivot columns, the environment, enums, the builder |
| `/example10` | a menu: three ways to ask, with a record, with a class, with nothing |
| `/example11` | guests |
| `/example12` | "why can't I see it": a 403 that names what was refused, `?access_debug=1` adds the cause |
| `/example13` | `explain`, `lint` and the audit log as data |
| `/example14` | XACML: download, check, import |
| `/example15` | polymorphic relations, example 18 of part two |
| `/user`, `/user/{id}` | the profile with the card of the panel: what the account inherits from |
| `/users` | every account, for an administrator |
| `/access-control` | the panel: rules, owners, permissions with conditions, inheritance, why?, health, XACML |

The seeded state is the one the tutorials describe; `/example9/reset` and `/example15/reset` put it back after the scenarios.

## Tests

```bash
php artisan test
```

Every example answers as its tutorial says, and every screen of the panel answers for the administrator.

## Like What You See? :thumbsup:

[![Stars](https://img.shields.io/github/stars/wnikk/-laravel-access-example)](https://github.com/wnikk/-laravel-access-example)

### Give Stars :star:

Make the maintainer happy by hitting the star icon for this repository!

### Ready to be a sponsor? :coffee:

You can now [buy me a latte](https://www.buymeacoffee.com/wnik) =)
