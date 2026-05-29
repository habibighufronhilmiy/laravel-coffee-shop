#!/bin/bash
set -e

echo "=== TensCoffee - Railway Deployment ==="
echo "APP_ENV: ${APP_ENV:-not set}"
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"

echo ""
echo "=== Menjalankan Migrasi Database ==="
php artisan migrate --force
echo "Migrasi selesai."

echo ""
echo "=== Membersihkan Cache ==="
php artisan optimize:clear

echo ""
echo "=== Optimasi Cache ==="
php artisan config:cache 2>/dev/null || echo "(config cache skipped)"
php artisan event:cache 2>/dev/null || echo "(event cache skipped)"
php artisan route:cache 2>/dev/null || echo "(route cache skipped)"
php artisan view:cache 2>/dev/null || echo "(view cache skipped)"

echo ""
echo "=== Pre-deployment Selesai! ==="
