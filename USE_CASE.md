# USE CASE - APLIKASI KOPISOP / TENS COFFEE

---

## Aktor

1. **Customer** — Pengguna aplikasi yang memesan menu
2. **Kasir** — Petugas yang melayani pesanan di outlet
3. **Admin** — Pengelola sistem dan data master

---

## Use Case Customer

1. Mendaftar akun baru
2. Login (email/password atau Google OAuth)
3. Melihat daftar menu (filter kategori, cari)
4. Melihat detail menu (varian, opsi, harga, rating)
5. Mengelola wishlist (tambah/hapus favorit)
6. Mengelola keranjang (tambah, ubah, hapus item)
7. Checkout (pilih outlet, tipe ambil, voucher, poin, alamat)
8. Melakukan pembayaran (Midtrans atau bayar di kasir)
9. Melihat riwayat pesanan (filter status, cari)
10. Membatalkan pesanan
11. Memesan ulang
12. Melacak pengiriman (tracking)
13. Memberi rating dan review
14. Mengelola profil (nama, email, no_telp, password)
15. Mengelola alamat pengiriman (tambah, edit, hapus)
16. Logout

---

## Use Case Kasir

1. Login panel kasir
2. Membuat transaksi offline (POS)
3. Mengupdate status pesanan (Proses, Siap, Antar, Selesai)
4. Melihat daftar transaksi (filter, cari)
5. Mencetak struk pembelian
6. Mendownload PDF struk
7. Melihat laporan (filter tanggal)
8. Export laporan (CSV / PDF)
9. Logout

---

## Use Case Admin

1. Login panel admin
2. Mengelola menu (CRUD + varian + opsi)
3. Mengelola kategori menu (CRUD)
4. Mengelola outlet (CRUD)
5. Mengelola user (CRUD, role)
6. Mengelola voucher diskon (CRUD)
7. Mengelola banner promosi (CRUD)
8. Melihat dashboard statistik
9. Memonitor seluruh transaksi
10. Melihat laporan (filter tanggal)
11. Export laporan (CSV / PDF)
12. Logout

---

## Relasi Include dan Extend

### Customer

| Use Case | Relasi | Use Case Terkait |
|----------|--------|------------------|
| Mendaftar akun baru | **extend** → | Login (auto-login setelah daftar) |
| Login | **include** → | Google OAuth (sistem eksternal) |
| Checkout | **include** → | Melihat detail menu (data item) |
| Checkout | **include** → | Cek voucher |
| Checkout | **include** → | Cek poin loyalitas |
| Checkout | **include** → | Hitung ongkir (via Nominatim) |
| Checkout | **include** → | OpenStreetMap (pilih lokasi delivery) |
| Melakukan pembayaran | **include** → | Midtrans Snap (sistem eksternal) |
| Melakukan pembayaran | **extend** ← | Konfirmasi pembayaran (jika bayar di kasir) |
| Melihat riwayat pesanan | **extend** → | Membatalkan pesanan |
| Melihat riwayat pesanan | **extend** → | Memesan ulang |
| Melihat riwayat pesanan | **extend** → | Memberi rating dan review |
| Melacak pengiriman | **include** → | OpenStreetMap (tracking map) |

### Kasir

| Use Case | Relasi | Use Case Terkait |
|----------|--------|------------------|
| Login panel kasir | **include** → | Autentikasi Filament |
| Membuat transaksi offline | **include** → | Melihat daftar transaksi (verifikasi) |
| Mengupdate status pesanan | **extend** → | Mencetak struk (jika status selesai) |
| Mencetak struk | **extend** → | Mendownload PDF struk |
| Melihat laporan | **extend** → | Export laporan |

### Admin

| Use Case | Relasi | Use Case Terkait |
|----------|--------|------------------|
| Login panel admin | **include** → | Autentikasi Filament |
| Melihat dashboard | **include** → | Melihat laporan |
| Melihat laporan | **extend** → | Export laporan |
| Mengelola menu | **include** → | Mengelola kategori (pilih kategori) |
| Memonitor transaksi | **extend** ← | Semua use case CRUD (konteks data) |

---

## Sistem Eksternal

| Sistem | Digunakan Pada Use Case | Fungsi |
|--------|-------------------------|--------|
| Google OAuth | Login | Autentikasi dengan akun Google |
| Midtrans Snap | Melakukan pembayaran | Payment gateway online |
| OpenStreetMap (Leaflet) | Checkout, Lacak pengiriman | Peta untuk pilih lokasi delivery |
| Nominatim | Checkout | Geocoding alamat ke koordinat |
| Geolocation API | Melihat menu, Checkout | Deteksi lokasi otomatis pengguna |
