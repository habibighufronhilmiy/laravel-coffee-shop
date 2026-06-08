<?php

use App\Models\CartItem;
use App\Models\Menu;
use App\Models\Outlet;
use App\Models\Transaksi;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $menu = Menu::factory()->create(['harga' => 25000, 'stok' => 10]);
    $outlet = Outlet::factory()->create();

    CartItem::create([
        'user_id' => $this->user->id,
        'menu_id' => $menu->id,
        'jumlah' => 1,
        'harga' => 25000,
        'subtotal' => 25000,
    ]);

    $this->postJson('/api/checkout', [
        'outlet_id' => $outlet->id,
        'tipe_pengambilan' => 'ditempat',
        'no_meja' => '3',
        'metode_pembayaran' => 'cash',
    ]);

    $this->transaksi = Transaksi::where('user_id', $this->user->id)->first();
});

test('orders list returns paginated orders', function () {
    $response = $this->getJson('/api/orders');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0'))->toHaveKeys(['id', 'invoice', 'total_harga', 'status_pesanan', 'detail_transaksis']);
});

test('orders list filtered by status', function () {
    $response = $this->getJson('/api/orders?status=selesai');
    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);

    $response = $this->getJson('/api/orders?status=pending');
    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);

    $response = $this->getJson('/api/orders?status=diproses');
    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

test('orders list searched by invoice', function () {
    $response = $this->getJson('/api/orders?search=' . $this->transaksi->invoice);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

test('orders list only shows own orders', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    $response = $this->getJson('/api/orders');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

test('order show returns detail', function () {
    $response = $this->getJson('/api/orders/' . $this->transaksi->id);

    $response->assertOk();
    expect($response->json('id'))->toBe($this->transaksi->id);
    expect($response->json('detail_transaksis'))->toHaveCount(1);
});

test('order show 404 for other user order', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    $this->getJson('/api/orders/' . $this->transaksi->id)
        ->assertStatus(403);
});

test('cancel order restores stock', function () {
    $menuId = $this->transaksi->detailTransaksis->first()->menu_id;
    $originalStok = Menu::find($menuId)->stok;

    $response = $this->postJson('/api/orders/' . $this->transaksi->id . '/cancel');

    $response->assertOk();
    expect($this->transaksi->fresh()->status_pesanan)->toBe('dibatalkan');
    expect(Menu::find($menuId)->stok)->toBe($originalStok + 1);
});

test('reorder adds previous items to cart', function () {
    $response = $this->postJson('/api/orders/' . $this->transaksi->id . '/reorder');

    $response->assertOk();
    $cartResponse = $this->getJson('/api/cart');
    expect($cartResponse->json('cart'))->toHaveCount(1);
});

test('confirm payment updates status', function () {
    $outlet = Outlet::factory()->create();
    $pendingTransaksi = Transaksi::factory()->pending()->create([
        'user_id' => $this->user->id,
        'outlet_id' => $outlet->id,
    ]);

    $response = $this->postJson('/api/orders/' . $pendingTransaksi->id . '/confirm-payment');

    $response->assertOk();
    expect($pendingTransaksi->fresh()->status_pembayaran)->toBe('lunas');
});
