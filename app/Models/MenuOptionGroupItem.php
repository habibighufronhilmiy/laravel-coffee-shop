<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuOptionGroupItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_option_group_id',
        'nama',
        'harga_tambahan',
        'stok',
        'urutan',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'harga_tambahan' => 'integer',
            'stok' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    public function group()
    {
        return $this->belongsTo(MenuOptionGroup::class, 'menu_option_group_id');
    }
}

