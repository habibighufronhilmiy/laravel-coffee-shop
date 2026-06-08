<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'menu_id' => Menu::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'review' => fake()->optional(0.7)->sentence(),
        ];
    }
}
