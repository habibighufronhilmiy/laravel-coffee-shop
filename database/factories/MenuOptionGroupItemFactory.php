<?php

namespace Database\Factories;

use App\Models\MenuOptionGroup;
use App\Models\MenuOptionGroupItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuOptionGroupItemFactory extends Factory
{
    protected $model = MenuOptionGroupItem::class;

    public function definition(): array
    {
        return [
            'menu_option_group_id' => MenuOptionGroup::factory(),
            'nama' => fake()->randomElement(['Boba', 'Grass Jelly', 'Oreo', 'Ice Cream']),
            'harga_tambahan' => fake()->numberBetween(0, 5000),
            'urutan' => fake()->numberBetween(1, 5),
            'is_default' => false,
        ];
    }
}
