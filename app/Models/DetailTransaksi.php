<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $fillable = [
        'transaksi_id',
        'menu_id',
        'menu_variant_id',
        'selected_options',
        'jumlah',
        'subtotal',
    ];

    protected $with = ['menu', 'variant'];

    protected function casts(): array
    {
        return [
            'selected_options' => 'array',
            'jumlah' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function variant()
    {
        return $this->belongsTo(MenuVariant::class, 'menu_variant_id');
    }
}
