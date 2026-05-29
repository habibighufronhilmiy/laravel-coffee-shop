<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'cart' => $this->getCartData($request),
            'total' => CartItem::where('user_id', $request->user()->id)->sum('subtotal'),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'menu_variant_id' => 'nullable|exists:menu_variants,id',
            'selected_options' => 'nullable|array',
            'selected_options.*.group_id' => 'required|exists:menu_option_groups,id',
            'selected_options.*.item_id' => 'required|exists:menu_option_group_items,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $variantId = $validated['menu_variant_id'] ?? null;
        $selectedOptions = $validated['selected_options'] ?? [];
        $menu = Menu::findOrFail($validated['menu_id']);

        $variant = null;
        if ($variantId) {
            $variant = MenuVariant::findOrFail($variantId);
        }

        $existing = $this->findExistingCartItem($request->user()->id, $menu->id, $variantId, $selectedOptions);

        $newJumlah = $validated['jumlah'] + ($existing?->jumlah ?? 0);

        $stokCek = $variant?->stok ?? $menu->stok;
        if ($stokCek < $newJumlah) {
            $label = $menu->nama_menu . ($variant ? ' (' . $variant->nama . ')' : '');
            return response()->json(['message' => "Stok {$label} tidak mencukupi (sisa: {$stokCek})."], 400);
        }

        $hargaTambahan = $this->hitungHargaTambahanOptions($selectedOptions);
        $harga = $menu->harga + ($variant?->harga_tambahan ?? 0) + $hargaTambahan;

        $optionsHash = $this->computeOptionsHash($selectedOptions);

        $cartData = [
            'user_id' => $request->user()->id,
            'menu_id' => $menu->id,
            'menu_variant_id' => $variantId,
            'options_hash' => $optionsHash,
            'selected_options' => $selectedOptions,
            'jumlah' => $newJumlah,
            'harga' => $harga,
            'subtotal' => $harga * $newJumlah,
        ];

        if ($existing) {
            $existing->update($cartData);
        } else {
            CartItem::create($cartData);
        }

        $label = $menu->nama_menu . ($variant ? ' (' . $variant->nama . ')' : '');

        try {
            $cartData = $this->getCartData($request);
        } catch (\Exception $e) {
            logger()->error('getCartData failed after add', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => "{$label} ditambahkan ke keranjang",
                'cart' => [],
            ]);
        }

        return response()->json([
            'message' => "{$label} ditambahkan ke keranjang",
            'cart' => $cartData,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:cart_items,id',
            'menu_id' => 'required|exists:menus,id',
            'menu_variant_id' => 'nullable|exists:menu_variants,id',
            'selected_options' => 'nullable|array',
            'selected_options.*.group_id' => 'required|exists:menu_option_groups,id',
            'selected_options.*.item_id' => 'required|exists:menu_option_group_items,id',
            'jumlah' => 'required|integer|min:0',
        ]);

        $variantId = $validated['menu_variant_id'] ?? null;
        $selectedOptions = $validated['selected_options'] ?? [];
        $userId = $request->user()->id;

        // If cart_item_id provided, edit that specific item in-place
        if (!empty($validated['id'])) {
            $item = CartItem::findOrFail($validated['id']);
            if ($item->user_id !== $userId) {
                return response()->json(['message' => 'Bukan item Anda'], 403);
            }

            if ($validated['jumlah'] == 0) {
                $item->delete();
                return response()->json([
                    'message' => 'Item dihapus dari keranjang',
                    'cart' => $this->getCartData($request),
                ]);
            }

            $menu = Menu::findOrFail($validated['menu_id']);
            $harga = $menu->harga;
            if ($variantId) {
                $variant = MenuVariant::findOrFail($variantId);
                $harga += $variant->harga_tambahan;
                $stokCek = $variant->stok ?? $menu->stok;
            } else {
                $stokCek = $menu->stok;
            }
            $harga += $this->hitungHargaTambahanOptions($selectedOptions);

            if ($stokCek < $validated['jumlah']) {
                return response()->json(['message' => "Stok {$menu->nama_menu} tidak mencukupi (sisa: {$stokCek})."], 400);
            }

            $optionsHash = $this->computeOptionsHash($selectedOptions);

            $item->update([
                'menu_variant_id' => $variantId,
                'options_hash' => $optionsHash,
                'selected_options' => $selectedOptions,
                'jumlah' => $validated['jumlah'],
                'harga' => $harga,
                'subtotal' => $harga * $validated['jumlah'],
            ]);

            return response()->json([
                'message' => 'Item diperbarui',
                'cart' => $this->getCartData($request),
            ]);
        }

        // Legacy: find by hash (quantity-only updates)
        $existing = $this->findExistingCartItem($userId, $validated['menu_id'], $variantId, $selectedOptions);

        if ($validated['jumlah'] == 0) {
            if ($existing) {
                $existing->delete();
            }
            return response()->json([
                'message' => 'Item dihapus dari keranjang',
                'cart' => $this->getCartData($request),
            ]);
        }

        $menu = Menu::findOrFail($validated['menu_id']);

        $harga = $menu->harga;
        if ($variantId) {
            $variant = MenuVariant::findOrFail($variantId);
            $harga += $variant->harga_tambahan;
            $stokCek = $variant->stok ?? $menu->stok;
        } else {
            $stokCek = $menu->stok;
        }
        $harga += $this->hitungHargaTambahanOptions($selectedOptions);

        if ($stokCek < $validated['jumlah']) {
            return response()->json(['message' => "Stok {$menu->nama_menu} tidak mencukupi (sisa: {$stokCek})."], 400);
        }

        $optionsHash = $this->computeOptionsHash($selectedOptions);

        $cartData = [
            'user_id' => $userId,
            'menu_id' => $menu->id,
            'menu_variant_id' => $variantId,
            'options_hash' => $optionsHash,
            'selected_options' => $selectedOptions,
            'jumlah' => $validated['jumlah'],
            'harga' => $harga,
            'subtotal' => $harga * $validated['jumlah'],
        ];

        if ($existing) {
            $existing->update($cartData);
        } else {
            CartItem::create($cartData);
        }

        return response()->json([
            'message' => 'Keranjang diperbarui',
            'cart' => $this->getCartData($request),
        ]);
    }

    public function remove(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Bukan item Anda'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item dihapus dari keranjang',
            'cart' => $this->getCartData($request),
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Keranjang dikosongkan', 'cart' => []]);
    }

    public function getCartData(Request $request): array
    {
        $optionItemIds = collect();
        $items = CartItem::with('menu', 'variant')
            ->where('user_id', $request->user()->id)
            ->get();

        foreach ($items as $item) {
            foreach (($item->selected_options ?: []) as $opt) {
                $optionItemIds->push($opt['item_id']);
            }
        }

        $optionItems = \App\Models\MenuOptionGroupItem::whereIn('id', $optionItemIds->unique())
            ->with('group')
            ->get()
            ->keyBy('id');

        return $items->map(fn($item) => [
            'id' => $item->id,
            'menu_id' => $item->menu_id,
            'menu_variant_id' => $item->menu_variant_id,
            'selected_options' => collect($item->selected_options ?: [])->map(fn($opt) => [
                'group_id' => $opt['group_id'],
                'group_name' => $optionItems->get($opt['item_id'])?->group?->nama ?? '',
                'item_id' => $opt['item_id'],
                'item_name' => $optionItems->get($opt['item_id'])?->nama ?? '',
                'harga_tambahan' => $optionItems->get($opt['item_id'])?->harga_tambahan ?? 0,
            ])->toArray(),
            'nama_menu' => $item->menu->nama_menu,
            'nama_varian' => $item->variant?->nama,
            'harga_dasar' => $item->menu->harga,
            'harga' => $item->harga,
            'jumlah' => $item->jumlah,
            'subtotal' => $item->subtotal,
            'stok' => $item->variant?->stok ?? $item->menu->stok,
            'foto_menu' => $item->menu->foto_menu,
        ])->toArray();
    }

    private function findExistingCartItem(int $userId, int $menuId, ?int $variantId, array $selectedOptions): ?CartItem
    {
        return CartItem::where('user_id', $userId)
            ->where('menu_id', $menuId)
            ->where('menu_variant_id', $variantId)
            ->where('options_hash', $this->computeOptionsHash($selectedOptions))
            ->first();
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

    private function hitungHargaTambahanOptions(array $selectedOptions): int
    {
        if (empty($selectedOptions)) {
            return 0;
        }

        $itemIds = collect($selectedOptions)->pluck('item_id')->toArray();
        $items = \App\Models\MenuOptionGroupItem::whereIn('id', $itemIds)->get()->keyBy('id');

        $total = 0;
        foreach ($selectedOptions as $opt) {
            $item = $items->get($opt['item_id']);
            if ($item) {
                $total += $item->harga_tambahan;
            }
        }

        return $total;
    }
}
