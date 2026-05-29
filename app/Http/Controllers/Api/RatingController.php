<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ratings = Rating::with('user:id,name,avatar')
            ->where('menu_id', $request->menu_id)
            ->latest()
            ->get();

        return response()->json($ratings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'transaksi_id' => 'nullable|exists:transaksis,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $userId = $request->user()->id;

        if (!empty($validated['transaksi_id'])) {
            $transaksi = \App\Models\Transaksi::where('id', $validated['transaksi_id'])
                ->where('user_id', $userId)
                ->first();

            if (!$transaksi) {
                return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
            }

            $hasMenu = \App\Models\DetailTransaksi::where('transaksi_id', $transaksi->id)
                ->where('menu_id', $validated['menu_id'])
                ->exists();

            if (!$hasMenu) {
                return response()->json(['message' => 'Menu tidak ada dalam transaksi ini.'], 400);
            }
        }

        $existing = Rating::where('user_id', $userId)
            ->where('menu_id', $validated['menu_id'])
            ->where('transaksi_id', $validated['transaksi_id'] ?? null)
            ->first();

        if ($existing) {
            $existing->update($validated);
            return response()->json(['message' => 'Rating berhasil diperbarui', 'rating' => $existing]);
        }

        $validated['user_id'] = $userId;
        $rating = Rating::create($validated);

        return response()->json(['message' => 'Rating berhasil dikirim', 'rating' => $rating], 201);
    }
}
