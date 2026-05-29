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

        // 2. Modifikasi Index Unik secara aman dengan melepas Foreign Key terlebih dahulu
        Schema::table('cart_items', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('cart_items'))->pluck('name')->toArray();

            if (in_array('cart_items_user_id_menu_id_menu_variant_id_unique', $indexes)) {
                // Hapus foreign key terlebih dahulu agar index uniknya bisa di-drop oleh MySQL
                // Asumsi nama FK menggunakan konvensi standar Laravel [table]_[column]_foreign
                $table->dropForeign('cart_items_user_id_foreign');
                $table->dropForeign('cart_items_menu_id_foreign');
                $table->dropForeign('cart_items_menu_variant_id_foreign');

                // Baru hapus unique key lama
                $table->dropUnique('cart_items_user_id_menu_id_menu_variant_id_unique');
            }

            // Buat unique key baru jika belum ada
            if (!in_array('cart_items_user_opts_unique', $indexes)) {
                $table->unique(['user_id', 'menu_id', 'menu_variant_id', 'options_hash'], 'cart_items_user_opts_unique');
            }

            // Pasang kembali foreign key setelah index diperbarui
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('menu_variant_id')->references('id')->on('menu_variants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('cart_items'))->pluck('name')->toArray();

            // Drop unique key baru jika ada
            if (in_array('cart_items_user_opts_unique', $indexes)) {
                // Lepas foreign key dulu sebelum utak-atik indeks unik
                $table->dropForeign('cart_items_user_id_foreign');
                $table->dropForeign('cart_items_menu_id_foreign');
                $table->dropForeign('cart_items_menu_variant_id_foreign');

                $table->dropUnique('cart_items_user_opts_unique');
            }

            // Kembalikan unique key lama jika belum ada
            if (!in_array('cart_items_user_id_menu_id_menu_variant_id_unique', $indexes)) {
                $table->unique(['user_id', 'menu_id', 'menu_variant_id'], 'cart_items_user_id_menu_id_menu_variant_id_unique');
            }

            // Pasang kembali foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('menu_variant_id')->references('id')->on('menu_variants')->onDelete('cascade');
        });

        // Hapus kolom options_hash jika ada
        if (Schema::hasColumn('cart_items', 'options_hash')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropColumn('options_hash');
            });
        }
    }
};