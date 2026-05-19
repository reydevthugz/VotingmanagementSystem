# AI Agent Instructions for Voting Management System

## Project Overview
- PHP MVC application for school election management.
- Custom lightweight framework with manual autoloading in `app/bootstrap.php`, custom router in `app/core/Router.php`, and route definitions in `routes/web.php`.
- No Composer dependencies are required to run the app.

## Key Files and Directories
- `app/controllers/` — request handlers and action methods.
- `app/models/` — database interaction and domain logic.
- `app/views/` — HTML templates and layout partials.
- `app/core/Database.php` — PDO connection using `config/database.php` and `.env` values.
- `routes/web.php` — route registration and middleware attachments.
- `config/` — runtime configuration values.
- `public/` — webserver document root.
- `database/schema.sql` — database schema used to initialize MySQL tables.

## Runtime and Environment
- Entry point: `public/index.php`.
- Bootstrap helpers: `base_path()`, `env()`, `flash()`, `csrf_token()`, `csrf_field()`, `csrf_validate()`.
- Environment is loaded from `.env` if present; `.env.example` shows expected variables.
- Database settings are in `config/database.php`.

## Development Guidance
- Follow existing MVC layering: controller actions should orchestrate input validation, model calls, and view rendering.
- Keep database access inside models or `BaseModel`; controllers should not directly create PDO connections.
- Preserve session and CSRF behavior when adding forms or auth flows.
- Use route definitions in `routes/web.php` for all public endpoints; maintain HTTP method consistency.
- Admin routes use `AuthMiddleware` plus `RoleMiddleware` with `admin`; student routes use `AuthMiddleware` plus `RoleMiddleware` with `student`.

## What to Watch For
- Authentication and authorization are session-based and enforced by middleware.
- The app relies on `$_SESSION` for flash messages, CSRF tokens, and login state.
- `app/bootstrap.php` configures `APP_DEBUG` from `.env`; do not assume errors are always displayed in production.
- The router maps exact path strings, not regex, so new routes must match path and method precisely.

## Helpful References
- `README.md` — project features and milestone summary.
- `database/schema.sql` — schema for core tables.
- `app/bootstrap.php` — application bootstrapping, helpers, and error handling.
- `routes/web.php` — current endpoint structure and middleware patterns.
