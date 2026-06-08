<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuVariant extends Model
{
    use HasFactory;
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

