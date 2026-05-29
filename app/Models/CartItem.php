<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'user_id', 'menu_id', 'menu_variant_id', 'options_hash', 'selected_options', 'jumlah', 'harga', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'selected_options' => 'array',
            'jumlah' => 'integer',
            'harga' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
