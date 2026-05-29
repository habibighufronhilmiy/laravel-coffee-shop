<?php

namespace App\Filament\Kasir\Resources\Transaksis\Pages;

use App\Filament\Kasir\Resources\Transaksis\TransaksiResource;
use App\Models\Menu;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach ($data['detailTransaksis'] ?? [] as $item) {
            $menu = Menu::find($item['menu_id']);
            if ($menu && $menu->stok < ($item['jumlah'] ?? 1)) {
                Notification::make()
                    ->title("Stok {$menu->nama_menu} tidak mencukupi (sisa: {$menu->stok})")
                    ->danger()
                    ->send();
                $this->halt();
            }
        }

        $data['total_harga'] = collect($data['detailTransaksis'] ?? [])->sum('subtotal');
        $data['tipe_pemesanan'] = 'kasir';
        $data['tipe_pengambilan'] = 'ditempat';
        $data['kasir_id'] = auth()->id();
        $data['metode_pembayaran'] = 'cash';
        $data['status_pembayaran'] = 'lunas';

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->update([
            'total_harga' => $this->record->detailTransaksis->sum('subtotal'),
        ]);

        foreach ($this->record->detailTransaksis as $detail) {
            Menu::where('id', $detail->menu_id)->decrement('stok', $detail->jumlah);
        }
    }
}
