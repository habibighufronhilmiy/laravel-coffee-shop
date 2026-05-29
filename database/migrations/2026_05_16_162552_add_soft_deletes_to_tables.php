<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('kategoris', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('transaksis', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamp('expired_at')->nullable()->after('midtrans_snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('kategoris', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('expired_at');
        });
    }
};
