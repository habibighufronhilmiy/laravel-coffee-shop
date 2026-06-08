<?php

use App\Models\Banner;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Outlet;
use App\Models\MenuVariant;
use App\Models\MenuOptionGroup;
use App\Models\MenuOptionGroupItem;
use App\Models\User;

test('menu index returns all menus with relations', function () {
    Kategori::factory()->create();
    $menu = Menu::factory()->create();

    $response = $this->getJson('/api/menu');

    $response->assertOk()
        ->assertJsonCount(1);
    $response->assertJsonFragment(['nama_menu' => $menu->nama_menu]);
});

test('menu index includes kategori in response', function () {
    $kategori = Kategori::factory()->create(['nama_kategori' => 'Kopi Spesial']);
    Menu::factory()->create(['kategori_id' => $kategori->id]);

    $response = $this->getJson('/api/menu');

    $response->assertOk();
    expect($response->json('0.kategori.nama_kategori'))->toBe('Kopi Spesial');
});

test('menu show returns single menu detail', function () {
    $menu = Menu::factory()->create();

    $response = $this->getJson('/api/menu/' . $menu->id);

    $response->assertOk()
        ->assertJsonFragment(['nama_menu' => $menu->nama_menu]);
});

test('menu show includes variants and option groups', function () {
    $menu = Menu::factory()->create();
    $variant = MenuVariant::factory()->create(['menu_id' => $menu->id, 'nama' => 'Large']);
    $group = MenuOptionGroup::factory()->create(['menu_id' => $menu->id, 'nama' => 'Topping']);
    MenuOptionGroupItem::factory()->create([
        'menu_option_group_id' => $group->id,
        'nama' => 'Boba',
    ]);

    $response = $this->getJson('/api/menu/' . $menu->id);

    $response->assertOk();
    expect($response->json('variants'))->toHaveCount(1);
    expect($response->json('option_groups'))->toHaveCount(1);
});

test('menu show marks wishlisted for authenticated user', function () {
    $user = User::factory()->create();
    $menu = Menu::factory()->create();
    $user->wishlists()->create(['menu_id' => $menu->id]);

    $response = $this->actingAs($user)->getJson('/api/menu/' . $menu->id);

    $response->assertOk();
    expect($response->json('wishlisted'))->toBeTrue();
});

test('menu show wishlisted false for guest', function () {
    $menu = Menu::factory()->create();

    $response = $this->getJson('/api/menu/' . $menu->id);

    $response->assertOk();
    expect($response->json('wishlisted'))->toBeFalse();
    expect($response->json('wishlisted'))->not->toBeNull();
});

test('menu show 404 for non-existent menu', function () {
    $this->getJson('/api/menu/99999')->assertStatus(404);
});

test('kategoris returns all categories', function () {
    Kategori::factory()->count(3)->create();

    $response = $this->getJson('/api/kategoris');

    $response->assertOk()
        ->assertJsonCount(3);
});

test('banners returns only active banners ordered by urutan', function () {
    Banner::factory()->create(['judul' => 'Active 1', 'urutan' => 2, 'aktif' => true]);
    Banner::factory()->create(['judul' => 'Active 2', 'urutan' => 1, 'aktif' => true]);
    Banner::factory()->create(['judul' => 'Inactive', 'aktif' => false]);

    $response = $this->getJson('/api/banners');

    $response->assertOk()
        ->assertJsonCount(2);
    expect($response->json('0.judul'))->toBe('Active 2');
    expect($response->json('1.judul'))->toBe('Active 1');
});

test('menu index filtered by wishlisted when authenticated', function () {
    $user = User::factory()->create();
    Menu::factory()->count(3)->create();
    $menu = Menu::latest('id')->first();
    $user->wishlists()->create(['menu_id' => $menu->id]);

    $response = $this->actingAs($user)->getJson('/api/menu');

    $response->assertOk();
    $wishlisted = collect($response->json())->where('id', $menu->id)->first();
    expect($wishlisted['wishlisted'])->toBeTrue();
});

test('menu with zero stock is still returned', function () {
    Menu::factory()->habis()->create();

    $response = $this->getJson('/api/menu');

    $response->assertOk();
    expect($response->json('0.stok'))->toBe(0);
});
