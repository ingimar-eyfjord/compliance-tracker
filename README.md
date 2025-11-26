# Compliance Tracker

Modern Laravel + Inertia (React) app for managing compliance cases, breach logging, reports, and org settings. This repo is a standalone extraction of the app that previously lived inside a multi-app portfolio; the `Docs/` folder contains the original planning and requirements notes.

## Tech stack
- Laravel (PHP 8.2) with Fortify auth, queues, and jobs
- Inertia + React + Vite + TailwindCSS
- Postgres (primary datastore)
- Nginx + PHP-FPM dockerized runtime

## Quick start (Docker)
Requirements: Docker + Docker Compose v2; free ports 80 (nginx), 5173 (Vite dev), 5433 (Postgres).

```bash
# from repo root
RTE=dev docker compose up -d --build

# first-time DB setup
docker compose exec compliance-app php artisan migrate --seed
```

URLs:
- App via nginx: http://compliance.localhost
- Vite HMR (dev): http://localhost:5173
- Postgres: localhost:5433 (user/pass `compliance` / `compliance_password`)

Env switching: `RTE=prod` will load `env/compliance/app_prod.env` and `env/compliance/db_prod.env`. Adjust keys/URLs/creds there as needed.

## Local (non-Docker) basics
If you prefer running locally: `cp .env.example .env`, set a DB, `composer install`, `npm install`, `php artisan key:generate`, `php artisan migrate --seed`, then `npm run dev` and `php artisan serve`.

## Tests
```bash
docker compose exec compliance-app php artisan test
```

## Project map & docs
- Domain overview and requirements live in `Docs/` (Context, MVP plan, Epics, Models, Routes/CRUD, etc.).
- Frontend pages: `resources/js/pages/*` (Inertia React)
- Backend HTTP/logic: `app/Http/*`, `app/Models/*`, `database/migrations/*`
- Docker: `docker-compose.yml`, `docker/` (PHP-FPM + nginx)

## Production notes
- Set `APP_ENV=production`, `APP_DEBUG=false`, and a strong `APP_KEY` in your prod env file.
- Build assets once on container start (handled in entrypoint when not in dev).
- Run migrations on deploy: `docker compose exec compliance-app php artisan migrate --force`.
- Cache config/routes/views for speed: `php artisan config:cache route:cache view:cache`.
