# SPESIFIKASI DOKUMEN MASUKAN DAN KELUARAN
## Aplikasi Kopisop / Tens Coffee

---

## 2.1.4. Spesifikasi Dokumen Masukan

### A. Web Forms (Customer)

**1. Nama Dokumen : Form Login**
Fungsi : Untuk memvalidasi kredensial pengguna (email dan password) saat masuk ke sistem
Sumber : Customer
Tujuan : Sistem melakukan autentikasi dan memulai sesi pengguna
Media : Form web
Frekuensi : Setiap kali pengguna login
Jumlah : 1 form per login

**2. Nama Dokumen : Form Register**
Fungsi : Untuk mencatat data identitas pengguna baru (name, email, password, no_telp)
Sumber : Calon Customer
Tujuan : Menyimpan data anggota baru ke dalam sistem
Media : Form web
Frekuensi : Setiap kali ada pendaftaran baru
Jumlah : 1 form per pendaftaran

**3. Nama Dokumen : Form Logout**
Fungsi : Untuk mengakhiri sesi login pengguna
Sumber : Customer
Tujuan : Sistem menghapus sesi dan token autentikasi
Media : Form web (POST)
Frekuensi : Setiap kali pengguna logout
Jumlah : 1 form per logout

**4. Nama Dokumen : Form Konfirmasi Pembayaran**
Fungsi : Untuk mengkonfirmasi pembayaran tunai di kasir
Sumber : Customer
Tujuan : Sistem mengubah status pembayaran menjadi lunas
Media : Form web (POST)
Frekuensi : Setiap kali konfirmasi pembayaran
Jumlah : 1 form per transaksi

### B. API Request (Mobile / SPA Customer)

**5. Nama Dokumen : API Register**
Fungsi : Untuk mendaftarkan akun baru melalui aplikasi mobile (name, email, password, no_telp)
Sumber : Customer (Mobile)
Tujuan : Sistem membuat user baru dan mengembalikan token autentikasi
Media : REST API (POST /api/auth/register)
Frekuensi : Setiap kali pendaftaran baru
Jumlah : 1 request per pendaftaran

**6. Nama Dokumen : API Login**
Fungsi : Untuk login melalui aplikasi mobile dengan email dan password
Sumber : Customer (Mobile)
Tujuan : Sistem memvalidasi kredensial dan mengembalikan token API
Media : REST API (POST /api/auth/login)
Frekuensi : Setiap kali login mobile
Jumlah : 1 request per login

**7. Nama Dokumen : API Tambah Keranjang**
Fungsi : Untuk menambahkan item menu ke keranjang belanja (menu_id, variant, opsi, jumlah)
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem menyimpan item ke keranjang pengguna
Media : REST API (POST /api/cart/add)
Frekuensi : Setiap kali customer menambah item
Jumlah : 1 request per item

**8. Nama Dokumen : API Checkout**
Fungsi : Untuk memproses pemesanan baru (outlet_id, tipe_pengambilan, no_meja, metode_pembayaran, voucher, alamat, poin)
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem membuat transaksi baru, mengurangi stok, dan mengembalikan Snap Token Midtrans
Media : REST API (POST /api/checkout)
Frekuensi : Setiap kali pemesanan
Jumlah : 1 request per transaksi

**9. Nama Dokumen : API Cek Voucher**
Fungsi : Untuk memverifikasi kode voucher dan menghitung diskon
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem memvalidasi voucher dan mengembalikan informasi diskon
Media : REST API (POST /api/checkout/cekVoucher)
Frekuensi : Setiap kali customer mengecek voucher
Jumlah : 1 request per pengecekan

**10. Nama Dokumen : API Hitung Ongkir**
Fungsi : Untuk menghitung biaya ongkos kirim berdasarkan lokasi outlet dan tujuan
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem mengembalikan biaya ongkir yang dihitung dari jarak
Media : REST API (POST /api/checkout/hitung-ongkir)
Frekuensi : Setiap kali customer mengganti lokasi pengiriman
Jumlah : 1 request per perubahan lokasi

**11. Nama Dokumen : API Batalkan Pesanan**
Fungsi : Untuk membatalkan pesanan yang masih berstatus pending
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem mengubah status pesanan menjadi dibatalkan dan mengembalikan stok
Media : REST API (POST /api/orders/{id}/cancel)
Frekuensi : Setiap kali pembatalan pesanan
Jumlah : 1 request per pembatalan

**12. Nama Dokumen : API Pesan Ulang**
Fungsi : Untuk memesan ulang item dari pesanan sebelumnya
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem menambahkan semua item dari pesanan lama ke keranjang aktif
Media : REST API (POST /api/orders/{id}/reorder)
Frekuensi : Setiap kali pesan ulang
Jumlah : 1 request per pesan ulang

**13. Nama Dokumen : API Update Profil**
Fungsi : Untuk memperbarui data profil pengguna (name, email, no_telp)
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem menyimpan perubahan data profil
Media : REST API (PUT /api/profile)
Frekuensi : Setiap kali ubah profil
Jumlah : 1 request per perubahan

**14. Nama Dokumen : API Ganti Password**
Fungsi : Untuk mengubah password akun (current_password, password, confirmation)
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem memperbarui password pengguna
Media : REST API (PUT /api/profile/password)
Frekuensi : Setiap kali ganti password
Jumlah : 1 request per perubahan

**15. Nama Dokumen : API Tambah Alamat**
Fungsi : Untuk menyimpan alamat pengiriman baru (label, alamat, penerima, no_telp)
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem menambahkan alamat baru ke daftar alamat pengguna
Media : REST API (POST /api/addresses)
Frekuensi : Setiap kali tambah alamat
Jumlah : 1 request per alamat

**16. Nama Dokumen : API Toggle Wishlist**
Fungsi : Untuk menambah atau menghapus menu dari daftar favorit
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem menambah/menghapus item wishlist pengguna
Media : REST API (POST /api/wishlists/toggle)
Frekuensi : Setiap kali toggle favorit
Jumlah : 1 request per aksi

**17. Nama Dokumen : API Kirim Rating**
Fungsi : Untuk mengirim rating bintang dan ulasan untuk menu yang sudah dibeli
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem menyimpan rating dan review dengan verifikasi pembelian
Media : REST API (POST /api/ratings)
Frekuensi : Setiap kali memberi rating
Jumlah : 1 request per menu

**18. Nama Dokumen : API Tukar Poin**
Fungsi : Untuk mengecek informasi penukaran poin loyalitas
Sumber : Customer (Mobile/SPA)
Tujuan : Sistem mengembalikan informasi penukaran poin
Media : REST API (POST /api/loyalty/redeem)
Frekuensi : Setiap kali cek poin
Jumlah : 1 request per pengecekan

**19. Nama Dokumen : Notifikasi Midtrans**
Fungsi : Untuk menerima callback status pembayaran dari payment gateway Midtrans
Sumber : Midtrans (Payment Gateway)
Tujuan : Sistem memperbarui status pembayaran transaksi (lunas/gagal/expired)
Media : REST API (POST /api/midtrans/notification)
Frekuensi : Setiap kali ada pembayaran Midtrans
Jumlah : 1 request per transaksi

### C. Filament Admin Forms

**20. Nama Dokumen : Form Kelola Menu**
Fungsi : Untuk menambah atau mengedit data menu (kategori, nama, deskripsi, harga, stok, foto, varian, opsi)
Sumber : Admin
Tujuan : Sistem menyimpan perubahan data menu
Media : Form Filament (Web)
Frekuensi : Setiap kali tambah/ubah menu
Jumlah : 1 form per menu

**21. Nama Dokumen : Form Kelola Kategori**
Fungsi : Untuk menambah atau mengedit kategori menu (nama_kategori, icon)
Sumber : Admin
Tujuan : Sistem menyimpan perubahan data kategori
Media : Form Filament (Web)
Frekuensi : Setiap kali tambah/ubah kategori
Jumlah : 1 form per kategori

**22. Nama Dokumen : Form Kelola Outlet**
Fungsi : Untuk menambah atau mengedit data outlet (nama, alamat, lat/lng, no_telp, jam_buka, jam_tutup, foto, status)
Sumber : Admin
Tujuan : Sistem menyimpan perubahan data outlet
Media : Form Filament (Web)
Frekuensi : Setiap kali tambah/ubah outlet
Jumlah : 1 form per outlet

**23. Nama Dokumen : Form Kelola User**
Fungsi : Untuk menambah atau mengedit data pengguna (name, email, password, no_telp, role)
Sumber : Admin
Tujuan : Sistem menyimpan perubahan data user
Media : Form Filament (Web)
Frekuensi : Setiap kali tambah/ubah user
Jumlah : 1 form per user

**24. Nama Dokumen : Form Kelola Voucher**
Fungsi : Untuk menambah atau mengedit voucher diskon (kode, nama, tipe, nilai, min_belanja, kuota, masa_berlaku)
Sumber : Admin
Tujuan : Sistem menyimpan perubahan data voucher
Media : Form Filament (Web)
Frekuensi : Setiap kali tambah/ubah voucher
Jumlah : 1 form per voucher

**25. Nama Dokumen : Form Kelola Banner**
Fungsi : Untuk menambah atau mengedit banner promosi (judul, deskripsi, gambar, link, urutan, status)
Sumber : Admin
Tujuan : Sistem menyimpan perubahan data banner
Media : Form Filament (Web)
Frekuensi : Setiap kali tambah/ubah banner
Jumlah : 1 form per banner

**26. Nama Dokumen : Form Filter Laporan**
Fungsi : Untuk memfilter laporan berdasarkan rentang tanggal
Sumber : Admin / Kasir
Tujuan : Sistem menampilkan data laporan sesuai periode yang dipilih
Media : Form Filament (DatePicker)
Frekuensi : Setiap kali buka halaman laporan
Jumlah : 1 form per permintaan laporan

### D. Filament Kasir Forms

**27. Nama Dokumen : Form Transaksi Kasir**
Fungsi : Untuk mencatat transaksi offline/pickup oleh kasir (outlet_id, tipe ambil, no_meja, item menu, total)
Sumber : Kasir
Tujuan : Sistem membuat transaksi baru dengan status pesanan langsung diproses
Media : Form Filament (Web)
Frekuensi : Setiap kali transaksi kasir
Jumlah : 1 form per transaksi

**28. Nama Dokumen : Form Update Status Pesanan**
Fungsi : Untuk memperbarui status pesanan (Proses, Siap, Antar, Selesai, Batalkan)
Sumber : Kasir
Tujuan : Sistem mengubah status_pesanan dan mencatat waktu perubahan
Media : Filament Table Actions (Web)
Frekuensi : Setiap kali update status
Jumlah : 1 aksi per update

---

## 2.1.5. Spesifikasi Dokumen Keluaran

### A. Halaman Web (Customer Frontend)

**1. Nama Dokumen : Halaman Landing**
Fungsi : Untuk menampilkan informasi hero, tentang perusahaan, menu unggulan, keunggulan, testimoni, dan ajakan mendaftar
Sumber : Sistem
Tujuan : Memberikan gambaran umum Tens Coffee kepada pengunjung
Media : Halaman web (GET /)
Frekuensi : Setiap kali pengunjung membuka halaman utama
Jumlah : 1 halaman per kunjungan

**2. Nama Dokumen : Halaman Menu**
Fungsi : Untuk menampilkan daftar menu lengkap dengan banner carousel, filter kategori, pencarian, varian/opsi item, status wishlist, rating, dan stok
Sumber : Sistem
Tujuan : Memberikan informasi menu kepada customer untuk memilih dan memesan
Media : Halaman web (GET /menu)
Frekuensi : Setiap kali customer melihat menu
Jumlah : 1 halaman per kunjungan

**3. Nama Dokumen : Halaman Keranjang**
Fungsi : Untuk menampilkan daftar item yang dipilih, ringkasan harga, dan tombol checkout
Sumber : Sistem
Tujuan : Memberikan ringkasan pesanan sebelum checkout
Media : Halaman web (GET /cart)
Frekuensi : Setiap kali customer membuka keranjang
Jumlah : 1 halaman per kunjungan

**4. Nama Dokumen : Halaman Checkout**
Fungsi : Untuk menampilkan form lengkap pemesanan: pilih outlet, tipe pengambilan, detail pesanan, voucher, peta lokasi (Leaflet + OSM), poin loyalitas, dan ringkasan pembayaran
Sumber : Sistem
Tujuan : Memfasilitasi customer menyelesaikan pemesanan dengan semua informasi yang diperlukan
Media : Halaman web (GET /checkout)
Frekuensi : Setiap kali customer checkout
Jumlah : 1 halaman per checkout

**5. Nama Dokumen : Halaman Pesanan**
Fungsi : Untuk menampilkan riwayat pesanan customer dengan filter status, pencarian, pagination, tombol bayar/batal/pesan ulang/beri rating
Sumber : Sistem
Tujuan : Memberikan informasi status dan riwayat pesanan kepada customer
Media : Halaman web (GET /orders)
Frekuensi : Setiap kali customer melihat pesanan
Jumlah : 1 halaman per kunjungan

**6. Nama Dokumen : Halaman Wishlist**
Fungsi : Untuk menampilkan daftar menu favorit yang disimpan customer
Sumber : Sistem
Tujuan : Memberikan akses cepat ke menu favorit customer
Media : Halaman web (GET /wishlist)
Frekuensi : Setiap kali customer membuka wishlist
Jumlah : 1 halaman per kunjungan

**7. Nama Dokumen : Halaman Tracking**
Fungsi : Untuk menampilkan progress status pengiriman delivery dengan timeline (Pesanan -> Diproses -> Dikirim -> Selesai)
Sumber : Sistem
Tujuan : Memberikan informasi real-time status pengiriman kepada customer
Media : Halaman web (GET /tracking)
Frekuensi : Setiap kali customer melacak pengiriman
Jumlah : 1 halaman per tracking

**8. Nama Dokumen : Halaman Profil**
Fungsi : Untuk menampilkan data profil pengguna, form edit profil, ganti password, dan daftar alamat tersimpan
Sumber : Sistem
Tujuan : Memberikan akses kepada customer untuk mengelola data akun dan alamat
Media : Halaman web (GET /profile)
Frekuensi : Setiap kali customer membuka profil
Jumlah : 1 halaman per kunjungan

**9. Nama Dokumen : Halaman Login**
Fungsi : Untuk menampilkan form login dengan email/password dan tombol Google OAuth
Sumber : Sistem
Tujuan : Memfasilitasi autentikasi pengguna
Media : Halaman web (GET /login)
Frekuensi : Setiap kali pengguna login
Jumlah : 1 halaman per login

**10. Nama Dokumen : Halaman Register**
Fungsi : Untuk menampilkan form pendaftaran akun baru
Sumber : Sistem
Tujuan : Memfasilitasi pendaftaran pengguna baru
Media : Halaman web (GET /register)
Frekuensi : Setiap kali pendaftaran baru
Jumlah : 1 halaman per pendaftaran

### B. Respons API (Mobile / SPA)

**11. Nama Dokumen : Response Daftar Menu**
Fungsi : Untuk menyajikan data seluruh menu beserta kategori, varian, opsi, status wishlist, jumlah pesanan, dan rating
Sumber : Sistem
Tujuan : Memberikan data menu ke aplikasi mobile/SPA untuk ditampilkan
Media : JSON (GET /api/menu)
Frekuensi : Setiap kali aplikasi memuat halaman menu
Jumlah : 1 response per load

**12. Nama Dokumen : Response Detail Menu**
Fungsi : Untuk menyajikan data detail satu menu termasuk varian, opsi, dan rating
Sumber : Sistem
Tujuan : Memberikan data lengkap satu menu untuk ditampilkan di modal detail
Media : JSON (GET /api/menu/{menu})
Frekuensi : Setiap kali customer melihat detail menu
Jumlah : 1 response per menu

**13. Nama Dokumen : Response Daftar Kategori**
Fungsi : Untuk menyajikan daftar kategori menu
Sumber : Sistem
Tujuan : Memberikan data kategori untuk filter menu
Media : JSON (GET /api/kategoris)
Frekuensi : Setiap kali aplikasi memuat halaman menu
Jumlah : 1 response per load

**14. Nama Dokumen : Response Banner**
Fungsi : Untuk menyajikan data banner promosi yang aktif
Sumber : Sistem
Tujuan : Memberikan data banner untuk ditampilkan di carousel
Media : JSON (GET /api/banners)
Frekuensi : Setiap kali aplikasi memuat halaman menu
Jumlah : 1 response per load

**15. Nama Dokumen : Response Keranjang**
Fungsi : Untuk menyajikan data item keranjang belanja lengkap dengan varian, opsi, subtotal, dan total harga
Sumber : Sistem
Tujuan : Memberikan informasi keranjang ke aplikasi mobile
Media : JSON (GET /api/cart)
Frekuensi : Setiap kali customer membuka keranjang
Jumlah : 1 response per kunjungan

**16. Nama Dokumen : Response Daftar Pesanan**
Fungsi : Untuk menyajikan riwayat transaksi customer dengan pagination, filter status, dan pencarian
Sumber : Sistem
Tujuan : Memberikan data riwayat pesanan ke aplikasi mobile
Media : JSON (GET /api/orders)
Frekuensi : Setiap kali customer melihat pesanan
Jumlah : 1 response per kunjungan

**17. Nama Dokumen : Response Detail Pesanan**
Fungsi : Untuk menyajikan data detail satu transaksi termasuk item, voucher, outlet, dan status
Sumber : Sistem
Tujuan : Memberikan informasi lengkap satu pesanan
Media : JSON (GET /api/orders/{transaksi})
Frekuensi : Setiap kali customer melihat detail pesanan
Jumlah : 1 response per pesanan

**18. Nama Dokumen : Response Profil**
Fungsi : Untuk menyajikan data profil pengguna (name, email, no_telp, poin)
Sumber : Sistem
Tujuan : Memberikan data profil ke aplikasi mobile
Media : JSON (GET /api/profile)
Frekuensi : Setiap kali customer membuka profil
Jumlah : 1 response per kunjungan

**19. Nama Dokumen : Response Alamat**
Fungsi : Untuk menyajikan daftar alamat tersimpan customer
Sumber : Sistem
Tujuan : Memberikan data alamat pengiriman ke aplikasi mobile
Media : JSON (GET /api/addresses)
Frekuensi : Setiap kali customer membuka alamat
Jumlah : 1 response per kunjungan

**20. Nama Dokumen : Response Wishlist**
Fungsi : Untuk menyajikan daftar menu favorit customer dengan informasi menu dan kategori
Sumber : Sistem
Tujuan : Memberikan data wishlist ke aplikasi mobile
Media : JSON (GET /api/wishlists)
Frekuensi : Setiap kali customer membuka wishlist
Jumlah : 1 response per kunjungan

**21. Nama Dokumen : Response Rating**
Fungsi : Untuk menyajikan daftar rating dan review pengguna untuk suatu menu
Sumber : Sistem
Tujuan : Memberikan data ulasan untuk ditampilkan di modal review
Media : JSON (GET /api/menu/{menu}/ratings)
Frekuensi : Setiap kali customer membuka review menu
Jumlah : 1 response per menu

**22. Nama Dokumen : Response Loyalty**
Fungsi : Untuk menyajikan saldo poin dan history transaksi poin customer
Sumber : Sistem
Tujuan : Memberikan informasi poin loyalitas ke aplikasi mobile
Media : JSON (GET /api/loyalty)
Frekuensi : Setiap kali customer mengecek poin
Jumlah : 1 response per kunjungan

**23. Nama Dokumen : Response Cek Voucher**
Fungsi : Untuk menyajikan hasil validasi voucher (valid/tidak, besaran diskon, total setelah diskon)
Sumber : Sistem
Tujuan : Memberikan informasi diskon voucher ke aplikasi mobile
Media : JSON (POST /api/checkout/cekVoucher)
Frekuensi : Setiap kali customer mengecek voucher
Jumlah : 1 response per pengecekan

**24. Nama Dokumen : Response Hitung Ongkir**
Fungsi : Untuk menyajikan biaya ongkos kirim berdasarkan jarak outlet ke tujuan
Sumber : Sistem
Tujuan : Memberikan informasi ongkir ke aplikasi mobile
Media : JSON (POST /api/checkout/hitung-ongkir)
Frekuensi : Setiap kali customer mengganti lokasi
Jumlah : 1 response per perubahan

**25. Nama Dokumen : Response Checkout**
Fungsi : Untuk menyajikan hasil pemrosesan checkout (message, data transaksi, Snap Token Midtrans)
Sumber : Sistem
Tujuan : Memberikan konfirmasi pemesanan dan token pembayaran ke aplikasi mobile
Media : JSON (POST /api/checkout)
Frekuensi : Setiap kali checkout berhasil
Jumlah : 1 response per transaksi

**26. Nama Dokumen : Response Daftar Outlet**
Fungsi : Untuk menyajikan daftar outlet aktif Tens Coffee
Sumber : Sistem
Tujuan : Memberikan data outlet ke aplikasi mobile untuk dipilih customer
Media : JSON (GET /api/outlets)
Frekuensi : Setiap kali customer memilih outlet
Jumlah : 1 response per kunjungan

**27. Nama Dokumen : Response Outlet Terdekat**
Fungsi : Untuk menyajikan daftar outlet yang diurutkan berdasarkan jarak dari lokasi customer
Sumber : Sistem
Tujuan : Memberikan rekomendasi outlet terdekat ke aplikasi mobile
Media : JSON (GET /api/outlets/nearby)
Frekuensi : Setiap kali customer mendeteksi lokasi
Jumlah : 1 response per deteksi

**28. Nama Dokumen : Response Midtrans Client Key**
Fungsi : Untuk menyajikan client key yang diperlukan untuk inisialisasi Midtrans Snap
Sumber : Sistem
Tujuan : Memberikan konfigurasi Midtrans ke aplikasi frontend
Media : JSON (GET /api/midtrans/client-key)
Frekuensi : Setiap kali customer checkout dengan Midtrans
Jumlah : 1 response per checkout

**29. Nama Dokumen : Response Auth User**
Fungsi : Untuk menyajikan data user yang sedang login (name, email, role)
Sumber : Sistem
Tujuan : Memberikan informasi session user ke aplikasi mobile
Media : JSON (GET /api/auth/user)
Frekuensi : Setiap kali aplikasi mengecek session
Jumlah : 1 response per pengecekan

**30. Nama Dokumen : Response Notifikasi Midtrans**
Fungsi : Untuk menyajikan konfirmasi penerimaan callback notifikasi pembayaran dari Midtrans
Sumber : Sistem (ke Midtrans)
Tujuan : Memberikan acknowledgment ke Midtrans bahwa notifikasi telah diproses
Media : JSON (POST /api/midtrans/notification)
Frekuensi : Setiap kali ada notifikasi pembayaran
Jumlah : 1 response per notifikasi

### C. Halaman Filament Admin

**31. Nama Dokumen : Dashboard Admin**
Fungsi : Untuk menyajikan ringkasan statistik: pendapatan hari ini, pesanan hari ini, pesanan pending, total pelanggan, menu terjual, dan tabel pesanan terbaru
Sumber : Sistem
Tujuan : Memberikan informasi monitoring bisnis secara real-time kepada admin
Media : Halaman Filament Dashboard (Web)
Frekuensi : Setiap kali admin membuka panel
Jumlah : 1 halaman per kunjungan

**32. Nama Dokumen : Tabel Kelola Menu**
Fungsi : Untuk menyajikan daftar menu dengan kolom foto, nama, kategori, harga, stok, dan aksi edit/hapus
Sumber : Sistem
Tujuan : Memberikan tampilan CRUD data menu kepada admin
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali admin mengelola menu
Jumlah : 1 tabel per kunjungan

**33. Nama Dokumen : Tabel Kelola Kategori**
Fungsi : Untuk menyajikan daftar kategori menu dengan kolom icon, nama, jumlah menu
Sumber : Sistem
Tujuan : Memberikan tampilan CRUD data kategori kepada admin
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali admin mengelola kategori
Jumlah : 1 tabel per kunjungan

**34. Nama Dokumen : Tabel Kelola Outlet**
Fungsi : Untuk menyajikan daftar outlet dengan kolom nama, alamat, no_telp, jam operasional, status aktif
Sumber : Sistem
Tujuan : Memberikan tampilan CRUD data outlet kepada admin
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali admin mengelola outlet
Jumlah : 1 tabel per kunjungan

**35. Nama Dokumen : Tabel Kelola User**
Fungsi : Untuk menyajikan daftar pengguna dengan kolom nama, email, no_telp, role, tanggal daftar
Sumber : Sistem
Tujuan : Memberikan tampilan CRUD data user kepada admin
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali admin mengelola user
Jumlah : 1 tabel per kunjungan

**36. Nama Dokumen : Tabel Kelola Voucher**
Fungsi : Untuk menyajikan daftar voucher dengan kolom kode, nama, tipe, nilai, kuota, terpakai, status aktif, masa berlaku
Sumber : Sistem
Tujuan : Memberikan tampilan CRUD data voucher kepada admin
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali admin mengelola voucher
Jumlah : 1 tabel per kunjungan

**37. Nama Dokumen : Tabel Kelola Banner**
Fungsi : Untuk menyajikan daftar banner promosi dengan kolom urutan, gambar, judul, deskripsi, status aktif
Sumber : Sistem
Tujuan : Memberikan tampilan CRUD data banner kepada admin
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali admin mengelola banner
Jumlah : 1 tabel per kunjungan

**38. Nama Dokumen : Tabel Transaksi Admin**
Fungsi : Untuk menyajikan daftar seluruh transaksi dengan kolom invoice, customer, outlet, meja, tipe ambil, total, metode bayar, status bayar, status pesanan, dan aksi view
Sumber : Sistem
Tujuan : Memberikan tampilan monitoring transaksi kepada admin
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali admin memonitor transaksi
Jumlah : 1 tabel per kunjungan

**39. Nama Dokumen : Halaman Laporan Admin**
Fungsi : Untuk menyajikan ringkasan laporan: total pendapatan, total transaksi, menu terjual, pelanggan, menu terlaris, pendapatan bulanan, dengan filter tanggal
Sumber : Sistem
Tujuan : Memberikan informasi analitik bisnis kepada admin
Media : Halaman Filament Page (Web)
Frekuensi : Setiap kali admin membuka laporan
Jumlah : 1 halaman per kunjungan

### D. Halaman Filament Kasir

**40. Nama Dokumen : Tabel Transaksi Kasir**
Fungsi : Untuk menyajikan daftar transaksi dengan aksi proses, siap, antar, selesai, batalkan, lihat struk, download PDF
Sumber : Sistem
Tujuan : Memberikan tampilan manajemen pesanan kepada kasir
Media : Halaman Filament Table (Web)
Frekuensi : Setiap kali kasir membuka panel
Jumlah : 1 tabel per kunjungan

**41. Nama Dokumen : Halaman Laporan Kasir**
Fungsi : Untuk menyajikan ringkasan laporan dengan filter tanggal: pendapatan, transaksi, menu terjual, pelanggan, menu terlaris, pendapatan bulanan
Sumber : Sistem
Tujuan : Memberikan informasi analitik kepada kasir
Media : Halaman Filament Page (Web)
Frekuensi : Setiap kali kasir membuka laporan
Jumlah : 1 halaman per kunjungan

**42. Nama Dokumen : Halaman Cetak Struk**
Fungsi : Untuk menyajikan struk pembelian dalam format cetak (HTML+CSS) berisi invoice, tanggal, outlet, meja, customer, item, total, status bayar
Sumber : Sistem
Tujuan : Memberikan dokumen bukti pembelian kepada customer
Media : Halaman cetak Filament (Web)
Frekuensi : Setiap kali kasir mencetak struk
Jumlah : 1 struk per transaksi

### E. Dokumen Export / Download

**43. Nama Dokumen : PDF Struk Pembelian**
Fungsi : Untuk menyajikan struk pembelian dalam format PDF yang dapat diunduh
Sumber : Sistem
Tujuan : Memberikan bukti pembelian digital kepada customer
Media : PDF Download
Frekuensi : Setiap kali kasir mengunduh struk
Jumlah : 1 file per transaksi

**44. Nama Dokumen : PDF Laporan**
Fungsi : Untuk menyajikan laporan ringkasan (pendapatan, transaksi, menu terjual, pelanggan), menu terlaris, pendapatan bulanan dalam format PDF
Sumber : Sistem
Tujuan : Memberikan dokumen laporan untuk keperluan administrasi dan manajemen
Media : PDF Download
Frekuensi : Setiap kali admin/kasir mengekspor laporan
Jumlah : 1 file per permintaan

**45. Nama Dokumen : CSV Laporan**
Fungsi : Untuk menyajikan data laporan dalam format CSV (ringkasan, menu terlaris, pendapatan bulanan) yang dapat dibuka di spreadsheet
Sumber : Sistem (GET /laporan/export)
Tujuan : Memberikan data mentah laporan untuk analisis lebih lanjut
Media : CSV Download
Frekuensi : Setiap kali admin/kasir mengekspor CSV
Jumlah : 1 file per permintaan
