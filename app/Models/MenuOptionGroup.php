<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuOptionGroup extends Model
{
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
