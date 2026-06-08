<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('no_meja', 10)->nullable()->change();
            $table->string('tipe_pengambilan', 50)->default('ditempat')->after('tipe_pemesanan');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->dropForeign(['outlet_id']);
            }
            $table->dropColumn('outlet_id');
            $table->string('no_meja', 10)->nullable(false)->change();
            $table->dropColumn('tipe_pengambilan');
        });
    }
};
