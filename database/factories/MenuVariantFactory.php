<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuVariantFactory extends Factory
{
    protected $model = MenuVariant::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'nama' => fake()->randomElement(['Small', 'Medium', 'Large']),
            'harga_tambahan' => fake()->numberBetween(0, 10000),
            'stok' => fake()->numberBetween(10, 50),
        ];
    }
}
