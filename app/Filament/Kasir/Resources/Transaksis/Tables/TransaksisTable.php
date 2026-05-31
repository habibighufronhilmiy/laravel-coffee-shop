<?php

namespace App\Filament\Kasir\Resources\Transaksis\Tables;

use App\Filament\Kasir\Resources\Transaksis\TransaksiResource;
use App\Http\Controllers\Api\LoyaltyController;
use App\Models\LoyaltyPoint;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TransaksisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice')->sortable()->searchable()->label('Invoice'),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('outlet.nama')->label('Outlet')->searchable()->placeholder('-'),
                TextColumn::make('no_meja')->label('Meja')->searchable(),
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
                    Action::make('proses')
                        ->label('Proses')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->visible(fn(Transaksi $record): bool => $record->status_pesanan === 'pending')
                        ->action(function (Transaksi $record) {
                            $record->update(['status_pesanan' => 'diproses']);
                            notify('Sukses', 'Pesanan sedang diproses')
                                ->success()
                                ->send();
                        }),
                    Action::make('siap')
                        ->label('Siap')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Transaksi $record): bool =>
                            $record->status_pesanan === 'diproses' && ($record->tipe_pengambilan ?? '') !== 'delivery')
                        ->action(function (Transaksi $record) {
                            $record->update(['status_pesanan' => 'selesai']);
                            $earned = LoyaltyPoint::where('transaksi_id', $record->id)
                                ->where('type', 'earn')->exists();
                            if (!$earned) {
                                LoyaltyController::earnForOrder($record);
                            }
                            notify('Sukses', 'Pesanan siap diambil')
                                ->success()
                                ->send();
                        }),
                    Action::make('antar')
                        ->label('Antar')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->visible(fn(Transaksi $record): bool =>
                            $record->status_pesanan === 'diproses' && ($record->tipe_pengambilan ?? '') === 'delivery')
                        ->action(function (Transaksi $record) {
                            $record->update(['status_pesanan' => 'diantar']);
                            notify('Sukses', 'Pesanan sedang diantar')
                                ->success()
                                ->send();
                        }),
                    Action::make('selesai_antar')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn(Transaksi $record): bool => $record->status_pesanan === 'diantar')
                        ->action(function (Transaksi $record) {
                            $record->update(['status_pesanan' => 'selesai']);
                            $earned = LoyaltyPoint::where('transaksi_id', $record->id)
                                ->where('type', 'earn')->exists();
                            if (!$earned) {
                                LoyaltyController::earnForOrder($record);
                            }
                            notify('Sukses', 'Pesanan selesai')
                                ->success()
                                ->send();
                        }),
                    Action::make('batalkan')
                        ->label('Batalkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(Transaksi $record): bool =>
                            $record->status_pesanan === 'pending' || $record->status_pesanan === 'diproses')
                        ->requiresConfirmation()
                        ->action(function (Transaksi $record) {
                            DB::transaction(function () use ($record) {
                                $record->update(['status_pesanan' => 'dibatalkan']);

                                $record->loadMissing('detailTransaksis.menu', 'detailTransaksis.variant');
                                foreach ($record->detailTransaksis as $detail) {
                                    if ($detail->menu_variant_id && $detail->variant?->stok !== null) {
                                        MenuVariant::where('id', $detail->menu_variant_id)->increment('stok', $detail->jumlah);
                                    } else {
                                        Menu::where('id', $detail->menu_id)->increment('stok', $detail->jumlah);
                                    }
                                }

                                $record->restorePoin();
                            });

                            notify('Sukses', 'Pesanan dibatalkan, stok dan poin dikembalikan')
                                ->success()
                                ->send();
                        }),
                    Action::make('print')
                        ->label('Cetak Struk')
                        ->icon('heroicon-o-printer')
                        ->url(fn($record): string => TransaksiResource::getUrl('print', ['record' => $record->id])),
                    Action::make('download_pdf')
                        ->label('Download PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Transaksi $record) {
                            $record->load(['detailTransaksis.menu', 'user', 'kasir', 'outlet']);
                            $pdf = Pdf::loadView('filament.kasir.pages.print-struk-pdf', [
                                'transaksi' => $record,
                            ]);
                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                "struk-{$record->id}.pdf"
                            );
                        }),
                ]),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
