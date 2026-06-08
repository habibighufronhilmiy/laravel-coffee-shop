<?php

namespace Database\Factories;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutletFactory extends Factory
{
    protected $model = Outlet::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->company() . ' Coffee',
            'alamat' => fake()->address(),
            'latitude' => fake()->latitude(-7, -5),
            'longitude' => fake()->longitude(106, 112),
            'no_telp' => fake()->phoneNumber(),
            'jam_buka' => '08:00',
            'jam_tutup' => '22:00',
            'aktif' => true,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn(array $attributes) => ['aktif' => false]);
    }
}
