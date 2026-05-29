<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_option_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_option_group_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->integer('harga_tambahan')->default(0);
            $table->integer('stok')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_option_group_items');
    }
};
