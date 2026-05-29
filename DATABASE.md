# DATABASE - APLIKASI KOPISOP / TENS COFFEE

---

**Nama Database:** `kopisop`

**DBMS:** MySQL (production), SQLite in-memory (testing)

---

## Daftar Tabel

### Tabel Utama (Bisnis)

| No | Nama Tabel | Deskripsi |
|----|-----------|-----------|
| 1 | `users` | Data pengguna (customer, admin, kasir) |
| 2 | `kategoris` | Kategori menu minuman/makanan |
| 3 | `menus` | Data menu (nama, harga, stok, foto, deskripsi) |
| 4 | `menu_variants` | Varian menu (ukuran, rasa) dengan harga tambahan |
| 5 | `menu_option_groups` | Grup opsi (single/multiple pilihan) |
| 6 | `menu_option_group_items` | Item opsi dalam grup dengan harga tambahan |
| 7 | `outlets` | Data outlet/cabang (alamat, jam buka, koordinat) |
| 8 | `banners` | Banner promosi (gambar, link, urutan) |
| 9 | `vouchers` | Voucher diskon (kode, tipe, nilai, kuota, masa berlaku) |
| 10 | `voucher_pakai` | Riwayat pemakaian voucher oleh user |
| 11 | `transaksis` | Transaksi/pesanan (invoice, total, status, outlet, dll) |
| 12 | `detail_transaksis` | Item menu dalam transaksi (menu, varian, opsi, jumlah, subtotal) |
| 13 | `cart_items` | Item keranjang belanja customer |
| 14 | `ratings` | Rating bintang dan review dari customer |
| 15 | `wishlists` | Menu favorit yang disimpan customer |
| 16 | `addresses` | Alamat pengiriman customer |
| 17 | `loyalty_points` | Riwayat poin loyalitas customer |
| 18 | `notifications` | Notifikasi pengiriman (nama_kurir, no_resi) |

### Tabel Sistem (Laravel)

| No | Nama Tabel | Deskripsi |
|----|-----------|-----------|
| 19 | `personal_access_tokens` | Token autentikasi API (Sanctum) |
| 20 | `sessions` | Sesi login web |
| 21 | `password_reset_tokens` | Token reset password |
| 22 | `cache` | Cache aplikasi |
| 23 | `cache_locks` | Lock untuk cache |
| 24 | `jobs` | Antrian job |
| 25 | `job_batches` | Batch antrian job |
| 26 | `failed_jobs` | Job yang gagal diproses |

---

## Relasi Antar Tabel

```
users
  ├── 1:N ── addresses         (user punya banyak alamat)
  ├── 1:N ── cart_items         (user punya banyak item keranjang)
  ├── 1:N ── transaksis         (user punya banyak pesanan)
  ├── 1:N ── ratings            (user memberi banyak rating)
  ├── 1:N ── wishlists          (user punya banyak wishlist)
  ├── 1:N ── voucher_pakai      (user pakai banyak voucher)
  └── 1:N ── loyalty_points     (user punya riwayat poin)

menus
  ├── N:1 ── kategoris          (menu punya satu kategori)
  ├── 1:N ── menu_variants      (menu punya banyak varian)
  ├── 1:N ── menu_option_groups (menu punya banyak grup opsi)
  │     └── 1:N ── menu_option_group_items (grup punya banyak item)
  ├── 1:N ── detail_transaksis  (menu muncul di banyak transaksi)
  ├── 1:N ── cart_items         (menu ada di banyak keranjang)
  ├── 1:N ── ratings            (menu punya banyak rating)
  └── 1:N ── wishlists          (menu ada di banyak wishlist)

outlets
  ├── 1:N ── transaksis         (outlet punya banyak transaksi)
  └── 1:N ── users              (user pilih outlet untuk pesan)

transaksis
  ├── 1:N ── detail_transaksis  (transaksi punya banyak item)
  └── N:1 ── vouchers           (transaksi pakai satu voucher, via voucher_pakai)
```
