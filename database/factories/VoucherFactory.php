<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        return [
            'kode' => fake()->unique()->bothify('VCH-????-####'),
            'nama' => fake()->words(3, true),
            'tipe' => 'persen',
            'nilai' => 10,
            'min_belanja' => 50000,
            'maks_diskon' => 20000,
            'kuota' => 100,
            'terpakai' => 0,
            'berlaku_mulai' => now()->subDay(),
            'berlaku_sampai' => now()->addMonth(),
            'aktif' => true,
        ];
    }

    public function nominal(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipe' => 'nominal',
            'nilai' => fake()->numberBetween(5000, 25000),
        ]);
    }

    public function habisKuota(): static
    {
        return $this->state(fn(array $attributes) => [
            'kuota' => 0,
            'terpakai' => 10,
        ]);
    }
}
