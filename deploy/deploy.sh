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

echo "==> artisan"
php artisan migrate --force
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true
