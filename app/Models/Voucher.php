<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode', 'nama', 'tipe', 'nilai', 'min_belanja', 'maks_diskon',
        'kuota', 'terpakai', 'berlaku_mulai', 'berlaku_sampai', 'aktif',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'min_belanja' => 'integer',
            'maks_diskon' => 'integer',
            'kuota' => 'integer',
            'terpakai' => 'integer',
            'berlaku_mulai' => 'datetime',
            'berlaku_sampai' => 'datetime',
            'aktif' => 'boolean',
        ];
    }

    public function pemakaian()
    {
        return $this->hasMany(VoucherPakai::class);
    }

    public function isValid(int $totalBelanja = 0): bool
    {
        if (!$this->aktif) return false;
        if ($this->kuota && $this->terpakai >= $this->kuota) return false;
        if ($this->berlaku_mulai && now()->lt($this->berlaku_mulai)) return false;
        if ($this->berlaku_sampai && now()->gt($this->berlaku_sampai)) return false;
        if ($totalBelanja < $this->min_belanja) return false;
        return true;
    }

    public function hitungDiskon(int $totalBelanja): int
    {
        $diskon = $this->tipe === 'persen'
            ? (int) ($totalBelanja * $this->nilai / 100)
            : $this->nilai;

        if ($this->maks_diskon && $diskon > $this->maks_diskon) {
            $diskon = $this->maks_diskon;
        }

        return min($diskon, $totalBelanja);
    }
}
