#!/usr/bin/env bash
set -euo pipefail

APP_ENVIRONMENT="${APP_ENV:-production}"
APP_DEBUG_MODE="${APP_DEBUG:-false}"
RUNTIME_TIER="${RTE:-}"
VITE_PORT="${VITE_PORT:-5173}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
NODE_STAMP_FILE="node_modules/.container-installed"
NODE_STAMP_VALUE="$(uname -m)-$(node -v)"
OPCACHE_RUNTIME_FILE="/usr/local/etc/php/conf.d/zz-runtime-opcache.ini"

if [[ -z "$RUNTIME_TIER" ]]; then
    RUNTIME_TIER="$APP_ENVIRONMENT"
fi

IS_DEV=false
if [[ "$RUNTIME_TIER" == "dev" || "$APP_ENVIRONMENT" == "local" || "$APP_DEBUG_MODE" == "true" ]]; then
    IS_DEV=true
fi

configure_opcache() {
    if [[ "$IS_DEV" == true ]]; then
        cat > "$OPCACHE_RUNTIME_FILE" <<'INI'
opcache.validate_timestamps=1
opcache.revalidate_freq=0
opcache.enable_cli=1
INI
    else
        cat > "$OPCACHE_RUNTIME_FILE" <<'INI'
opcache.validate_timestamps=0
INI
    fi
}

pids=()

cleanup() {
    for pid in "${pids[@]}"; do
        if [[ -n "$pid" ]] && kill -0 "$pid" 2>/dev/null; then
            kill "$pid" 2>/dev/null || true
        fi
    done
}

trap cleanup EXIT TERM INT

ensure_node_modules() {
    mkdir -p node_modules

    if [[ ! -f "$NODE_STAMP_FILE" ]] || [[ "$(cat "$NODE_STAMP_FILE")" != "$NODE_STAMP_VALUE" ]]; then
        echo "[entrypoint] Installing npm dependencies for ${NODE_STAMP_VALUE}..."
        npm install --legacy-peer-deps >/tmp/npm-install.log 2>&1 || {
            cat /tmp/npm-install.log
            echo "[entrypoint] npm install failed."
            exit 1
        }
        echo "$NODE_STAMP_VALUE" > "$NODE_STAMP_FILE"
    fi
}

ensure_assets() {
    if [[ "$APP_ENVIRONMENT" != "local" && "$APP_DEBUG_MODE" != "true" ]]; then
        if [[ ! -d public/build ]]; then
            echo "[entrypoint] Building production assets..."
            ensure_node_modules
            npm run build >/tmp/npm-build.log 2>&1 || {
                cat /tmp/npm-build.log
                echo "[entrypoint] npm run build failed."
                exit 1
            }
        fi
    fi
}

run_dev_processes() {
    echo "[entrypoint] Starting development helpers..."

    ensure_node_modules

    if [[ "$QUEUE_CONNECTION" != "sync" ]]; then
        echo "[entrypoint] Starting queue listener..."
        php artisan queue:listen --tries=1 --timeout=90 --quiet &
        pids+=("$!")
    fi

    echo "[entrypoint] Starting Vite dev server on port ${VITE_PORT}..."
    npm run dev -- --host 0.0.0.0 --port "${VITE_PORT}" &
    pids+=("$!")
}

if [[ "$APP_ENVIRONMENT" == "local" || "$APP_DEBUG_MODE" == "true" ]]; then
    run_dev_processes
else
    ensure_assets
fi

configure_opcache

php-fpm &
pids+=("$!")

wait -n "${pids[@]}"
