<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Transaksi;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:cancel-expired-orders')]
#[Description('Batalkan otomatis pesanan pending yang sudah lebih dari 24 jam')]
class CancelExpiredOrders extends Command
{
    public function handle()
    {
        $transaksis = Transaksi::where('status_pesanan', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        $count = 0;

        foreach ($transaksis as $transaksi) {
            DB::transaction(function () use ($transaksi, &$count) {
                $transaksi->update(['status_pesanan' => 'dibatalkan', 'status_pembayaran' => 'expired']);

                $transaksi->loadMissing('detailTransaksis.menu', 'detailTransaksis.variant');
                foreach ($transaksi->detailTransaksis as $detail) {
                    if ($detail->menu_variant_id && $detail->variant?->stok !== null) {
                        MenuVariant::where('id', $detail->menu_variant_id)->increment('stok', $detail->jumlah);
                    } else {
                        Menu::where('id', $detail->menu_id)->increment('stok', $detail->jumlah);
                    }
                }

                $count++;
            });
        }

        $this->info("{$count} pesanan expired berhasil dibatalkan dan stok dikembalikan.");
    }
}
