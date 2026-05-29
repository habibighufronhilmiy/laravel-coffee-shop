<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->foreignId('menu_variant_id')->nullable()->after('menu_id')
                ->constrained('menu_variants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menu_variant_id');
        });
    }
};
