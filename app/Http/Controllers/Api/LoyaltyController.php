<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    const POINT_PER_RUPIAH = 1000;
    const REDEEM_RATE = 100;
    const REDEEM_VALUE = 10000;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $history = LoyaltyPoint::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'balance' => (int) $user->poin,
            'history' => $history,
            'redeem_rate' => self::REDEEM_RATE,
            'redeem_value' => self::REDEEM_VALUE,
        ]);
    }

    public static function earnForOrder(Transaksi $transaksi): void
    {
        $points = (int) floor($transaksi->total_harga / self::POINT_PER_RUPIAH);
        if ($points <= 0) return;

        LoyaltyPoint::create([
            'user_id' => $transaksi->user_id,
            'points' => $points,
            'type' => 'earn',
            'description' => "Poin dari pesanan #{$transaksi->invoice}",
            'transaksi_id' => $transaksi->id,
        ]);

        $transaksi->user->increment('poin', $points);
    }

    public function redeem(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $points = $validated['points'];

        if ($points > $user->poin) {
            return response()->json([
                'message' => 'Poin tidak mencukupi. Saldo Anda: ' . $user->poin,
            ], 400);
        }

        $nilaiPerPoin = (int) (self::REDEEM_VALUE / self::REDEEM_RATE);
        $discount = $points * $nilaiPerPoin;

        return response()->json([
            'discount' => $discount,
            'points_used' => $points,
            'message' => "Diskon Rp " . number_format($discount, 0, ',', '.') . " dengan {$points} poin",
        ]);
    }
}
