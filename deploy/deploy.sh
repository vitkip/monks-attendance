#!/usr/bin/env bash
set -euo pipefail

# Runs on the production server (~/watnbt) after each push to main.
# GitHub Actions git-pulls this file to latest before executing it,
# so edits here take effect on the very next deploy.

cd "$(dirname "$0")/.."

echo "==> git pull"
git fetch origin main
git reset --hard origin/main

echo "==> composer install"
composer install --no-dev --optimize-autoloader --no-interaction

# Clear stale caches from the previous deploy before anything below (e.g. a
# migration) has a chance to fail and leave old cached routes/config serving
# the new code for the rest of the request lifecycle.
echo "==> clear stale caches"
php artisan optimize:clear

echo "==> artisan"
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true
