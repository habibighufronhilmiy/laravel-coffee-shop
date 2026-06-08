<?php

namespace Database\Factories;

use App\Models\Outlet;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiFactory extends Factory
{
    protected $model = Transaksi::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'outlet_id' => Outlet::factory(),
            'total_harga' => fake()->numberBetween(25000, 150000),
            'tipe_pemesanan' => 'aplikasi',
            'tipe_pengambilan' => 'ditempat',
            'metode_pembayaran' => 'cash',
            'status_pembayaran' => 'lunas',
            'status_pesanan' => 'diproses',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status_pembayaran' => 'belum_bayar',
            'status_pesanan' => 'pending',
        ]);
    }

    public function delivery(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipe_pengambilan' => 'delivery',
            'alamat_pengiriman' => fake()->address(),
            'ongkir' => fake()->numberBetween(5000, 20000),
        ]);
    }
}
