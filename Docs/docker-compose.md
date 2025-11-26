# Compliance app with Docker

Minimal steps to run the stack that was previously in the portfolio repo.

## Prereqs
- Docker + Docker Compose v2
- Ports free: 80 (nginx), 5173 (Vite dev), 5433 (Postgres)

## Quick start (dev)
1) From repo root run:
   - `RTE=dev docker compose up -d --build`
2) Browse:
   - App via nginx: http://compliance.localhost (uses container PHP-FPM)
   - Vite HMR: http://localhost:5173
   - Postgres: localhost:5433 (user/pass `compliance` / `compliance_password`)
3) First-time DB setup:
   - `docker compose exec compliance-app php artisan migrate --seed`
4) Stop:
   - `docker compose down` (add `-v` to wipe DB volume)

## Env files
- Compose pulls from `env/compliance/app_{dev,prod}.env` and `env/compliance/db_{dev,prod}.env`.
- Adjust URLs/keys as needed; `RTE=prod` will switch to the prod variants.

## Notes
- Vite dev server is started automatically in dev; for prod (`APP_ENV=production`), assets are built once on container start.
- Nginx routes PHP to `compliance-app:9000`; the PHP container mounts the repo and persists `node_modules` in a named volume.
