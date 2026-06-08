<?php

use App\Models\DetailTransaksi;
use App\Models\Menu;
use App\Models\Outlet;
use App\Models\Rating;
use App\Models\Transaksi;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('rating index returns ratings for menu', function () {
    $menu = Menu::factory()->create();
    Rating::factory()->count(3)->create(['menu_id' => $menu->id]);

    $response = $this->getJson('/api/menu/' . $menu->id . '/ratings');

    $response->assertOk()
        ->assertJsonCount(3);
});

test('rating index includes user info', function () {
    $menu = Menu::factory()->create();
    Rating::factory()->create([
        'menu_id' => $menu->id,
        'user_id' => $this->user->id,
        'rating' => 5,
        'review' => 'Enak banget!',
    ]);

    $response = $this->getJson('/api/menu/' . $menu->id . '/ratings');

    expect($response->json('0.user.name'))->toBe($this->user->name);
    expect($response->json('0.rating'))->toBe(5);
    expect($response->json('0.review'))->toBe('Enak banget!');
});

test('rating index returns empty for menu without ratings', function () {
    $menu = Menu::factory()->create();

    $this->getJson('/api/menu/' . $menu->id . '/ratings')
        ->assertOk()
        ->assertJson([]);
});

test('rating store creates new rating', function () {
    $menu = Menu::factory()->create();

    $response = $this->postJson('/api/ratings', [
        'menu_id' => $menu->id,
        'rating' => 4,
        'review' => 'Kopi enak, recommended!',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('ratings', [
        'user_id' => $this->user->id,
        'menu_id' => $menu->id,
        'rating' => 4,
        'review' => 'Kopi enak, recommended!',
    ]);
});

test('rating store updates existing rating', function () {
    $menu = Menu::factory()->create();
    Rating::create([
        'user_id' => $this->user->id,
        'menu_id' => $menu->id,
        'rating' => 3,
        'review' => 'Biasa aja',
    ]);

    $response = $this->postJson('/api/ratings', [
        'menu_id' => $menu->id,
        'rating' => 5,
        'review' => 'Updated! Ternyata enak',
    ]);

    $response->assertOk();
    expect($response->json('message'))->toBe('Rating berhasil diperbarui');
    $this->assertDatabaseCount('ratings', 1);
    $this->assertDatabaseHas('ratings', ['rating' => 5, 'review' => 'Updated! Ternyata enak']);
});

test('rating store verifies purchase when transaksi_id provided', function () {
    $menu = Menu::factory()->create();
    $outlet = Outlet::factory()->create();
    $transaksi = Transaksi::factory()->create([
        'user_id' => $this->user->id,
        'outlet_id' => $outlet->id,
    ]);
    DetailTransaksi::create([
        'transaksi_id' => $transaksi->id,
        'menu_id' => $menu->id,
        'jumlah' => 1,
        'subtotal' => 25000,
    ]);

    $response = $this->postJson('/api/ratings', [
        'menu_id' => $menu->id,
        'transaksi_id' => $transaksi->id,
        'rating' => 5,
        'review' => 'Verified purchase!',
    ]);

    $response->assertCreated();
});

test('rating store rejects non-existent transaksi', function () {
    $menu = Menu::factory()->create();

    $this->postJson('/api/ratings', [
        'menu_id' => $menu->id,
        'transaksi_id' => 99999,
        'rating' => 5,
    ])->assertStatus(422);
});

test('rating store validates rating range', function () {
    $menu = Menu::factory()->create();

    $this->postJson('/api/ratings', ['menu_id' => $menu->id, 'rating' => 6])
        ->assertStatus(422);

    $this->postJson('/api/ratings', ['menu_id' => $menu->id, 'rating' => 0])
        ->assertStatus(422);
});

test('rating store requires valid menu', function () {
    $this->postJson('/api/ratings', ['menu_id' => 99999, 'rating' => 3])
        ->assertStatus(422);
});

test('rating public without auth returns data', function () {
    $menu = Menu::factory()->create();
    Rating::factory()->count(2)->create(['menu_id' => $menu->id]);

    $response = $this->getJson('/api/menu/' . $menu->id . '/ratings');

    $response->assertOk()->assertJsonCount(2);
});
