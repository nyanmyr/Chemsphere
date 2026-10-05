# Chemsphere

> **Know what's in your lab.**
> A web app for tracking chemicals and equipment, logging usage, and getting alerted before anything expires or runs out.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white)
![Pest](https://img.shields.io/badge/tested_with-Pest_3-F28D1A)
![License](https://img.shields.io/badge/license-MIT-green)

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Roles and Permissions](#roles-and-permissions)
- [Tech Stack and Tools](#tech-stack-and-tools)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [Running the App](#running-the-app)
- [How Alerts Work](#how-alerts-work)
- [Real-Time Updates](#real-time-updates)
- [API](#api)
- [Database](#database)
- [Testing and Code Style](#testing-and-code-style)
- [License](#license)

---

## Overview

Chemsphere is a Laravel 12 application for managing chemical stock and equipment in one place. Users can search the inventory, record how much of a chemical they used, check which instruments are available, and review alerts for chemicals that are expiring, expired, or running low. Administrators approve new accounts, manage the catalogue, and have access to full usage and audit trails.

Powered by WebSockets, every table updates instantly without needing a page refresh.

## Features

### Inventory and equipment

- **Chemical inventory** - with batch number, brand, volume per unit, initial and current quantity, arrival and expiration dates, storage location, and unit of measure (kg, g, mg, µg, L, mL, µL).
- **Safety metadata** - on every chemical: safety classes (flammable, corrosive, reactive, toxic) and GHS pictograms (GHS01 to GHS09).
- **Advanced search and filtering** - on the inventory: free-text search plus min/max ranges for ID, location, creator, volume, quantities, and dates, and filters for safety classes, GHS symbols, and unit.
- **Equipment tracking** - with model, serial ID, location, purchase and warranty dates, last/next maintenance dates, and a status of *available*, *unavailable*, *broken*, or *under maintenance*.
- **Locations** - to record where chemicals and equipment are kept.

### Usage tracking

- **Record chemical usage** - with the amount used and optional notes. The amount is validated against the remaining quantity, so stock can never go negative.
- **Check out equipment** - marks it as unavailable and logs who took it and why.
- **Usage logs** - capture who used what, how much, how much remains, and when.

### Alerts

- Automatic alerts for **expiring soon**, **expired**, **low stock**, and **out of stock** chemicals.
- Alerts are created the moment a chemical changes, and re-checked every morning by a scheduled command automatically.
- Unread alerts are de-duplicated, and each alert can be marked as read.

### Accounts and security

- **Registration restricted to `@uic.edu.ph` email addresses**, with strong password rules (minimum 8 characters, mixed case, numbers, symbols, and not found in known data breaches).
- **Admin approval workflow** - new accounts start as *pending* and cannot use the app until an admin approves them.
- **Google sign-in via Socialite** - a user's first password login requires linking their Google account (the emails must match). After that, they can sign in with Google directly.
- **Account suspension** - suspended users are redirected to a dedicated page and locked out of the app.
- **Immutable audit trail** - logins, logouts, registrations, and create/update/delete actions are recorded. Audit logs and usage logs are append-only; the models throw an error on any attempt to update or delete them.

### Developer experience

- Centralized user-facing messages in `lang/en/messages.php`, plus global handlers that turn missing records, expired logins, and forbidden actions into friendly flash messages.
- Reusable Blade components (layouts, inputs, selects, filter bar, chips, flash messages) and a small custom Tailwind theme.
- Health-check endpoint at `/up`.

## Roles and Permissions

| Capability                                          | User | Admin |
| --------------------------------------------------- | :--: | :---: |
| Access the app                                      |  ✓   |   ✓   |
| View inventory, equipment, locations, and alerts    |  ✓   |   ✓   |
| Record chemical usage / check out equipment         |  ✓   |   ✓   |
| Receive stock and expiry alerts                     |  ✓   |   ✓   |
| Add, edit, and delete chemicals, equipment, locations |  -   |   ✓   |
| Approve accounts and change user roles              |  -   |   ✓   |
| View usage logs and audit logs                      |  -   |   ✓   |

Admins cannot change their own role.

Pending and suspended users cannot access these features.

## Tech Stack and Tools

### Backend

| Tool | Purpose |
| --- | --- |
| [PHP 8.2+](https://www.php.net/) | Language runtime |
| [Laravel 12](https://laravel.com/) | Application framework (routing, Eloquent ORM, validation, scheduling, queues) |
| [Laravel Reverb](https://laravel.com/docs/reverb) | First-party WebSocket server for real-time broadcasting |
| [Laravel Socialite](https://laravel.com/docs/socialite) | Google OAuth sign-in |
| [Laravel Sanctum](https://laravel.com/docs/sanctum) | Token authentication for the JSON API |
| [Laravel Tinker](https://github.com/laravel/tinker) | Interactive REPL for the app |
| MySQL | Default database (configured in `.env.example`) |

### Frontend

| Tool | Purpose |
| --- | --- |
| [Blade](https://laravel.com/docs/blade) | Server-rendered templates and components |
| [Tailwind CSS 4](https://tailwindcss.com/) | Styling, with a custom "reagent" theme defined in `resources/css/app.css` |
| [Vite 7](https://vite.dev/) + [laravel-vite-plugin](https://github.com/laravel/vite-plugin) | Asset bundling and hot reload |
| [@tailwindcss/vite](https://tailwindcss.com/docs/installation/using-vite) | Tailwind integration for Vite |
| [Laravel Echo](https://github.com/laravel/echo) + [pusher-js](https://github.com/pusher/pusher-js) | WebSocket client (Reverb speaks the Pusher protocol) |
| [Axios](https://axios-http.com/) | HTTP client |

### Development and testing

| Tool | Purpose |
| --- | --- |
| [Pest 3](https://pestphp.com/) + pest-plugin-laravel | Test framework |
| [Laravel Pint](https://laravel.com/docs/pint) | Code style fixer |
| [Laravel Pail](https://laravel.com/docs/logging#tailing-log-messages-using-pail) | Real-time log tailing |
| [Laravel Sail](https://laravel.com/docs/sail) | Optional Docker development environment |
| [Collision](https://github.com/nunomaduro/collision) | Readable CLI error reporting |
| [Mockery](https://github.com/mockery/mockery) and [Faker](https://fakerphp.org/) | Mocking and fake data |
| [concurrently](https://github.com/open-cli-tools/concurrently) | Runs the server, queue, Reverb, and Vite together with `composer run dev` |

## Project Structure

```
chemsphere/
├── app/
│   ├── Console/Commands/
│   │   └── CheckChemicalAlerts.php      # `chemicals:check-alerts` Artisan command
│   ├── Enums/                           # AlertType, AuditAction, EquipmentStatus, GHSSymbol,
│   │                                    # ItemType, SafetyClass, Unit, UserRole
│   ├── Http/
│   │   ├── Controllers/                 # Alerts, AuditLogs, Auth, Chemicals, Equipment,
│   │   │                                # Locations, UsageLogs, Users
│   │   └── Middleware/
│   │       ├── EnforceSuspension.php    # alias: suspended
│   │       ├── PendingRedirect.php      # alias: pending
│   │       ├── RoleAuthorization.php    # alias: role:<name>
│   │       └── VerifyUserRole.php       # alias: verify
│   ├── Models/                          # Alert, AuditLog, Chemical, Equipment, Location,
│   │                                    # UsageLog, User
│   ├── Observers/
│   │   └── ChemicalsObserver.php        # Re-checks alerts when a chemical changes
│   ├── Providers/
│   │   └── AppServiceProvider.php       # Registers the Chemical observer
│   └── Services/
│       └── ChemicalAlertService.php     # Expiry and stock-level alert logic
├── bootstrap/
│   └── app.php                          # Routing, middleware aliases, exception rendering
├── config/                              # app, auth, broadcasting, cache, database, mail,
│                                        # queue, reverb, sanctum, services, session, ...
├── database/
│   ├── factories/
│   ├── migrations/                      # users, locations, audit_logs, chemicals, equipment,
│   │                                    # alerts, usage_logs, sessions, jobs, cache, tokens
│   └── seeders/
├── lang/en/
│   └── messages.php                     # Centralized success and error messages
├── public/                              # Web root
├── resources/
│   ├── css/app.css                      # Tailwind import, theme tokens, component classes
│   ├── js/
│   │   ├── app.js                       # Live table refresh via Echo
│   │   ├── bootstrap.js                 # Axios + Echo (Reverb) setup
│   │   └── echo.js
│   └── views/
│       ├── components/                  # app-layout, guest-layout, input, select, textarea,
│       │                                # filter-bar, chips, flash, meta, form-actions
│       ├── partials/                    # nav and shared form fields
│       └── *.blade.php                  # inventory, equipment, locations, alerts, users,
│                                        # usage_logs, audit_logs, login, register, pending, ...
├── routes/
│   ├── web.php                          # Pages and form actions
│   ├── api.php                          # Sanctum-protected JSON endpoints
│   ├── channels.php                     # Private broadcast channels
│   └── console.php                      # Scheduled tasks
├── tests/                               # Pest feature and unit tests
├── composer.json
├── package.json
└── vite.config.js
```

## Getting Started

### Prerequisites

- PHP 8.2 or newer (with the extensions Laravel requires)
- [Composer](https://getcomposer.org/)
- Node.js and npm
- MySQL (or another database supported by Laravel)
- A Google Cloud OAuth client (for Google sign-in)

### Installation

```bash
# 1. Get the code
git clone https://github.com/nyanmyr/Chemsphere chemsphere
cd chemsphere

# 2. Install dependencies
composer install
npm install

# 3. Create your environment file and app key
cp .env.example .env
php artisan key:generate
```

Then edit `.env` (see [Configuration](#configuration)), create the database, and run the migrations:

```bash
php artisan migrate
```

> **Shortcut:** once `.env` is configured, `composer run setup` installs dependencies, generates the key, runs migrations, and builds the assets in one go.

### Create your first admin

New accounts start as *pending*, so the first admin has to be promoted directly in the database. Register an account through the app, then run:

```bash
php artisan tinker
```

```php
App\Models\User::where('email', 'you@uic.edu.ph')->update(['user_role' => 'admin']);
```

From there, admins can approve everyone else on the **Users** page.

## Configuration

Set these in `.env`:

```dotenv
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chemsphere
DB_USERNAME=root
DB_PASSWORD=

# Google sign-in (Google Cloud Console > APIs & Services > Credentials)
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Real-time updates (Laravel Reverb)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=chemsphere
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Add `http://localhost:8000/auth/google/callback` as an authorized redirect URI on your Google OAuth client. Sessions, cache, and the queue all use the database driver by default.

## Running the App

### Development

```bash
composer run dev
```

This starts four processes side by side: the PHP dev server, the queue listener, the Reverb WebSocket server, and Vite. The app is served at `http://localhost:8000`.

The scheduler is not part of that command. To exercise the daily alert check locally, run it in another terminal:

```bash
php artisan schedule:work
```

or trigger the check once by hand:

```bash
php artisan chemicals:check-alerts
```

### Production

```bash
npm run build
```

Then keep these running under a process manager such as Supervisor:

- `php artisan reverb:start`
- `php artisan queue:work`

and add the standard Laravel scheduler entry to cron:

```cron
* * * * * cd /path/to/chemsphere && php artisan schedule:run >> /dev/null 2>&1
```

## How Alerts Work

`ChemicalAlertService` evaluates each chemical against these rules:

| Alert | Condition |
| --- | --- |
| Expired | Expiration date has passed |
| Expiring soon | Expires within 30 days |
| Out of stock | Current quantity is 0 or less |
| Low stock | Current quantity is at or below 20% of the initial quantity |

Checks run in two places:

1. **On change:** `ChemicalsObserver` checks a chemical when it is created, and again whenever its `current_quantity` or `expiration_date` changes.
2. **On a schedule:** `chemicals:check-alerts` runs daily at 06:00 and checks every chemical in batches of 100.

Alerts go to all users with the *user* or *admin* role. If a recipient already has an unread alert of the same type for the same chemical, its message is refreshed (for example with the new quantity) instead of creating a duplicate.

## Real-Time Updates

Models broadcast their create, update, and delete events on private channels (`inventory`, `equipment`, `locations`, `alerts`, `users`, `usage_logs`, `audit_logs`). The frontend listens through Laravel Echo and, when an event arrives, re-fetches the current page and swaps in the fresh table, so active filters and pagination are preserved.

## API

JSON endpoints live in `routes/api.php` and require a Sanctum token (`auth:sanctum`).

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/user` | The authenticated user |
| `GET` | `/api/alerts` | The authenticated user's alerts |
| `PATCH` | `/api/alerts/{alert}/read` | Mark an alert as read |

## Database

| Table | Contents |
| --- | --- |
| `users` | Email, password, Google ID, and role (`pending`, `suspended`, `user`, `admin`) |
| `locations` | Named storage locations with a description |
| `chemicals` | Chemical stock, quantities, dates, safety classes, GHS symbols, unit |
| `equipment` | Equipment records, status, and maintenance/warranty dates |
| `alerts` | Per-user chemical alerts with type and read state |
| `usage_logs` | Append-only record of chemical and equipment use |
| `audit_logs` | Append-only record of logins and data changes |
| `sessions`, `jobs`, `cache`, `personal_access_tokens` | Framework support tables |

Chemicals, equipment, and locations are linked by `location_id`, and every record stores the `created_by` user.

## Testing and Code Style

```bash
composer test          # Clears config, then runs the Pest suite
./vendor/bin/pint      # Fixes code style issues
php artisan pail       # Tails the application log live
```

Tests run against an in-memory SQLite database with the array cache and session drivers, synchronous queues, and broadcasting disabled (see `phpunit.xml`).

## License

Released under the [MIT license](https://opensource.org/licenses/MIT), as declared in `composer.json`.
