<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp' . number_format(
                Transaksi::where('status_pembayaran', 'lunas')->where('created_at', '>=', $today)->sum('total_harga'),
                0, ',', '.'
            )),
            Stat::make('Pesanan Hari Ini', Transaksi::where('created_at', '>=', $today)->count()),
            Stat::make('Pesanan Pending', Transaksi::where('status_pesanan', 'pending')->count()),
            Stat::make('Total Pelanggan', User::where('role', 'customer')->count()),
            Stat::make('Menu Terjual Hari Ini', DetailTransaksi::whereHas('transaksi', fn($q) => $q->where('created_at', '>=', $today))->sum('jumlah')),
        ];
    }
}
