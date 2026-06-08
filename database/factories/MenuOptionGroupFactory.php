<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\MenuOptionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuOptionGroupFactory extends Factory
{
    protected $model = MenuOptionGroup::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'nama' => fake()->randomElement(['Topping', 'Milk', 'Sweetener']),
            'tipe' => 'single',
            'urutan' => fake()->numberBetween(1, 5),
        ];
    }
}
