<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom options_hash jika belum ada
        if (!Schema::hasColumn('cart_items', 'options_hash')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->string('options_hash', 64)->nullable()->after('menu_variant_id');
            });
        }

        // 2. Modifikasi Index Unik secara aman menggunakan fitur native Laravel
        Schema::table('cart_items', function (Blueprint $table) {
            // Mengambil daftar nama index yang ada di tabel secara native (Laravel 10/11+)
            $indexes = collect(Schema::getIndexes('cart_items'))->pluck('name')->toArray();

            // Hapus unique key lama jika masih ada
            if (in_array('cart_items_user_id_menu_id_menu_variant_id_unique', $indexes)) {
                $table->dropUnique('cart_items_user_id_menu_id_menu_variant_id_unique');
            }

            // Buat unique key baru jika belum ada
            if (!in_array('cart_items_user_opts_unique', $indexes)) {
                $table->unique(['user_id', 'menu_id', 'menu_variant_id', 'options_hash'], 'cart_items_user_opts_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('cart_items'))->pluck('name')->toArray();

            // Drop unique key baru jika ada
            if (in_array('cart_items_user_opts_unique', $indexes)) {
                $table->dropUnique('cart_items_user_opts_unique');
            }

            // Kembalikan unique key lama jika belum ada
            if (!in_array('cart_items_user_id_menu_id_menu_variant_id_unique', $indexes)) {
                $table->unique(['user_id', 'menu_id', 'menu_variant_id'], 'cart_items_user_id_menu_id_menu_variant_id_unique');
            }
        });

        // Hapus kolom options_hash jika ada
        if (Schema::hasColumn('cart_items', 'options_hash')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropColumn('options_hash');
            });
        }
    }
};