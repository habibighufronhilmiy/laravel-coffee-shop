<?php

namespace App\Filament\Kasir\Resources\Transaksis\Schemas;

use App\Models\Menu;
use App\Models\Transaksi;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransaksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('outlet_id')
                    ->label('Outlet')
                    ->relationship('outlet', 'nama')
                    ->required(),
                Select::make('tipe_pengambilan')
                    ->label('Tipe Pengambilan')
                    ->options([
                        'ditempat' => 'Makan di Tempat',
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                    ])
                    ->default('ditempat')
                    ->live(),
                TextInput::make('no_meja')
                    ->label('Nomor Meja')
                    ->required()
                    ->maxLength(10)
                    ->visible(fn ($get) => $get('tipe_pengambilan') === 'ditempat'),
                Repeater::make('detailTransaksis')
                    ->relationship()
                    ->schema([
                        Select::make('menu_id')
                            ->label('Menu')
                            ->options(fn () => Menu::with('kategori')->get()->groupBy(fn ($menu) => $menu->kategori?->nama_kategori ?? 'Tanpa Kategori')->map(fn ($group) => $group->pluck('nama_menu', 'id')))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $menu = Menu::find($state);
                                $harga = $menu ? $menu->harga : 0;
                                $jumlah = (int) ($get('jumlah') ?: 1);
                                $set('subtotal', $harga * $jumlah);
                                $items = $get('../../detailTransaksis') ?? [];
                                $total = collect($items)->sum('subtotal');
                                $set('../../total_harga', $total);
                            }),
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $menu = Menu::find($get('menu_id'));
                                $harga = $menu ? $menu->harga : 0;
                                $set('subtotal', $harga * (int) ($state ?: 0));
                                $items = $get('../../detailTransaksis') ?? [];
                                $total = collect($items)->sum('subtotal');
                                $set('../../total_harga', $total);
                            }),
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Rp ')
                            ->readOnly()
                            ->dehydrated(),
                    ])
                    ->columns(3)
                    ->addActionLabel('Tambah Menu')
                    ->reorderable(false)
                    ->collapsible(false),
                TextInput::make('total_harga')
                    ->label('Total Harga')
                    ->numeric()
                    ->prefix('Rp ')
                    ->readOnly()
                    ->dehydrated(false),
                Hidden::make('metode_pembayaran')->default('cash'),
                Hidden::make('status_pembayaran')->default('lunas'),
                Select::make('status_pesanan')
                    ->options([
                        'pending' => 'Pending',
                        'diproses' => 'Diproses',
                        'diantar' => 'Diantar',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->required()
                    ->visible(fn(?Transaksi $record): bool => $record === null || $record->status_pesanan !== 'selesai'),
            ]);
    }
}
