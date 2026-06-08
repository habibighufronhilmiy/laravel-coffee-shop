<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Rumah', 'Kantor', 'Kost']),
            'alamat' => fake()->address(),
            'penerima' => fake()->name(),
            'no_telp_penerima' => fake()->phoneNumber(),
            'is_default' => false,
        ];
    }

    public function utama(): static
    {
        return $this->state(fn(array $attributes) => ['is_default' => true]);
    }
}
