<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'kategori_id' => Kategori::factory(),
            'nama_menu' => fake()->words(2, true),
            'deskripsi' => fake()->sentence(),
            'harga' => fake()->numberBetween(15000, 50000),
            'stok' => fake()->numberBetween(5, 100),
        ];
    }

    public function habis(): static
    {
        return $this->state(fn(array $attributes) => ['stok' => 0]);
    }
}
