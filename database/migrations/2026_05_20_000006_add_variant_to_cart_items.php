<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['menu_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'menu_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('menu_variant_id')->nullable()->after('menu_id')
                ->constrained('menu_variants')->nullOnDelete();

            $table->unique(['user_id', 'menu_id', 'menu_variant_id']);

            $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['menu_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'menu_id', 'menu_variant_id']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['user_id', 'menu_id']);

            $table->dropConstrainedForeignId('menu_variant_id');

            $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
