<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('alamat', 255);
            $table->string('latitude', 20);
            $table->string('longitude', 20);
            $table->string('no_telp', 20)->nullable();
            $table->string('jam_buka', 10)->nullable();
            $table->string('jam_tutup', 10)->nullable();
            $table->text('foto')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
