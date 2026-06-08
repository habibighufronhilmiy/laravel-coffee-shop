<?php

namespace Database\Factories;

use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyPointFactory extends Factory
{
    protected $model = LoyaltyPoint::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'points' => fake()->numberBetween(10, 500),
            'type' => 'earn',
            'description' => fake()->sentence(3),
        ];
    }

    public function redeem(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'redeem',
            'points' => fake()->numberBetween(-500, -10),
        ]);
    }
}
