<?php

namespace App\Filament\Kasir\Resources\Transaksis\Pages;

use App\Filament\Kasir\Resources\Transaksis\TransaksiResource;
use App\Http\Controllers\Api\LoyaltyController;
use App\Models\LoyaltyPoint;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransaksi extends EditRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Struk')
                ->icon('heroicon-o-printer')
                ->url(fn(): string => TransaksiResource::getUrl('print', ['record' => $this->record->id])),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $correctTotal = $this->record->detailTransaksis->sum('subtotal');

        $diskonPoin = (int) $this->record->diskon_poin;
        $ongkir = (int) $this->record->ongkir;
        $correctTotal = max($correctTotal - $diskonPoin + $ongkir, 0);

        $this->record->update(['total_harga' => $correctTotal]);

        if ($this->record->status_pesanan === 'selesai') {
            $earned = LoyaltyPoint::where('transaksi_id', $this->record->id)
                ->where('type', 'earn')->exists();
            if (!$earned) {
                LoyaltyController::earnForOrder($this->record);
            }
        }
    }
}
