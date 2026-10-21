#!/bin/sh

set -eu

php artisan storage:link --force
php artisan migrate --force

if [ "${RUN_PRODUCTION_DEMO_SEEDER:-}" = "true" ]; then
    php artisan db:seed --class=ProductionDemoSeeder --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
