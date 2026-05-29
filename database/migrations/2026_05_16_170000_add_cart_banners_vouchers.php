<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->integer('jumlah');
            $table->integer('harga');
            $table->integer('subtotal');
            $table->timestamps();
            $table->unique(['user_id', 'menu_id']);
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->string('deskripsi', 255)->nullable();
            $table->string('gambar', 255);
            $table->string('link', 255)->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 150);
            $table->string('tipe', 50);
            $table->integer('nilai');
            $table->integer('min_belanja')->default(0);
            $table->integer('maks_diskon')->nullable();
            $table->integer('kuota')->nullable();
            $table->integer('terpakai')->default(0);
            $table->timestamp('berlaku_mulai')->nullable();
            $table->timestamp('berlaku_sampai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('voucher_pakai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaksi_id')->constrained()->cascadeOnDelete();
            $table->integer('diskon');
            $table->timestamps();
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('nama_menu');
            $table->index('kategori_id');
            $table->index('stok');
        });

        Schema::table('kategoris', function (Blueprint $table) {
            $table->string('icon', 50)->nullable()->after('nama_kategori');
            $table->unique('nama_kategori');
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('kasir_id');
            $table->index('status_pembayaran');
            $table->index('status_pesanan');
            $table->index(['status_pembayaran', 'status_pesanan']);
        });

        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->index('menu_id');
            $table->index('transaksi_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_pakai');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('cart_items');

        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
            $table->dropIndex(['kategori_id']);
            $table->dropIndex(['stok']);
        });

        Schema::table('kategoris', function (Blueprint $table) {
            $table->dropColumn('icon');
            $table->dropUnique(['nama_kategori']);
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['kasir_id']);
            $table->dropIndex(['status_pembayaran']);
            $table->dropIndex(['status_pesanan']);
            $table->dropIndex(['status_pembayaran', 'status_pesanan']);
        });

        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->dropIndex(['menu_id']);
            $table->dropIndex(['transaksi_id']);
        });
    }
};
