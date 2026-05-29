<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'no_telp',
        'jam_buka',
        'jam_tutup',
        'foto',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
