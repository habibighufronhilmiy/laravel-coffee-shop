<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kasir_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('no_meja', 10);
            $table->integer('total_harga');
            $table->string('tipe_pemesanan', 50);
            $table->string('metode_pembayaran', 50);
            $table->string('status_pembayaran', 50)->default('belum_bayar');
            $table->string('status_pesanan', 50)->default('pending');
            $table->string('midtrans_snap_token', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
