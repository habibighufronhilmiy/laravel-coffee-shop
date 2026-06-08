<?php

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\MenuOptionGroup;
use App\Models\MenuOptionGroupItem;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('empty cart returns empty array', function () {
    $response = $this->getJson('/api/cart');
    $response->assertOk();
    expect($response->json('cart'))->toBe([]);
    expect($response->json('total'))->toBe(0);
});

test('add item to cart', function () {
    $menu = Menu::factory()->create(['harga' => 25000]);

    $response = $this->postJson('/api/cart/add', [
        'menu_id' => $menu->id,
        'jumlah' => 2,
    ]);

    $response->assertOk();
    expect($response->json('message'))->toContain('ditambahkan ke keranjang');

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $this->user->id,
        'menu_id' => $menu->id,
        'jumlah' => 2,
        'subtotal' => 50000,
    ]);
});

test('add item with variant', function () {
    $menu = Menu::factory()->create(['harga' => 25000]);
    $variant = MenuVariant::factory()->create([
        'menu_id' => $menu->id,
        'nama' => 'Large',
        'harga_tambahan' => 5000,
    ]);

    $response = $this->postJson('/api/cart/add', [
        'menu_id' => $menu->id,
        'menu_variant_id' => $variant->id,
        'jumlah' => 1,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $this->user->id,
        'menu_variant_id' => $variant->id,
    ]);
});

test('add item with options', function () {
    $menu = Menu::factory()->create(['harga' => 25000]);
    $group = MenuOptionGroup::factory()->create([
        'menu_id' => $menu->id,
        'tipe' => 'multiple',
    ]);
    $option = MenuOptionGroupItem::factory()->create([
        'menu_option_group_id' => $group->id,
        'harga_tambahan' => 3000,
    ]);

    $response = $this->postJson('/api/cart/add', [
        'menu_id' => $menu->id,
        'jumlah' => 1,
        'selected_options' => [
            ['group_id' => $group->id, 'item_id' => $option->id],
        ],
    ]);

    $response->assertOk();
});

test('add same item increments quantity', function () {
    $menu = Menu::factory()->create(['harga' => 15000]);

    $this->postJson('/api/cart/add', ['menu_id' => $menu->id, 'jumlah' => 1]);
    $this->postJson('/api/cart/add', ['menu_id' => $menu->id, 'jumlah' => 2]);

    $response = $this->getJson('/api/cart');
    expect($response->json('cart'))->toHaveCount(1);
    expect($response->json('cart.0.jumlah'))->toBe(3);
    expect($response->json('total'))->toBe(45000);
});

test('add item fails without auth', function () {
    $this->app->get('auth')->forgetGuards();

    $menu = Menu::factory()->create();
    $this->postJson('/api/cart/add', ['menu_id' => $menu->id, 'jumlah' => 1])
        ->assertStatus(401);
});

test('add item with insufficient stock fails', function () {
    $menu = Menu::factory()->habis()->create();

    $this->postJson('/api/cart/add', ['menu_id' => $menu->id, 'jumlah' => 1])
        ->assertStatus(400);
});

test('update cart item quantity', function () {
    $menu = Menu::factory()->create(['harga' => 20000]);
    $this->postJson('/api/cart/add', ['menu_id' => $menu->id, 'jumlah' => 1]);

    $cart = $this->getJson('/api/cart')->json('cart');
    $cartItemId = $cart[0]['id'];

    $response = $this->putJson('/api/cart/update', [
        'id' => $cartItemId,
        'menu_id' => $menu->id,
        'jumlah' => 5,
    ]);

    $response->assertOk();
    expect($response->json('message'))->toBe('Item diperbarui');
    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItemId,
        'jumlah' => 5,
        'subtotal' => 100000,
    ]);
});

test('remove item from cart', function () {
    $menu = Menu::factory()->create();
    $this->postJson('/api/cart/add', ['menu_id' => $menu->id, 'jumlah' => 1]);

    $cartItemId = $this->getJson('/api/cart')->json('cart.0.id');

    $this->deleteJson('/api/cart/remove/' . $cartItemId)
        ->assertOk()
        ->assertJson(['message' => 'Item dihapus dari keranjang']);

    $this->assertDatabaseMissing('cart_items', ['id' => $cartItemId]);
});

test('clear cart removes all items', function () {
    $menu = Menu::factory()->count(3)->create();
    foreach ($menu as $m) {
        $this->postJson('/api/cart/add', ['menu_id' => $m->id, 'jumlah' => 1]);
    }

    $this->postJson('/api/cart/clear')->assertOk();

    $response = $this->getJson('/api/cart');
    expect($response->json('cart'))->toBe([]);
});
