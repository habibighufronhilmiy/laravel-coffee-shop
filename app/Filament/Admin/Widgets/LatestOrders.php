<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaksi;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaksi::latest()->limit(10))
            ->columns([
                TextColumn::make('id')->label('Invoice'),
                TextColumn::make('user.name')->label('Customer'),
                TextColumn::make('total_harga')->money('idr'),
                TextColumn::make('status_pembayaran')->badge(),
                TextColumn::make('status_pesanan')->badge(),
                TextColumn::make('created_at')->dateTime()->label('Waktu'),
            ]);
    }
}
