<?php

namespace Database\Factories;

use App\Models\CartItem;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        $menu = Menu::factory()->create();
        $jumlah = fake()->numberBetween(1, 5);

        return [
            'user_id' => User::factory(),
            'menu_id' => $menu->id,
            'jumlah' => $jumlah,
            'harga' => $menu->harga,
            'subtotal' => $menu->harga * $jumlah,
        ];
    }
}
