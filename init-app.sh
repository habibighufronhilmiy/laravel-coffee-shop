#!/bin/bash

# Hapus set -e agar kegagalan migrasi tidak langsung mematikan seluruh kontainer
set +e

echo "=== Menjalankan Migrasi Database ==="
# Jalankan migrasi, jika gagal beri peringatan tapi jangan hentikan script
php artisan migrate --force || echo "PERINGATAN: Migrasi database gagal. Periksa koneksi DB Anda nanti."

# Aktifkan kembali set -e untuk perintah optimasi bawaan
set -e

echo "=== Membersihkan Cache lama ==="
php artisan optimize:clear

echo "=== Membuat Cache Optimasi Baru ==="
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "=== Pre-deployment Selesai! ==="