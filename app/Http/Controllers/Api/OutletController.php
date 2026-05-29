<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index(): JsonResponse
    {
        $outlets = Outlet::aktif()->get();

        return response()->json($outlets);
    }

    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;

        $outlets = Outlet::aktif()->get()->map(function ($outlet) use ($lat, $lng) {
            $outlet->jarak_km = $this->hitungJarak($lat, $lng, (float) $outlet->latitude, (float) $outlet->longitude);
            return $outlet;
        })->sortBy('jarak_km')->values();

        return response()->json($outlets);
    }

    public function show(Outlet $outlet): JsonResponse
    {
        return response()->json($outlet);
    }

    private function hitungJarak(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
