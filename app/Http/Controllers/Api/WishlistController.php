<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = $request->user()->wishlists()->with('menu.kategori')->latest()->get();
        return response()->json($items);
    }

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
        ]);

        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('menu_id', $validated['menu_id'])
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['message' => 'Dihapus dari favorit', 'wishlisted' => false]);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'menu_id' => $validated['menu_id'],
        ]);

        return response()->json(['message' => 'Ditambahkan ke favorit', 'wishlisted' => true], 201);
    }
}
