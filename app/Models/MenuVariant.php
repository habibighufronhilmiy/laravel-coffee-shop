<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuVariant extends Model
{
    protected $fillable = [
        'menu_id',
        'nama',
        'harga_tambahan',
        'stok',
    ];

    protected function casts(): array
    {
        return [
            'harga_tambahan' => 'integer',
            'stok' => 'integer',
        ];
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
