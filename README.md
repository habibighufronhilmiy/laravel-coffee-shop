# Tens Coffee ☕

Aplikasi pemesanan kopi online berbasis web dengan panel admin & kasir.

## Fitur

### Customer (Web)
- Landing page marketing
- Registrasi & Login (email/password + Google OAuth)
- Lihat menu + filter kategori + search
- **Favorit (Wishlist)** menu ❤️
- **Rating & Review** menu ⭐
- Keranjang belanja
- Checkout (Dine-in / Pickup / Delivery)
- Kode Voucher diskon
- Pembayaran Midtrans (QRIS, e-wallet, dll) / Cash
- **Invoice Number** (format: `INV-YYYYMMDD-xxxx`)
- **Pesanan Saya** dengan pagination, filter status, search
- **Lacak Pesanan** (tracking delivery)
- **Profil Saya** (edit nama, email, no telp, ganti password)
- **Buku Alamat** (simpan alamat pengiriman)

### Admin Panel (`/admin`)
- Dashboard dengan statistik
- CRUD: Users, Menus, Kategoris, Outlets, Vouchers, Banners
- **Transaksi** (lihat semua pesanan dengan filter & detail)
- Laporan + export CSV

### Kasir Panel (`/kasir`)
- Buat pesanan manual
- Update status pesanan
- Print struk (view + PDF download)
- Laporan + export CSV/PDF

### API
- RESTful API untuk mobile/frontend
- Autentikasi Sanctum
- Midtrans payment gateway integration
- Voucher system

## Teknologi

- **Backend:** Laravel, MySQL
- **Admin Panel:** Filament v3
- **Frontend:** Alpine.js, Tailwind CSS, Axios
- **Payment:** Midtrans Snap
- **Maps:** Leaflet.js
- **Auth:** Laravel Sanctum, Socialite (Google)

## Instalasi

```bash
# Clone & install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
DB_DATABASE=kopisop
DB_USERNAME=root
DB_PASSWORD=

# Migrate & seed
php artisan migrate
php artisan storage:link

# Jalankan
php artisan serve
```

## Akun Default

Buat akun admin/kasir via register atau langsung di database:
- Admin: role `admin` — akses `/admin`
- Kasir: role `kasir` — akses `/kasir`

## Environment Variables Penting

```
MIDTRANS_MERCHANT_ID=
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```
