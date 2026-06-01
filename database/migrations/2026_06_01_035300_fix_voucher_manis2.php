<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('vouchers')
            ->where('kode', 'MANIS2')
            ->update([
                'maks_diskon' => null,
                'berlaku_mulai' => null,
            ]);
    }

    public function down(): void
    {
    }
};
