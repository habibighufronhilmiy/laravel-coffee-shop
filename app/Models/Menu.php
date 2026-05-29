<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kategori_id',
        'nama_menu',
        'deskripsi',
        'harga',
        'stok',
        'foto_menu',
    ];

    protected $with = ['kategori'];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
        ];
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function variants()
    {
        return $this->hasMany(MenuVariant::class);
    }

    public function optionGroups()
    {
        return $this->hasMany(MenuOptionGroup::class)->orderBy('urutan');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function ratingsCount()
    {
        return $this->ratings()->count();
    }
}
