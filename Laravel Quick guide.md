got you — here’s a fast, practical Laravel crash course tuned to what you’re building and how you’ll run it on your VPS with Docker. I’ll map folders to concepts (with Django parallels), explain env/config, call out local vs prod gotchas, Dockerizing, and why Laravel still makes sense for a modular “add-on” platform.

# how the pieces fit

**Top-level**

* `app/` → **application code** (like Django apps).

  * `Models/` → Eloquent ORM models (≈ Django models.py).
  * `Http/Controllers/` → controllers (≈ Django views).
  * `Http/Middleware/` → request/response filters (≈ Django middleware).
  * `Policies/` → authZ rules per model (≈ Django permissions w/ object checks).
  * `Providers/` → service providers (register services, events, routes; ≈ Django AppConfig.ready()).
* `bootstrap/` → app bootstrapping; `cache/` holds compiled caches.
* `config/` → per-feature config (mail, queue, cache, database…). **Reads from `.env`**.
* `database/` → **migrations**, **seeders**, **factories** (≈ Django migrations/fixtures/factories).
* `public/` → web root (index.php, compiled assets). (≈ Django `collectstatic` target + WSGI entry, conceptually.)
* `resources/` → **views** (Blade templates), **lang**, and your **SPA assets** (Inertia React in `resources/js`).
* `routes/` → route files:

  * `web.php` (session/CSRF, your Inertia pages),
  * `api.php` (stateless JSON),
  * `channels.php` (broadcasts),
  * optionally `console.php` (artisan commands).
* `storage/` → runtime files, logs, generated files; `storage/app/public` for user uploads; symlinked via `php artisan storage:link`.
* `tests/` → PHP Unit/Pest tests.

**Key runtime concepts**

* **Routing → Controller → (Policy) → Model (Eloquent)**.
* **Requests** validated via **FormRequest** classes.
* **Dependency Injection** via method signatures/constructors (Laravel IoC).
* **Facades**: static-looking helpers bound to services (e.g., `Cache::put()`), but you can DI the underlying contracts for testability.

**Your stack nuance**

* You’re using **Inertia + React**: controllers return Inertia responses; front-end “pages” live in `resources/js/Pages/*`. No separate API layer required for those routes (but you also have `/api/*`).

# env & configuration (the truth about `.env`)

* **`.env`** is **not committed**; it feeds `config/*` through `env()` calls.
* **Production rule**: run `php artisan config:cache` so **env is snapshotted** into one file (`bootstrap/cache/config.php`). After this, **env changes won’t take effect** until you clear/rebuild cache.
* **Precedence**: `.env` → `config/*.php` → runtime overrides. Never call `env()` outside `config/*.php` (it won’t work after caching).
* Must-set vars: `APP_KEY` (crypto), `APP_ENV`, `APP_DEBUG=false` in prod, DB/Redis, queue, mail, filesystems (MinIO/S3), `SESSION_DRIVER`, `CACHE_DRIVER`, `QUEUE_CONNECTION`.

# local vs production (VPS) — what to watch out for

**Local**

* `php artisan serve` or run under your Docker stack.
* `php artisan migrate --seed`, `storage:link`.
* Hot reloading via Vite dev server.
* Debugging: `APP_DEBUG=true`, Laravel Telescope (dev only).

**Production (esp. Docker on VPS)**

* Web server → PHP-FPM (your `web` → `app:9000`).
* **Disable debug**: `APP_DEBUG=false`.
* **Caches** (speed & stability):

  * `php artisan config:cache`
  * `php artisan route:cache`
  * `php artisan view:cache`
  * enable PHP **opcache** (you already did in Dockerfile).
* **Queue workers**: don’t use `queue:listen`; use `queue:work` or **Horizon**. You have a dedicated `queue` service — good.
* **Scheduler**: your `scheduler` container running `schedule:run` every minute — perfect.
* **File uploads**: always use Storage (S3/MinIO). Run `storage:link`. Don’t write to container FS (ephemeral).
* **Permissions**: `storage/` and `bootstrap/cache` writable by `www-data`.
* **Proxies/HTTPS**: behind Caddy/Nginx/Cloudflare, set `TRUSTED_PROXIES=*` and ensure `APP_URL` is **https**.
* **Security headers**: set via Caddy/Nginx (CSP, HSTS, X-Frame-Options).
* **Rate limiting**: throttle public intake; add CAPTCHA (Turnstile).
* **Backups**: nightly `pg_dump` + offsite (MinIO/Linode Object Storage).
* **Logs**: centralize to files (`storage/logs`) or ship to Loki/ELK; keep PII out.
* **Migrations**: run `migrate --force` on deploys (CI step).

# artisan cheat-sheet (you’ll use these daily)

* `php artisan migrate` / `make:migration` — DB changes.
* `php artisan make:model -m` — model + migration.
* `php artisan make:controller --invokable` — slim controllers.
* `php artisan make:request` — validation.
* `php artisan make:policy` — authZ.
* `php artisan make:job` / `event` / `listener` — async/events.
* `php artisan tinker` — REPL.
* `php artisan test` — tests.
* `php artisan horizon` — queue dashboard (after install).

# Eloquent quick map (vs Django ORM)

* Model class per table; `$fillable` or guarded fields.
* Relationships: `$this->hasMany()`, `belongsTo()`, `belongsToMany()` (with pivots).
* **Scopes** (global & local) for soft multi-tenancy (add a global scope by `booted()` to always filter `organization_id`).
* **Migrations** define schema; **Seeders/Factories** generate test data.

# is Laravel good in Docker? (yes — and here’s why)

* PHP-FPM + web proxy is a **classic** container pattern: small, fast, immutable deploys.
* Horizontally scale `web` and `app` containers; Redis for cache/session/queues; S3-compatible storage; Postgres separate service — you already have the blueprint.
* Laravel also runs well in **serverless** (Bref/Lambda), but your VPS+Docker is simpler and perfectly valid.

# “monolith” vs modular add-ons

Laravel is *often* used as a monolith, but it’s excellent for **modular** design:

* Feature boundaries via **domains/modules** (directory structure + namespaces).
* **Service providers** let each module register routes, events, policies conditionally (great for “add-ons”).
* **Feature flags** at runtime to expose/hide modules per org.
* If/when needed, extract modules into **packages** (Composer) or separate services — same language, same conventions.

# why would a team like Formalize choose Laravel?

* **Developer velocity**: batteries-included (queues, jobs, events, cache, mail, storage, policies, validation).
* **Rich ecosystem**: first-party & community packages (spatie/*, Horizon, Scout, Cashier, etc.).
* **Clean DX**: expressive syntax, powerful IoC, testing story (Pest/PhpUnit), fast scaffolding.
* **Modularity without microservice overhead**: easy to build “products” as modules/add-ons behind RBAC + feature flags.
* **Mature + hireable**: deep talent pool, predictable maintenance, long-term LTS releases.

# common pitfalls to avoid

* Forgetting to **rebuild caches** in prod after changing `.env`/`config/*`.
* Writing files to `public/` or app container FS (use Storage S3/MinIO).
* Running queue workers in the web container (keep **separate**).
* Not setting `APP_KEY` (encryption features break) or leaving `APP_DEBUG=true`.
* Skipping **rate limits** and **CAPTCHA** on public endpoints.
* Missing **trusted proxies** config behind a reverse proxy/CDN.
* Letting migrations drift (always migrate on deploy; use transactions where possible).

# quick “first run” checklist (local → VPS)

**Local**

1. `cp .env.example .env && php artisan key:generate`
2. `php artisan migrate --seed && php artisan storage:link`
3. `npm ci && npm run dev` (or `build`)
4. `php artisan serve` (or Docker Compose up)

**VPS (Docker)**

1. Build images, `docker compose up -d`
2. `docker compose exec app composer install --no-dev --optimize-autoloader`
3. `docker compose exec app php artisan key:generate`
4. `docker compose exec app php artisan migrate --force && php artisan storage:link`
5. `docker compose exec app php artisan config:cache && route:cache && view:cache`
6. Ensure `queue` & `scheduler` services running; check logs; test upload to MinIO.

