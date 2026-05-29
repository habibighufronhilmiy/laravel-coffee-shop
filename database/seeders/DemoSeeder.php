<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Outlet;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'judul' => 'Promo Kopi Spesial',
            'deskripsi' => 'Nikmati kopi spesial pilihan kami dengan harga spesial!',
            'gambar' => 'banners/promo_kopi.png',
            'link' => '/menu',
            'aktif' => true,
            'urutan' => 1,
        ]);

        Banner::create([
            'judul' => 'Menu Sarapan',
            'deskripsi' => 'Mulai harimu dengan sarapan lezat di Tens Coffee',
            'gambar' => 'banners/sarapan.png',
            'link' => '/menu?kategori=makanan',
            'aktif' => true,
            'urutan' => 2,
        ]);

        Banner::create([
            'judul' => 'Non Kopi Favorit',
            'deskripsi' => 'Bukan pecinta kopi? Tenang, kami punya banyak pilihan minuman non kopi!',
            'gambar' => 'banners/non_kopi.png',
            'link' => '/menu?kategori=non-kopi',
            'aktif' => true,
            'urutan' => 3,
        ]);

        Voucher::create([
            'kode' => 'BARU10',
            'nama' => 'Diskon 10% Untuk Member Baru',
            'tipe' => 'persen',
            'nilai' => 10,
            'min_belanja' => 50000,
            'maks_diskon' => 20000,
            'kuota' => 100,
            'terpakai' => 0,
            'berlaku_mulai' => now()->subDay(),
            'berlaku_sampai' => now()->addMonth(),
            'aktif' => true,
        ]);

        Voucher::create([
            'kode' => 'KOPI20',
            'nama' => 'Diskon 20% Semua Kopi',
            'tipe' => 'persen',
            'nilai' => 20,
            'min_belanja' => 30000,
            'maks_diskon' => 15000,
            'kuota' => 50,
            'terpakai' => 0,
            'berlaku_mulai' => now()->subDay(),
            'berlaku_sampai' => now()->addWeek(),
            'aktif' => true,
        ]);

        Voucher::create([
            'kode' => 'HEMAT15',
            'nama' => 'Potongan Rp15.000',
            'tipe' => 'nominal',
            'nilai' => 15000,
            'min_belanja' => 75000,
            'maks_diskon' => null,
            'kuota' => 30,
            'terpakai' => 0,
            'berlaku_mulai' => now()->subDay(),
            'berlaku_sampai' => now()->addMonth(),
            'aktif' => true,
        ]);

        Voucher::create([
            'kode' => 'GRATISONG',
            'nama' => 'Gratis Ongkir (Potongan Rp5.000)',
            'tipe' => 'nominal',
            'nilai' => 5000,
            'min_belanja' => 0,
            'maks_diskon' => null,
            'kuota' => null,
            'terpakai' => 0,
            'berlaku_mulai' => now()->subDay(),
            'berlaku_sampai' => now()->addDays(3),
            'aktif' => true,
        ]);

        Outlet::create([
            'nama' => 'Tens Coffee Margonda',
            'alamat' => 'Jl. Margonda Raya No.519, Pondok Cina, Kec. Beji, Kota Depok 16424',
            'latitude' => '-6.3723',
            'longitude' => '106.8330',
            'no_telp' => '021-78881234',
            'jam_buka' => '08:00',
            'jam_tutup' => '22:00',
            'aktif' => true,
        ]);

        Outlet::create([
            'nama' => 'Tens Coffee UI',
            'alamat' => 'Kantin Vokasi UI, Kukusan, Kec. Beji, Kota Depok 16425',
            'latitude' => '-6.3589',
            'longitude' => '106.8317',
            'no_telp' => '021-78885678',
            'jam_buka' => '07:00',
            'jam_tutup' => '21:00',
            'aktif' => true,
        ]);
    }
}
