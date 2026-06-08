<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(3),
            'deskripsi' => fake()->sentence(),
            'gambar' => 'banners/test.png',
            'link' => null,
            'urutan' => fake()->numberBetween(1, 10),
            'aktif' => true,
        ];
    }
}
