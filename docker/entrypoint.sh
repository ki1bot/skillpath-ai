#!/usr/bin/env bash

set -euo pipefail

cd /var/www/html

retry_command() {
    local max_attempts="$1"
    local delay_seconds="$2"
    shift 2

    local attempt=1

    until "$@"; do
        if [ "$attempt" -ge "$max_attempts" ]; then
            echo "Command failed after ${attempt} attempts: $*" >&2
            return 1
        fi

        echo "Command failed on attempt ${attempt}/${max_attempts}. Retrying in ${delay_seconds} seconds..." >&2

        attempt=$((attempt + 1))
        sleep "$delay_seconds"
    done
}

wait_for_database() {
    local host="${DB_HOST:-}"
    local port="${DB_PORT:-5432}"
    local max_attempts="${DB_STARTUP_MAX_ATTEMPTS:-30}"
    local delay_seconds="${DB_STARTUP_RETRY_DELAY:-2}"
    local attempt=1

    if [ -z "$host" ]; then
        return 0
    fi

    echo "Waiting for database at ${host}:${port}..."

    until php -r 'exit(@fsockopen($argv[1], (int) $argv[2], $errno, $errstr, 2) ? 0 : 1);' "$host" "$port"; do
        if [ "$attempt" -ge "$max_attempts" ]; then
            echo "Database ${host}:${port} is still unavailable after ${attempt} attempts." >&2
            return 1
        fi

        echo "Database is not reachable yet. Attempt ${attempt}/${max_attempts}. Retrying in ${delay_seconds} seconds..."

        attempt=$((attempt + 1))
        sleep "$delay_seconds"
    done

    echo "Database ${host}:${port} is reachable."
}

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ "${DOCKER_MODE:-production}" = "development" ]; then
    SOURCE_UID="$(stat -c '%u' /var/www/html)"
    SOURCE_GID="$(stat -c '%g' /var/www/html)"
    RUN_AS="${SOURCE_UID}:${SOURCE_GID}"

    mkdir -p vendor node_modules

    rm -rf node_modules/.vite-temp node_modules/.vite

    chown "$SOURCE_UID:$SOURCE_GID" vendor node_modules 2>/dev/null || true
    chown -R "$SOURCE_UID:$SOURCE_GID" storage bootstrap/cache 2>/dev/null || true

    if [ ! -f .env ]; then
        cp .env.example .env
        chown "$SOURCE_UID:$SOURCE_GID" .env 2>/dev/null || true
    fi

    if ! grep -q '^APP_KEY=' .env; then
        printf '\nAPP_KEY=\n' >> .env
        chown "$SOURCE_UID:$SOURCE_GID" .env 2>/dev/null || true
    fi

    composer_lock_hash="$(sha256sum composer.lock | awk '{print $1}')"
    composer_marker="vendor/.docker-composer-lock"

    if [ ! -f vendor/autoload.php ] \
        || [ ! -f "$composer_marker" ] \
        || [ "$(cat "$composer_marker" 2>/dev/null || true)" != "$composer_lock_hash" ]; then

        gosu "$RUN_AS" composer install \
            --no-interaction \
            --prefer-dist \
            --no-progress

        printf '%s' "$composer_lock_hash" \
            | gosu "$RUN_AS" tee "$composer_marker" >/dev/null
    fi

    npm_lock_hash="$(sha256sum package-lock.json | awk '{print $1}')"
    npm_marker="node_modules/.docker-package-lock"

    if [ ! -d node_modules/.bin ] \
        || [ ! -f "$npm_marker" ] \
        || [ "$(cat "$npm_marker" 2>/dev/null || true)" != "$npm_lock_hash" ]; then

        gosu "$RUN_AS" npm ci \
            --no-audit \
            --no-fund

        printf '%s' "$npm_lock_hash" \
            | gosu "$RUN_AS" tee "$npm_marker" >/dev/null
    fi

    APP_KEY_VALUE="$(
        sed -n 's/^APP_KEY=//p' .env \
            | head -n 1 \
            | tr -d '\r' \
            | sed \
                -e 's/^[[:space:]]*//' \
                -e 's/[[:space:]]*$//' \
                -e 's/^"//' \
                -e 's/"$//'
    )"

    case "$APP_KEY_VALUE" in
        ""|"null"|"NULL"|"Null"|"''")
            gosu "$RUN_AS" php artisan key:generate --force
            ;;
    esac

    wait_for_database

    gosu "$RUN_AS" php artisan config:clear

    retry_command \
        "${DB_MIGRATION_MAX_ATTEMPTS:-10}" \
        "${DB_MIGRATION_RETRY_DELAY:-3}" \
        gosu "$RUN_AS" php artisan migrate --force

    if [ "${RUN_SEEDER:-false}" = "true" ]; then
        gosu "$RUN_AS" php artisan db:seed --force
    elif [ "${RUN_ASSESSMENT_SEEDER:-false}" = "true" ]; then
        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\CareerSeeder' \
            --force

        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\AcademicAssessmentSkillSeeder' \
            --force

        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\CareerSkillSeeder' \
            --force

        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\AcademicAssessmentSeeder' \
            --force

        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\AcademicAssessmentQuestionPoolSeeder' \
            --force

        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\AcademicAssessmentCleanupSeeder' \
            --force

        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\AcademicProgramLearningMaterialSeeder' \
            --force

        gosu "$RUN_AS" php artisan db:seed \
            --class='Database\Seeders\AcademicStageLearningMaterialSeeder' \
            --force
    fi

    if [ ! -L public/storage ]; then
        gosu "$RUN_AS" php artisan storage:link
    fi

    gosu "$RUN_AS" php artisan optimize:clear

    exec gosu "$RUN_AS" npx concurrently \
        --kill-others-on-fail \
        --names="server,queue,vite" \
        "php artisan serve --host=0.0.0.0 --port=8080 --no-reload" \
        "php artisan queue:listen --tries=1" \
        "npm run dev"
fi

PORT="${PORT:-8080}"

rm -f \
    /etc/apache2/mods-enabled/mpm_event.conf \
    /etc/apache2/mods-enabled/mpm_event.load \
    /etc/apache2/mods-enabled/mpm_worker.conf \
    /etc/apache2/mods-enabled/mpm_worker.load \
    /etc/apache2/mods-enabled/mpm_prefork.conf \
    /etc/apache2/mods-enabled/mpm_prefork.load

a2enmod mpm_prefork >/dev/null

sed -ri "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
sed -ri "s#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#" /etc/apache2/sites-available/000-default.conf

chown -R www-data:www-data storage bootstrap/cache

wait_for_database

php artisan config:clear

retry_command \
    "${DB_MIGRATION_MAX_ATTEMPTS:-10}" \
    "${DB_MIGRATION_RETRY_DELAY:-3}" \
    php artisan migrate --force

if [ "${RUN_SEEDER:-false}" = "true" ]; then
    echo "Running full database seeder..."

    php artisan db:seed --force
elif [ "${RUN_ASSESSMENT_SEEDER:-false}" = "true" ]; then
    echo "Synchronizing academic assessment and learning data..."

    php artisan db:seed \
        --class='Database\Seeders\CareerSeeder' \
        --force

    php artisan db:seed \
        --class='Database\Seeders\AcademicAssessmentSkillSeeder' \
        --force

    php artisan db:seed \
        --class='Database\Seeders\CareerSkillSeeder' \
        --force

    php artisan db:seed \
        --class='Database\Seeders\AcademicAssessmentSeeder' \
        --force

    php artisan db:seed \
        --class='Database\Seeders\AcademicAssessmentQuestionPoolSeeder' \
        --force

    php artisan db:seed \
        --class='Database\Seeders\AcademicAssessmentCleanupSeeder' \
        --force

    php artisan db:seed \
        --class='Database\Seeders\AcademicProgramLearningMaterialSeeder' \
        --force

    php artisan db:seed \
        --class='Database\Seeders\AcademicStageLearningMaterialSeeder' \
        --force

    echo "Academic assessment and learning data synchronized."
fi

if [ ! -L public/storage ]; then
    php artisan storage:link
fi

php artisan optimize:clear
php artisan optimize

apache2ctl configtest

exec docker-php-entrypoint "$@"
