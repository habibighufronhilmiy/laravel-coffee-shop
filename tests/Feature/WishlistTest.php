<?php

use App\Models\Menu;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('wishlist index returns empty for new user', function () {
    $this->getJson('/api/wishlists')
        ->assertOk()
        ->assertJson([]);
});

test('wishlist toggle adds item', function () {
    $menu = Menu::factory()->create();

    $response = $this->postJson('/api/wishlists/toggle', [
        'menu_id' => $menu->id,
    ]);

    $response->assertCreated();
    expect($response->json('wishlisted'))->toBeTrue();

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $this->user->id,
        'menu_id' => $menu->id,
    ]);
});

test('wishlist toggle removes existing item', function () {
    $menu = Menu::factory()->create();
    $this->user->wishlists()->create(['menu_id' => $menu->id]);

    $response = $this->postJson('/api/wishlists/toggle', [
        'menu_id' => $menu->id,
    ]);

    $response->assertOk();
    expect($response->json('wishlisted'))->toBeFalse();

    $this->assertDatabaseMissing('wishlists', [
        'user_id' => $this->user->id,
        'menu_id' => $menu->id,
    ]);
});

test('wishlist index returns all wishlisted items with menu', function () {
    $menus = Menu::factory()->count(3)->create();
    foreach ($menus as $menu) {
        $this->user->wishlists()->create(['menu_id' => $menu->id]);
    }

    $response = $this->getJson('/api/wishlists');

    $response->assertOk()
        ->assertJsonCount(3);
    expect($response->json('0'))->toHaveKeys(['id', 'menu_id', 'menu']);
});

test('wishlist toggle requires menu_id', function () {
    $this->postJson('/api/wishlists/toggle', [])
        ->assertStatus(422);
});

test('wishlist toggle 404 for non-existent menu', function () {
    $this->postJson('/api/wishlists/toggle', ['menu_id' => 99999])
        ->assertStatus(422);
});

test('wishlist requires auth', function () {
    $this->app->get('auth')->forgetGuards();

    $this->getJson('/api/wishlists')->assertStatus(401);
    $this->postJson('/api/wishlists/toggle', ['menu_id' => 1])->assertStatus(401);
});
