<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuOptionGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_id',
        'nama',
        'tipe',
        'urutan',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function items()
    {
        return $this->hasMany(MenuOptionGroupItem::class, 'menu_option_group_id')->orderBy('urutan');
    }
}

