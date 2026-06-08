<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $menus = Menu::with('kategori', 'variants', 'optionGroups.items')->get();

        $wishlistedIds = [];
        $orderCounts = [];
        if ($request->user()) {
            $userId = $request->user()->id;
            $wishlistedIds = Wishlist::where('user_id', $userId)
                ->whereIn('menu_id', $menus->pluck('id'))
                ->pluck('menu_id')
                ->toArray();

            $orderCounts = DetailTransaksi::whereHas('transaksi', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->whereIn('status_pesanan', ['selesai', 'diantar', 'diproses']);
            })
                ->select('menu_id', DB::raw('SUM(jumlah) as total'))
                ->groupBy('menu_id')
                ->pluck('total', 'menu_id');
        }

        $menus->each(function ($menu) use ($wishlistedIds, $orderCounts) {
            $menu->wishlisted = in_array($menu->id, $wishlistedIds);
            $menu->order_count = (int) ($orderCounts[$menu->id] ?? 0);
            $menu->average_rating = $menu->averageRating();
            $menu->ratings_count = $menu->ratingsCount();
        });

        return response()->json($menus);
    }

    public function show(Request $request, Menu $menu): JsonResponse
    {
        $menu->load('kategori', 'variants', 'optionGroups.items');
        $menu->average_rating = $menu->averageRating();
        $menu->ratings_count = $menu->ratingsCount();

        if ($request->user()) {
            $menu->wishlisted = Wishlist::where('user_id', $request->user()->id)
                ->where('menu_id', $menu->id)
                ->exists();
        } else {
            $menu->wishlisted = false;
        }

        return response()->json($menu);
    }

    public function kategoris(): JsonResponse
    {
        $kategoris = Kategori::with('menus')->get();
        return response()->json($kategoris);
    }

    public function banners(): JsonResponse
    {
        $banners = Banner::where('aktif', true)
            ->orderBy('urutan')
            ->get();

        return response()->json($banners);
    }
}
