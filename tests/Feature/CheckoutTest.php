<?php

use App\Models\Menu;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Voucher;
use App\Models\CartItem;

beforeEach(function () {
    $this->user = User::factory()->create(['poin' => 500]);
    $this->actingAs($this->user);

    $this->menu = Menu::factory()->create(['harga' => 30000, 'stok' => 20]);
    $this->outlet = Outlet::factory()->create();

    CartItem::create([
        'user_id' => $this->user->id,
        'menu_id' => $this->menu->id,
        'jumlah' => 2,
        'harga' => 30000,
        'subtotal' => 60000,
    ]);
});

test('checkout with cash creates transaction', function () {
    $response = $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '5',
        'metode_pembayaran' => 'cash',
    ]);

    $response->assertCreated();
    expect($response->json('message'))->toBe('Pesanan berhasil dibuat. Silakan bayar di kasir.');

    $this->assertDatabaseHas('transaksis', [
        'user_id' => $this->user->id,
        'total_harga' => 60000,
        'status_pembayaran' => 'lunas',
        'tipe_pengambilan' => 'ditempat',
    ]);

    $this->assertDatabaseHas('menus', ['id' => $this->menu->id, 'stok' => 18]);
});

test('checkout with pickup', function () {
    $response = $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'pickup',
        'metode_pembayaran' => 'midtrans',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('transaksis', ['tipe_pengambilan' => 'pickup']);
});

test('checkout with delivery requires address', function () {
    $response = $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'delivery',
        'metode_pembayaran' => 'midtrans',
        'alamat_pengiriman' => 'Jl. Merdeka No. 1, Jakarta',
        'latitude_pengiriman' => -6.2,
        'longitude_pengiriman' => 106.8,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('transaksis', ['tipe_pengambilan' => 'delivery']);
});

test('checkout with voucher applies discount', function () {
    $voucher = Voucher::factory()->create([
        'kode' => 'DISC10',
        'tipe' => 'persen',
        'nilai' => 10,
        'min_belanja' => 30000,
        'maks_diskon' => 10000,
    ]);

    $response = $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '3',
        'metode_pembayaran' => 'cash',
        'kode_voucher' => 'DISC10',
    ]);

    $response->assertCreated();
    // 10% of 60000 = 6000
    $this->assertDatabaseHas('transaksis', ['total_harga' => 54000]);
    $this->assertDatabaseHas('voucher_pakai', ['diskon' => 6000]);
    expect($voucher->fresh()->terpakai)->toBe(1);
});

test('checkout applies poin discount', function () {
    $response = $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '7',
        'metode_pembayaran' => 'cash',
        'poin_dipakai' => 200,
    ]);

    $response->assertCreated();
    // 200 poin = 200 * (10000/100) = 20000 diskon -> total = 60000 - 20000 = 40000
    $this->assertDatabaseHas('transaksis', ['total_harga' => 40000, 'diskon_poin' => 20000]);
});

test('checkout fails on insufficient stock', function () {
    $this->menu->update(['stok' => 1]);

    $response = $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '5',
        'metode_pembayaran' => 'cash',
    ]);

    $response->assertStatus(400);
    expect($response->json('message'))->toBe('Stok tidak mencukupi.');
});

test('checkout fails on empty cart', function () {
    CartItem::where('user_id', $this->user->id)->delete();

    $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '5',
        'metode_pembayaran' => 'cash',
    ])->assertStatus(400);
});

test('checkout fails with invalid voucher', function () {
    $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '5',
        'metode_pembayaran' => 'cash',
        'kode_voucher' => 'INVALID123',
    ])->assertStatus(400);
});

test('cek voucher valid', function () {
    Voucher::factory()->create([
        'kode' => 'VALID10',
        'tipe' => 'persen',
        'nilai' => 10,
        'min_belanja' => 30000,
        'maks_diskon' => 5000,
    ]);

    $response = $this->postJson('/api/checkout/cekVoucher', [
        'kode' => 'VALID10',
        'total' => 60000,
    ]);

    $response->assertOk();
    expect($response->json('valid'))->toBeTrue();
    expect($response->json('diskon'))->toBe(5000);
    expect($response->json('total_setelah_diskon'))->toBe(55000);
});

test('cek voucher invalid returns error', function () {
    $this->postJson('/api/checkout/cekVoucher', [
        'kode' => 'NEXIST',
        'total' => 50000,
    ])->assertStatus(400);
});

test('cek voucher below minimum belanja fails', function () {
    Voucher::factory()->create([
        'kode' => 'MIN10',
        'tipe' => 'persen',
        'nilai' => 10,
        'min_belanja' => 100000,
    ]);

    $this->postJson('/api/checkout/cekVoucher', [
        'kode' => 'MIN10',
        'total' => 50000,
    ])->assertStatus(400);
});

test('hitung ongkir returns shipping cost', function () {
    $response = $this->postJson('/api/checkout/hitung-ongkir', [
        'outlet_id' => $this->outlet->id,
        'latitude_pengiriman' => $this->outlet->latitude + 0.01,
        'longitude_pengiriman' => $this->outlet->longitude + 0.01,
        'total_belanja' => 30000,
    ]);

    $response->assertOk();
    expect($response->json('ongkir'))->toBe(5000);
});

test('checkout clears cart after success', function () {
    $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '5',
        'metode_pembayaran' => 'cash',
    ]);

    $cartResponse = $this->getJson('/api/cart');
    expect($cartResponse->json('cart'))->toBe([]);
});

test('checkout with midtrans generates snap token', function () {
    $response = $this->postJson('/api/checkout', [
        'outlet_id' => $this->outlet->id,
        'tipe_pengambilan' => 'pickup',
        'metode_pembayaran' => 'midtrans',
    ]);

    $response->assertCreated();
    expect($response->json('snap_token'))->not->toBeNull();
    expect($response->json('message'))->toBe('Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
});
