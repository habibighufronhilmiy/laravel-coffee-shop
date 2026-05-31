<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Transaksi;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransController extends Controller
{
    public function notification(Request $request): JsonResponse
    {
        $serverKey = config('midtrans.server_key');
        $input = $request->all();

        if (empty($input['order_id']) || empty($input['status_code']) || empty($input['gross_amount'])) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $signatureKey = hash('sha512', $input['order_id'] . $input['status_code'] . $input['gross_amount'] . $serverKey);

        if ($signatureKey !== ($input['signature_key'] ?? '')) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $invoice = $input['order_id'];
        $transaksi = Transaksi::where('invoice', $invoice)->first();

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $transactionStatus = $input['transaction_status'];
        $fraudStatus = $input['fraud_status'] ?? 'accept';

        if ($transaksi->status_pembayaran === 'lunas') {
            return response()->json(['message' => 'OK']);
        }

        DB::transaction(function () use ($transaksi, $transactionStatus, $fraudStatus) {
            if (in_array($transactionStatus, ['capture', 'settlement']) && $fraudStatus === 'accept') {
                $transaksi->where('status_pembayaran', '!=', 'lunas')
                    ->update(['status_pembayaran' => 'lunas', 'status_pesanan' => 'diproses']);
                $transaksi->user?->notify(new OrderStatusNotification($transaksi, 'Pembayaran berhasil! Pesanan sedang diproses.'));
            } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
                if (!in_array($transaksi->status_pembayaran, ['lunas', 'expired'])) {
                    $transaksi->update(['status_pembayaran' => 'gagal', 'status_pesanan' => 'dibatalkan']);
                    $this->restoreStock($transaksi);
                    $transaksi->restorePoin();
                    $transaksi->user?->notify(new OrderStatusNotification($transaksi, 'Pembayaran gagal.'));
                }
            } elseif ($transactionStatus === 'expire') {
                if (!in_array($transaksi->status_pembayaran, ['lunas', 'gagal'])) {
                    $transaksi->update(['status_pembayaran' => 'expired', 'status_pesanan' => 'dibatalkan']);
                    $this->restoreStock($transaksi);
                    $transaksi->restorePoin();
                    $transaksi->user?->notify(new OrderStatusNotification($transaksi, 'Waktu pembayaran habis. Pesanan dibatalkan.'));
                }
            }
        });

        return response()->json(['message' => 'OK']);
    }

    public function getClientKey(): JsonResponse
    {
        return response()->json([
            'client_key' => config('midtrans.client_key'),
        ]);
    }

    private function restoreStock(Transaksi $transaksi): void
    {
        $transaksi->loadMissing('detailTransaksis.menu', 'detailTransaksis.variant');
        foreach ($transaksi->detailTransaksis as $detail) {
            if ($detail->menu_variant_id && $detail->variant?->stok !== null) {
                MenuVariant::where('id', $detail->menu_variant_id)->increment('stok', $detail->jumlah);
            } else {
                Menu::where('id', $detail->menu_id)->increment('stok', $detail->jumlah);
            }
        }
    }
}
