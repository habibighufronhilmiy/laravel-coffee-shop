<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nama_kategori',
        'icon',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}

