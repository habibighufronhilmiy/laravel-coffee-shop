<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('options_hash', 64)->nullable()->after('menu_variant_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_items_user_id_menu_id_menu_variant_id_unique');

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['user_id']);
            }

            $table->unique(['user_id', 'menu_id', 'menu_variant_id', 'options_hash'], 'cart_items_user_opts_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_user_opts_unique');

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->dropForeign(['menu_id']);
                $table->dropForeign(['user_id']);
            }

            $table->unique(['user_id', 'menu_id', 'menu_variant_id'], 'cart_items_user_id_menu_id_menu_variant_id_unique');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('options_hash');
        });
    }
};
