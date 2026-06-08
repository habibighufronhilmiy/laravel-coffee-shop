# LAPORAN QA TESTING - TENS COFFEE (KOPISOP)

**Nama:** [Nama Kamu] | **NIM:** [NIM Kamu] | **Tanggal:** 3 Juni 2026

---

## A. PHPUnit Automated Test

┌────┬───────────────────────┬────────────────────────────────────────────┬──────────┬──────────────────────────────────────┐
│ No │ Feature               │ Scenario                                    │ Status   │ Keterangan                           │
├────┼───────────────────────┼────────────────────────────────────────────┼──────────┼──────────────────────────────────────┤
│ 1  │ Unit Test             │ Contoh test dasar PHPUnit                   │ ✅ PASS  │ Assert true === true                 │
│ 2  │ Feature Test          │ Landing page return 200                     │ ✅ PASS  │ Halaman utama dapat diakses          │
│ 3  │ Banners API           │ GET /api/banners data banner               │ ✅ PASS  │ Banner aktif (urutan)                │
│ 4  │ Menu API              │ GET /api/menu daftar menu                  │ ✅ PASS  │ 1 menu (Cappuccino)                  │
│ 5  │ Cart Operations       │ Tambah, update, hapus cart                 │ ✅ PASS  │ Harga 35.000 x 2 = 70.000            │
│ 6  │ Cek Voucher           │ POST cekVoucher kode TEST10                │ ✅ PASS  │ Diskon 5.000 (maks_diskon)           │
│ 7  │ Checkout Cash         │ Checkout bayar tunai                       │ ✅ PASS  │ Status lunas, stok berkurang         │
│ 8  │ Checkout + Voucher    │ Checkout pakai voucher                     │ ✅ PASS  │ Diskon 5.000, terpakai +1            │
│ 9  │ Stok Tidak Cukup      │ Checkout jumlah > stok                     │ ✅ PASS  │ Response 400, stok tidak cukup       │
│ 10 │ Orders List           │ GET /api/orders setelah checkout           │ ✅ PASS  │ 1 data pesanan                       │
│ 11 │ Voucher Invalid       │ Cek kode voucher tidak ada                 │ ✅ PASS  │ Response 400, tidak valid            │
└────┴───────────────────────┴────────────────────────────────────────────┴──────────┴──────────────────────────────────────┘

## B. API Functional Test

┌────┬───────────────────────┬────────────────────────────────────────────┬──────────┬──────────────────────────────────────┐
│ No │ Feature               │ Scenario                                    │ Status   │ Keterangan                           │
├────┼───────────────────────┼────────────────────────────────────────────┼──────────┼──────────────────────────────────────┤
│ 1  │ Menu List             │ GET /api/menu semua menu                   │ ✅ PASS  │ 11 menu tersedia                     │
│ 2  │ Menu Detail           │ GET /api/menu/{id} detail menu             │ ✅ PASS  │ Termasuk varian & opsi               │
│ 3  │ Banner List           │ GET /api/banners banner aktif              │ ✅ PASS  │ 3 banner aktif (urutan)              │
│ 4  │ Kategori List         │ GET /api/kategoris kategori menu           │ ✅ PASS  │ 4 kategori                           │
│ 5  │ Outlet List           │ GET /api/outlets outlet aktif              │ ✅ PASS  │ 2 outlet                             │
│ 6  │ Register              │ POST /api/auth/register akun baru          │ ✅ PASS  │ Return user + token (201)            │
│ 7  │ Login                 │ POST /api/auth/login email password        │ ✅ PASS  │ Return user + token (200)            │
│ 8  │ Get User              │ GET /api/auth/user data login              │ ✅ PASS  │ Return name, email, role             │
│ 9  │ Profile               │ GET /api/profile profil pribadi            │ ✅ PASS  │ Data name, email, no_telp            │
│ 10 │ Loyalty               │ GET /api/loyalty poin loyalitas            │ ✅ PASS  │ Balance + history                    │
│ 11 │ Wishlist              │ GET /api/wishlists daftar favorit          │ ✅ PASS  │ Menu + kategori                      │
│ 12 │ Orders                │ GET /api/orders riwayat pesanan            │ ✅ PASS  │ Pagination + filter                  │
│ 13 │ Addresses             │ GET /api/addresses alamat tersimpan        │ ✅ PASS  │ Return array alamat                  │
│ 14 │ Logout                │ POST /api/auth/logout hapus token          │ ✅ PASS  │ Token tidak bisa dipakai lagi        │
└────┴───────────────────────┴────────────────────────────────────────────┴──────────┴──────────────────────────────────────┘

## C. Web Pages Test

┌────┬───────────────────────┬────────────────────────────────────────────┬──────────┬──────────────────────────────────────┐
│ No │ Feature               │ Scenario                                    │ Status   │ Keterangan                           │
├────┼───────────────────────┼────────────────────────────────────────────┼──────────┼──────────────────────────────────────┤
│ 1  │ Landing Page          │ GET / halaman utama                       │ ✅ PASS  │ Hero, navbar, menu unggulan          │
│ 2  │ Halaman Login         │ GET /login form login                     │ ✅ PASS  │ Email + Password + Google OAuth      │
│ 3  │ Halaman Register      │ GET /register form daftar                 │ ✅ PASS  │ Name, Email, Password                │
│ 4  │ Halaman Menu          │ GET /menu daftar menu customer            │ ✅ PASS  │ Filter kategori, pilih outlet        │
│ 5  │ Admin Panel           │ GET /admin panel admin                    │ ✅ PASS  │ Redirect ke login (terproteksi)      │
│ 6  │ Kasir Panel           │ GET /kasir panel kasir                    │ ✅ PASS  │ Redirect ke login (terproteksi)      │
└────┴───────────────────────┴────────────────────────────────────────────┴──────────┴──────────────────────────────────────┘

## D. Security Check

┌────┬───────────────────────┬────────────────────────────────────────────┬──────────┬──────────────────────────────────────┐
│ No │ Feature               │ Scenario                                    │ Status   │ Keterangan                           │
├────┼───────────────────────┼────────────────────────────────────────────┼──────────┼──────────────────────────────────────┤
│ 1  │ SQL Injection         │ Input karakter berbahaya ke form/API       │ ✅ AMAN  │ Pakai Eloquent ORM + binding         │
│ 2  │ XSS                   │ Input script tag ke field                  │ ✅ AMAN  │ Blade auto-escape {{ }}              │
│ 3  │ CSRF Web              │ Akses form POST tanpa token               │ ✅ AMAN  │ Semua form pakai @csrf               │
│ 4  │ CSRF API              │ Akses API tanpa token                     │ ✅ AMAN  │ Setiap request perlu Bearer Token    │
│ 5  │ Password              │ Penyimpanan password user                  │ ✅ AMAN  │ Di-hash bcrypt Hash::make()          │
│ 6  │ Role Admin            │ Customer akses /admin                     │ ✅ AMAN  │ Diblokir canAccessPanel()            │
│ 7  │ Role Kasir            │ Customer akses /kasir                     │ ✅ AMAN  │ Diblokir canAccessPanel()            │
│ 8  │ Kepemilikan Data      │ User A akses data User B                  │ ✅ AMAN  │ Dicek user_id di Cart/Order/Address  │
└────┴───────────────────────┴────────────────────────────────────────────┴──────────┴──────────────────────────────────────┘

## E. Kesimpulan

┌────┬─────────────────────────────────┬──────────────────────┬──────────┐
│ No │ Metode Testing                  │ Hasil                │ Status   │
├────┼─────────────────────────────────┼──────────────────────┼──────────┤
│ 1  │ PHPUnit Automated Test          │ 11/11 Berhasil       │ ✅ PASS  │
│ 2  │ API Functional Test             │ 14/14 Berhasil       │ ✅ PASS  │
│ 3  │ Web Pages Test                  │ 6/6 Berhasil         │ ✅ PASS  │
│ 4  │ Security Check                  │ 8/8 Aman             │ ✅ PASS  │
└────┴─────────────────────────────────┴──────────────────────┴──────────┘

**Aplikasi Kopisop (Tens Coffee) telah diuji dan dinyatakan LAYAK.** Seluruh fitur berjalan dengan baik dan keamanan dasar terpenuhi.
