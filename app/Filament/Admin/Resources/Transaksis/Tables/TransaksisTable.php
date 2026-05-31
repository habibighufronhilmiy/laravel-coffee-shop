<?php

namespace App\Filament\Admin\Resources\Transaksis\Tables;

use App\Models\Transaksi;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransaksisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice')->sortable()->searchable()->label('Invoice'),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('outlet.nama')->label('Outlet')->searchable()->placeholder('-'),
                TextColumn::make('no_meja')->label('Meja')->placeholder('-'),
                TextColumn::make('tipe_pengambilan')
                    ->badge()
                    ->label('Ambil')
                    ->color(fn(string $state): string => match ($state) {
                        'pickup' => 'info',
                        'ditempat' => 'success',
                        'delivery' => 'warning',
                    }),
                TextColumn::make('total_harga')->money('idr')->sortable(),
                TextColumn::make('tipe_pemesanan')
                    ->badge()
                    ->label('Tipe')
                    ->color(fn(string $state): string => match ($state) {
                        'kasir' => 'warning',
                        'aplikasi' => 'info',
                    }),
                TextColumn::make('metode_pembayaran')
                    ->badge()
                    ->label('Bayar')
                    ->color(fn(string $state): string => match ($state) {
                        'cash' => 'success',
                        'midtrans' => 'info',
                    }),
                TextColumn::make('status_pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'belum_bayar' => 'warning',
                        'lunas' => 'success',
                        'expired' => 'danger',
                        'gagal' => 'danger',
                    }),
                TextColumn::make('status_pesanan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'diproses' => 'info',
                        'diantar' => 'warning',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Waktu'),
            ])
            ->filters([
                SelectFilter::make('status_pesanan')
                    ->options([
                        'pending' => 'Pending',
                        'diproses' => 'Diproses',
                        'diantar' => 'Diantar',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                SelectFilter::make('status_pembayaran')
                    ->options([
                        'belum_bayar' => 'Belum Dibayar',
                        'lunas' => 'Lunas',
                        'expired' => 'Expired',
                        'gagal' => 'Gagal',
                    ]),
                SelectFilter::make('metode_pembayaran')
                    ->options([
                        'cash' => 'Cash',
                        'midtrans' => 'Midtrans',
                    ]),
                SelectFilter::make('tipe_pemesanan')
                    ->options([
                        'kasir' => 'Kasir',
                        'aplikasi' => 'Aplikasi',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    Action::make('view_detail')
                        ->label('Detail')
                        ->icon('heroicon-o-eye')
                        ->action(function (Transaksi $record) {
                            $record->load(['detailTransaksis.menu', 'user', 'kasir', 'outlet', 'voucherPakai.voucher']);
                            $items = $record->detailTransaksis->map(fn($d) => [
                                'menu' => $d->menu?->nama_menu ?? 'Menu #' . $d->menu_id,
                                'jumlah' => $d->jumlah,
                                'subtotal' => $d->subtotal,
                            ]);
                            $detail = "Invoice: {$record->invoice}\n";
                            $detail .= "Customer: {$record->user?->name}\n";
                            $detail .= "Outlet: {$record->outlet?->nama}\n";
                            $detail .= "Tipe: {$record->tipe_pemesanan} / {$record->tipe_pengambilan}\n";
                            $detail .= "Bayar: {$record->metode_pembayaran}\n";
                            $detail .= "Status Bayar: {$record->status_pembayaran}\n";
                            $detail .= "Status Pesanan: {$record->status_pesanan}\n";
                            $detail .= "Total: Rp" . number_format($record->total_harga, 0, ',', '.') . "\n\n";
                            $detail .= "--- Items ---\n";
                            foreach ($items as $item) {
                                $detail .= "{$item['menu']} x {$item['jumlah']} = Rp" . number_format($item['subtotal'], 0, ',', '.') . "\n";
                            }
                            if ($record->ongkir > 0) {
                                $detail .= "Ongkir: Rp" . number_format($record->ongkir, 0, ',', '.') . "\n";
                            }
                            if ($record->voucherPakai) {
                                $detail .= "Diskon Voucher: Rp" . number_format($record->voucherPakai->diskon, 0, ',', '.') . "\n";
                            }
                            $detail .= "\nDibuat: {$record->created_at}";
                            notify('Detail Transaksi')->info($detail);
                        }),
                ]),
            ])
            ->toolbarActions([]);
    }
}
