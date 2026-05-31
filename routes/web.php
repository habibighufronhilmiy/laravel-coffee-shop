<?php

use App\Http\Controllers\Web\AuthController;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/menu', function () {
    return view('customer.menu');
})->name('menu');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('customer.profile');
    })->name('profile');

    Route::get('/cart', function () {
        return view('customer.cart');
    })->name('cart');

    Route::get('/checkout', function () {
        return view('customer.checkout');
    })->name('checkout');

    Route::get('/orders', function () {
        return view('customer.orders');
    })->name('orders');

    Route::get('/wishlist', function () {
        return view('customer.wishlist');
    })->name('wishlist');

    Route::get('/tracking', function () {
        return view('customer.tracking');
    })->name('tracking');

    Route::post('/orders/confirm/{transaksi}', function (Transaksi $transaksi) {
        if ($transaksi->user_id !== auth()->id()) {
            abort(403);
        }
        if ($transaksi->status_pesanan === 'dibatalkan') {
            return redirect()->route('orders')->with('error', 'Pesanan sudah dibatalkan.');
        }
        $transaksi->update(['status_pembayaran' => 'lunas', 'status_pesanan' => 'diproses']);
        return redirect()->route('orders')->with('success', 'Pembayaran berhasil dikonfirmasi!');
    })->name('orders.confirm');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/laporan/export', function () {
    $totalPendapatan = Transaksi::where('status_pembayaran', 'lunas')->sum('total_harga');
    $totalTransaksi = Transaksi::count();
    $totalMenuTerjual = DetailTransaksi::sum('jumlah');
    $totalPelanggan = User::where('role', 'customer')->count();

    $menuTerlaris = DetailTransaksi::selectRaw('menu_id, SUM(jumlah) as total_terjual')
        ->groupBy('menu_id')
        ->orderByDesc('total_terjual')
        ->take(5)
        ->with('menu')
        ->get()
        ->toArray();

    $transaksiBulanan = Transaksi::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total_harga) as total")
        ->where('status_pembayaran', 'lunas')
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get()
        ->toArray();

    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="laporan-tenscoffee-' . now()->format('Y-m-d') . '.csv"',
    ];

    $callback = function () use ($totalPendapatan, $totalTransaksi, $totalMenuTerjual, $totalPelanggan, $menuTerlaris, $transaksiBulanan) {
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF");

        fputcsv($file, ['LAPORAN TENS COFFEE', '', '']);
        fputcsv($file, ['Tanggal: ' . now()->format('d/m/Y H:i'), '', '']);
        fputcsv($file, ['', '', '']);

        fputcsv($file, ['RINGKASAN']);
        fputcsv($file, ['Total Pendapatan', 'Rp' . number_format($totalPendapatan, 0, ',', '.'), '']);
        fputcsv($file, ['Total Transaksi', $totalTransaksi, '']);
        fputcsv($file, ['Total Menu Terjual', $totalMenuTerjual, '']);
        fputcsv($file, ['Total Pelanggan', $totalPelanggan, '']);
        fputcsv($file, ['', '', '']);

        fputcsv($file, ['MENU TERLARIS']);
        fputcsv($file, ['Menu', 'Terjual', '']);
        foreach ($menuTerlaris as $item) {
            fputcsv($file, [$item['menu']['nama_menu'] ?? '-', $item['total_terjual'], '']);
        }
        fputcsv($file, ['', '', '']);

        fputcsv($file, ['PENDAPATAN BULANAN']);
        fputcsv($file, ['Bulan', 'Total', '']);
        foreach ($transaksiBulanan as $item) {
            fputcsv($file, [$item['bulan'], 'Rp' . number_format($item['total'], 0, ',', '.'), '']);
        }

        fclose($file);
    };

    return response()->streamDownload($callback, 'laporan-tenscoffee-' . now()->format('Y-m-d') . '.csv', $headers);
})->name('laporan.export')->middleware('auth');
