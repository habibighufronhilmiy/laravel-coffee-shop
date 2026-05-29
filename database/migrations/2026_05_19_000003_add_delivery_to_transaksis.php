<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->text('alamat_pengiriman')->nullable()->after('tipe_pengambilan');
            $table->decimal('latitude_pengiriman', 10, 7)->nullable()->after('alamat_pengiriman');
            $table->decimal('longitude_pengiriman', 10, 7)->nullable()->after('latitude_pengiriman');
            $table->integer('ongkir')->nullable()->after('total_harga');
            $table->timestamp('waktu_pengiriman_dijadwalkan')->nullable()->after('ongkir');
            $table->string('nama_kurir', 100)->nullable()->after('waktu_pengiriman_dijadwalkan');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_pesanan VARCHAR(50) NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN tipe_pengambilan VARCHAR(50) NOT NULL DEFAULT 'ditempat'");
        }
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_pengiriman',
                'latitude_pengiriman',
                'longitude_pengiriman',
                'ongkir',
                'waktu_pengiriman_dijadwalkan',
                'nama_kurir',
            ]);
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN status_pesanan VARCHAR(50) NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN tipe_pengambilan VARCHAR(50) NOT NULL DEFAULT 'ditempat'");
        }
    }
};
