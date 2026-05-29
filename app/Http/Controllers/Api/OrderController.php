<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\DetailTransaksi;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Transaksi::with(['detailTransaksis.menu', 'detailTransaksis.variant', 'outlet'])
            ->where('user_id', $request->user()->id);

        if ($request->filled('status')) {
            $query->where('status_pesanan', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhereHas('outlet', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $perPage = min((int) $request->input('per_page', 10), 50);
        $transaksis = $query->latest()->paginate($perPage);

        return response()->json($transaksis);
    }

    public function show(Request $request, Transaksi $transaksi): JsonResponse
    {
        if ($transaksi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Bukan pesanan Anda'], 403);
        }

        $transaksi->load(['detailTransaksis.menu', 'detailTransaksis.variant', 'outlet', 'voucherPakai.voucher']);
        return response()->json($transaksi);
    }

    public function cancel(Request $request, Transaksi $transaksi): JsonResponse
    {
        if ($transaksi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Bukan pesanan Anda'], 403);
        }

        if ($transaksi->status_pesanan !== 'pending') {
            return response()->json(['message' => 'Pesanan sudah diproses, tidak bisa dibatalkan'], 400);
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->update(['status_pesanan' => 'dibatalkan', 'status_pembayaran' => 'gagal']);

            $transaksi->loadMissing('detailTransaksis.menu', 'detailTransaksis.variant');
            foreach ($transaksi->detailTransaksis as $detail) {
                if ($detail->menu_variant_id && $detail->variant?->stok !== null) {
                    MenuVariant::where('id', $detail->menu_variant_id)->increment('stok', $detail->jumlah);
                } else {
                    Menu::where('id', $detail->menu_id)->increment('stok', $detail->jumlah);
                }
            }
        });

        return response()->json([
            'message' => 'Pesanan dibatalkan',
            'transaksi' => $transaksi->fresh()->load('detailTransaksis.menu', 'detailTransaksis.variant'),
        ]);
    }

    public function reorder(Request $request, Transaksi $transaksi): JsonResponse
    {
        if ($transaksi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Bukan pesanan Anda'], 403);
        }

        $details = $transaksi->detailTransaksis()->with('menu', 'variant')->get();

        if ($details->isEmpty()) {
            return response()->json(['message' => 'Tidak ada item untuk dipesan ulang'], 400);
        }

        DB::transaction(function () use ($request, $details) {
            foreach ($details as $detail) {
                $menu = $detail->menu;
                if (!$menu) continue;

                $variantId = $detail->menu_variant_id;
                $menuVariant = $detail->variant;

                $selectedOptions = $detail->selected_options ?? [];
                $optionsHash = $this->computeOptionsHash($selectedOptions);

                $userId = $request->user()->id;
                $harga = $menu->harga;

                if ($variantId && $menuVariant) {
                    $harga += $menuVariant->harga_tambahan;
                }

                foreach ($selectedOptions as $opt) {
                    $harga += $opt['harga_tambahan'] ?? 0;
                }

                $existing = CartItem::where('user_id', $userId)
                    ->where('menu_id', $menu->id)
                    ->where('menu_variant_id', $variantId)
                    ->where('options_hash', $optionsHash)
                    ->first();

                if ($existing) {
                    $existing->increment('jumlah', $detail->jumlah);
                    $existing->update([
                        'subtotal' => ($existing->harga) * ($existing->jumlah),
                    ]);
                } else {
                    CartItem::create([
                        'user_id' => $userId,
                        'menu_id' => $menu->id,
                        'menu_variant_id' => $variantId,
                        'options_hash' => $optionsHash,
                        'selected_options' => $selectedOptions,
                        'jumlah' => $detail->jumlah,
                        'harga' => $harga,
                        'subtotal' => $harga * $detail->jumlah,
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Item ditambahkan ke keranjang',
            'cart' => app(CartController::class)->getCartData($request),
        ]);
    }

    private function computeOptionsHash(array $selectedOptions): ?string
    {
        if (empty($selectedOptions)) {
            return null;
        }

        $normalized = collect($selectedOptions)
            ->sortBy('group_id')
            ->values()
            ->map(fn($o) => $o['group_id'] . '-' . $o['item_id'])
            ->implode('|');

        return md5($normalized);
    }
}
