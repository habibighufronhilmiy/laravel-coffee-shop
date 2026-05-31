<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\LoyaltyController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/google/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('/google/callback', [GoogleAuthController::class, 'callback']);
});

Route::get('/menu', [MenuController::class, 'index']);
Route::get('/menu/{menu}', [MenuController::class, 'show']);
Route::get('/kategoris', [MenuController::class, 'kategoris']);
Route::get('/banners', [MenuController::class, 'banners']);
Route::get('/menu/{menu}/ratings', [RatingController::class, 'index']);

Route::post('/midtrans/notification', [MidtransController::class, 'notification']);
Route::get('/midtrans/client-key', [MidtransController::class, 'getClientKey']);

Route::get('/outlets', [App\Http\Controllers\Api\OutletController::class, 'index']);
Route::get('/outlets/nearby', [App\Http\Controllers\Api\OutletController::class, 'nearby']);
Route::get('/outlets/{outlet}', [App\Http\Controllers\Api\OutletController::class, 'show']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove']);
    Route::post('/cart/clear', [CartController::class, 'clear']);

    Route::post('/checkout', [CheckoutController::class, 'process']);
    Route::post('/checkout/cekVoucher', [CheckoutController::class, 'cekVoucher']);
    Route::post('/checkout/hitung-ongkir', [CheckoutController::class, 'hitungOngkir']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{transaksi}', [OrderController::class, 'show']);
    Route::post('/orders/{transaksi}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{transaksi}/reorder', [OrderController::class, 'reorder']);
    Route::post('/orders/{transaksi}/pay-now', [CheckoutController::class, 'payNow']);
    Route::post('/orders/{transaksi}/confirm-payment', [OrderController::class, 'confirmPayment']);

    Route::get('/loyalty', [LoyaltyController::class, 'index']);
    Route::post('/loyalty/redeem', [LoyaltyController::class, 'redeem']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);

    Route::get('/wishlists', [WishlistController::class, 'index']);
    Route::post('/wishlists/toggle', [WishlistController::class, 'toggle']);

    Route::post('/ratings', [RatingController::class, 'store']);
});
