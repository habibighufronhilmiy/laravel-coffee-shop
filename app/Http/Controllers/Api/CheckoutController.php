<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\DetailTransaksi;
use App\Models\LoyaltyPoint;
use App\Models\Menu;
use App\Models\MenuOptionGroupItem;
use App\Models\MenuVariant;
use App\Models\Outlet;
use App\Models\Transaksi;
use App\Models\Voucher;
use App\Models\VoucherPakai;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function process(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id,aktif,1',
            'tipe_pengambilan' => 'required|in:ditempat,pickup,delivery',
            'no_meja' => 'nullable|string|max:10',
            'metode_pembayaran' => 'required|in:cash,midtrans',
            'kode_voucher' => 'nullable|string|max:50',
            'alamat_pengiriman' => 'nullable|string',
            'latitude_pengiriman' => 'nullable|numeric|between:-90,90',
            'longitude_pengiriman' => 'nullable|numeric|between:-180,180',
            'waktu_pengiriman_dijadwalkan' => 'nullable|date|after_or_equal:now',
            'poin_dipakai' => 'nullable|integer|min:0',
        ]);

        if ($validated['tipe_pengambilan'] === 'ditempat' && empty($validated['no_meja'])) {
            return response()->json(['message' => 'Nomor meja wajib diisi untuk makan di tempat.'], 400);
        }

        if ($validated['tipe_pengambilan'] === 'delivery') {
            if (empty($validated['alamat_pengiriman'])) {
                return response()->json(['message' => 'Alamat pengiriman wajib diisi.'], 400);
            }
            if (empty($validated['latitude_pengiriman']) || empty($validated['longitude_pengiriman'])) {
                return response()->json(['message' => 'Lokasi pengiriman wajib dipilih di peta.'], 400);
            }
        }

        $userId = $request->user()->id;

        $cartItems = CartItem::with('menu', 'variant')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang masih kosong.'], 400);
        }

        $totalHarga = $cartItems->sum('subtotal');
        $ongkir = 0;

        if ($validated['tipe_pengambilan'] === 'delivery') {
            $outlet = Outlet::find($validated['outlet_id']);
            $ongkir = $this->hitungOngkirInternal(
                (float) $outlet->latitude,
                (float) $outlet->longitude,
                (float) $validated['latitude_pengiriman'],
                (float) $validated['longitude_pengiriman'],
                $totalHarga,
            );
        }

        try {
            $transaksi = DB::transaction(function () use ($request, $validated, $cartItems, $totalHarga, $ongkir, $userId) {
                $errors = [];
                foreach ($cartItems as $item) {
                    $stokCek = $item->variant?->stok ?? $item->menu->stok;
                    if ($stokCek < $item->jumlah) {
                        $label = $item->menu->nama_menu . ($item->variant ? ' (' . $item->variant->nama . ')' : '');
                        $errors[] = "Stok {$label} tidak mencukupi (sisa: {$stokCek}).";
                    }
                }

                if (!empty($errors)) {
                    throw new \RuntimeException(json_encode(['message' => 'Stok tidak mencukupi.', 'errors' => $errors]));
                }

                $diskon = 0;
                $voucher = null;

                if (!empty($validated['kode_voucher'])) {
                    $voucher = Voucher::where('kode', $validated['kode_voucher'])->lockForUpdate()->first();

                    if (!$voucher || !$voucher->isValid($totalHarga)) {
                        throw new \RuntimeException(json_encode(['message' => 'Voucher tidak valid atau sudah habis.']));
                    }

                    $diskon = $voucher->hitungDiskon($totalHarga);
                }

                $totalSetelahDiskon = $totalHarga - $diskon;
                $diskonPoin = 0;
                $poinDipakai = (int) ($validated['poin_dipakai'] ?? 0);
                if ($poinDipakai > 0) {
                    $user = $request->user();
                    if ($poinDipakai > $user->poin) {
                        throw new \RuntimeException(json_encode(['message' => 'Poin tidak mencukupi.']));
                    }
                    if ($poinDipakai % LoyaltyController::REDEEM_RATE !== 0) {
                        throw new \RuntimeException(json_encode(['message' => 'Jumlah poin harus kelipatan ' . LoyaltyController::REDEEM_RATE . '.']));
                    }
                    $diskonPoin = (int) ($poinDipakai / LoyaltyController::REDEEM_RATE) * LoyaltyController::REDEEM_VALUE;
                    if ($diskonPoin > $totalSetelahDiskon) {
                        $diskonPoin = $totalSetelahDiskon;
                        $poinDipakai = (int) (floor($diskonPoin / LoyaltyController::REDEEM_VALUE) * LoyaltyController::REDEEM_RATE);
                        $diskonPoin = (int) ($poinDipakai / LoyaltyController::REDEEM_RATE) * LoyaltyController::REDEEM_VALUE;
                    }
                }
                $totalSetelahDiskon -= $diskonPoin;
                $totalDenganOngkir = $totalSetelahDiskon + $ongkir;

                $data = [
                    'user_id' => $userId,
                    'outlet_id' => $validated['outlet_id'],
                    'total_harga' => $totalDenganOngkir,
                    'ongkir' => $ongkir,
                    'diskon_poin' => $diskonPoin,
                    'tipe_pemesanan' => 'aplikasi',
                    'tipe_pengambilan' => $validated['tipe_pengambilan'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'status_pembayaran' => $validated['metode_pembayaran'] === 'cash' ? 'lunas' : 'belum_bayar',
                    'status_pesanan' => 'pending',
                ];

                if ($validated['tipe_pengambilan'] === 'ditempat') {
                    $data['no_meja'] = $validated['no_meja'];
                } elseif ($validated['tipe_pengambilan'] === 'delivery') {
                    $data['alamat_pengiriman'] = $validated['alamat_pengiriman'];
                    $data['latitude_pengiriman'] = $validated['latitude_pengiriman'];
                    $data['longitude_pengiriman'] = $validated['longitude_pengiriman'];
                }

                $data['waktu_pengiriman_dijadwalkan'] = $validated['waktu_pengiriman_dijadwalkan'] ?? null;

                $transaksi = Transaksi::create($data);

                $optionItemIds = collect();
                foreach ($cartItems as $item) {
                    foreach (($item->selected_options ?: []) as $opt) {
                        $optionItemIds->push($opt['item_id']);
                    }
                }
                $optionItemMap = MenuOptionGroupItem::whereIn('id', $optionItemIds->unique())
                    ->with('group')
                    ->get()
                    ->keyBy('id');

                foreach ($cartItems as $item) {
                    $snapshotOptions = collect($item->selected_options ?: [])->map(fn($opt) => [
                        'group_id' => $opt['group_id'],
                        'group_name' => $optionItemMap->get($opt['item_id'])?->group?->nama ?? '',
                        'item_id' => $opt['item_id'],
                        'item_name' => $optionItemMap->get($opt['item_id'])?->nama ?? '',
                        'harga_tambahan' => $optionItemMap->get($opt['item_id'])?->harga_tambahan ?? 0,
                    ])->toArray();

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'menu_id' => $item->menu_id,
                        'menu_variant_id' => $item->menu_variant_id,
                        'selected_options' => $snapshotOptions,
                        'jumlah' => $item->jumlah,
                        'subtotal' => $item->subtotal,
                    ]);

                    $stokCek = $item->variant?->stok;
                    if ($stokCek !== null) {
                        MenuVariant::where('id', $item->menu_variant_id)
                            ->where('stok', '>=', $item->jumlah)
                            ->decrement('stok', $item->jumlah);
                    } else {
                        Menu::where('id', $item->menu_id)
                            ->where('stok', '>=', $item->jumlah)
                            ->decrement('stok', $item->jumlah);
                    }
                }

                if ($voucher) {
                    $voucher->increment('terpakai');

                    VoucherPakai::create([
                        'voucher_id' => $voucher->id,
                        'user_id' => $userId,
                        'transaksi_id' => $transaksi->id,
                        'diskon' => $diskon,
                    ]);
                }

                if ($poinDipakai > 0) {
                    LoyaltyPoint::create([
                        'user_id' => $userId,
                        'points' => -$poinDipakai,
                        'type' => 'redeem',
                        'description' => "Tukar poin untuk pesanan #{$transaksi->invoice}",
                        'transaksi_id' => $transaksi->id,
                    ]);
                    $request->user()->decrement('poin', $poinDipakai);
                }

                CartItem::where('user_id', $userId)->delete();

                return $transaksi;
            });
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            return response()->json($error, 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat memproses pesanan.'], 500);
        }

        $transaksi->load('detailTransaksis.menu', 'detailTransaksis.variant');

        if ($validated['metode_pembayaran'] === 'cash') {
            $request->user()->notify(new OrderStatusNotification($transaksi, 'Pesanan berhasil dibuat. Silakan bayar di kasir.'));
        }

        if ($validated['metode_pembayaran'] === 'midtrans') {
            if ($transaksi->total_harga <= 0) {
                $transaksi->update(['status_pembayaran' => 'lunas']);
                return response()->json([
                    'message' => 'Pesanan berhasil dibuat!',
                    'transaksi' => $transaksi,
                ], 201);
            }

            try {
                $snapToken = $this->getMidtransSnapToken($transaksi, $cartItems);
                $transaksi->update(['midtrans_snap_token' => $snapToken]);
            } catch (\Exception $e) {
                $transaksi->update(['status_pembayaran' => 'gagal']);
                return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
            }

            return response()->json([
                'message' => 'Pesanan berhasil dibuat, silakan lanjutkan pembayaran.',
                'transaksi' => $transaksi,
                'snap_token' => $snapToken,
            ], 201);
        }

        return response()->json([
            'message' => 'Pesanan berhasil dibuat. Silakan bayar di kasir.',
            'transaksi' => $transaksi,
        ], 201);
    }

    public function cekVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'kode' => 'required|string|max:50',
            'total' => 'required|integer|min:0',
        ]);

        $voucher = Voucher::where('kode', $request->kode)->first();

        if (!$voucher || !$voucher->isValid($request->total)) {
            return response()->json(['message' => 'Voucher tidak valid.'], 400);
        }

        $diskon = $voucher->hitungDiskon($request->total);

        return response()->json([
            'valid' => true,
            'voucher_id' => $voucher->id,
            'kode' => $voucher->kode,
            'nama' => $voucher->nama,
            'diskon' => $diskon,
            'total_setelah_diskon' => $request->total - $diskon,
        ]);
    }

    public function hitungOngkir(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id,aktif,1',
            'latitude_pengiriman' => 'required|numeric|between:-90,90',
            'longitude_pengiriman' => 'required|numeric|between:-180,180',
            'total_belanja' => 'required|integer|min:0',
        ]);

        $outlet = Outlet::findOrFail($validated['outlet_id']);
        $ongkir = $this->hitungOngkirInternal(
            (float) $outlet->latitude,
            (float) $outlet->longitude,
            (float) $validated['latitude_pengiriman'],
            (float) $validated['longitude_pengiriman'],
            $validated['total_belanja'],
        );

        return response()->json([
            'ongkir' => $ongkir,
            'total_ongkir_formatted' => 'Rp ' . number_format($ongkir, 0, ',', '.'),
        ]);
    }

    private function hitungOngkirInternal(float $lat1, float $lng1, float $lat2, float $lng2, int $totalBelanja): int
    {
        $jarakKm = $this->hitungJarak($lat1, $lng1, $lat2, $lng2);
        $ratePerKm = 2000;
        $minOngkir = 5000;
        $gratisMinBelanja = 50000;

        if ($totalBelanja >= $gratisMinBelanja) {
            return 0;
        }

        $ongkir = (int) round($jarakKm * $ratePerKm);

        return max($ongkir, $minOngkir);
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

    public function payNow(Request $request, Transaksi $transaksi): JsonResponse
    {
        if ($transaksi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($transaksi->status_pembayaran !== 'belum_bayar') {
            return response()->json(['message' => 'Pesanan sudah dibayar'], 400);
        }

        if ($transaksi->metode_pembayaran !== 'midtrans') {
            return response()->json(['message' => 'Metode pembayaran tidak mendukung pembayaran online'], 400);
        }

        $transaksi->load('detailTransaksis.menu', 'detailTransaksis.variant');
        $cartItems = $transaksi->detailTransaksis->map(fn($d) => (object) [
            'menu_id' => $d->menu_id,
            'menu' => $d->menu,
            'variant' => $d->variant,
            'harga' => $d->jumlah > 0 ? (int)($d->subtotal / $d->jumlah) : 0,
            'jumlah' => $d->jumlah,
        ]);

        try {
            $snapToken = $this->getMidtransSnapToken($transaksi, $cartItems, true);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }

        $transaksi->update(['midtrans_snap_token' => $snapToken]);

        return response()->json(['snap_token' => $snapToken]);
    }

    private function getMidtransSnapToken(Transaksi $transaksi, $cartItems, bool $isRetry = false): ?string
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $itemDetails = [];
        $itemTotal = 0;
        foreach ($cartItems as $item) {
            $subtotal = $item->harga * $item->jumlah;
            $itemTotal += $subtotal;
            $itemDetails[] = [
                'id' => (string) $item->menu_id,
                'price' => $item->harga,
                'quantity' => $item->jumlah,
                'name' => $item->menu->nama_menu . ($item->variant ? ' (' . $item->variant->nama . ')' : ''),
            ];
        }

        if ($transaksi->ongkir && $transaksi->ongkir > 0) {
            $itemTotal += $transaksi->ongkir;
            $itemDetails[] = [
                'id' => 'ONGKIR',
                'price' => $transaksi->ongkir,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        $selisih = $itemTotal - $transaksi->total_harga;

        if ($selisih > 0) {
            $diskonPoin = (int) $transaksi->diskon_poin;
            $sisaDiskon = $selisih - $diskonPoin;

            if ($diskonPoin > 0) {
                $itemDetails[] = [
                    'id' => 'DISKON_POIN',
                    'price' => -$diskonPoin,
                    'quantity' => 1,
                    'name' => 'Diskon Poin',
                ];
            }
            if ($sisaDiskon > 0) {
                $itemDetails[] = [
                    'id' => 'DISKON_VOUCHER',
                    'price' => -$sisaDiskon,
                    'quantity' => 1,
                    'name' => 'Diskon Voucher',
                ];
            }
        }

        $orderId = $transaksi->invoice ?? ('TXN-' . $transaksi->id);
        if ($isRetry) {
            $orderId = $orderId . '-R' . time();
        }
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $transaksi->total_harga,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $transaksi->user?->name ?? 'Customer',
                'email' => $transaksi->user?->email ?? 'customer@tenscoffee.com',
            ],
            'credit_card' => [
                'secure' => true,
                'installment' => [
                    'required' => false,
                    'terms' => [
                        'bca' => [3, 6, 12],
                        'bni' => [3, 6, 12],
                        'mandiri' => [3, 6, 12],
                        'bri' => [3, 6, 12],
                        'cimb' => [3, 6, 12],
                        'maybank' => [3, 6, 12],
                    ],
                ],
            ],
            'enabled_payments' => [
                'credit_card',
                'bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'permata_va',
                'gopay', 'shopeepay', 'other_qris',
                'akulaku',
                'kredivo',
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            logger()->error('Midtrans Snap token failed: ' . $msg, [
                'transaksi_id' => $transaksi->id,
                'total_harga' => $transaksi->total_harga,
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException($msg);
        }
    }
}
