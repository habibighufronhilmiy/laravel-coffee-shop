<?php

use App\Models\LoyaltyPoint;
use App\Models\Transaksi;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('loyalty index returns points balance and history', function () {
    $this->user->poin = 300;
    $this->user->save();

    LoyaltyPoint::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'type' => 'earn',
        'points' => 100,
    ]);

    $response = $this->getJson('/api/loyalty');

    $response->assertOk();
    expect($response->json('balance'))->toBe(300);
    expect($response->json('history.data'))->toHaveCount(3);
});

test('loyalty index returns zero poin for new user', function () {
    $response = $this->getJson('/api/loyalty');

    $response->assertOk();
    expect($response->json('balance'))->toBe(0);
    expect($response->json('history.data'))->toHaveCount(0);
});

test('loyalty redeem requires sufficient points', function () {
    $this->postJson('/api/loyalty/redeem', ['points' => 100])
        ->assertStatus(400);
});

test('loyalty redeem with sufficient points', function () {
    $this->user->poin = 500;
    $this->user->save();

    $response = $this->postJson('/api/loyalty/redeem', ['points' => 200]);

    $response->assertOk();
    expect($response->json('message'))->toContain('Diskon');
    expect($response->json('discount'))->toBe(20000);
    expect($response->json('points_used'))->toBe(200);
});

test('loyalty requires auth', function () {
    $this->app->get('auth')->forgetGuards();
    $this->getJson('/api/loyalty')->assertStatus(401);
});

test('loyalty history is paginated', function () {
    LoyaltyPoint::factory()->count(20)->create([
        'user_id' => $this->user->id,
        'type' => 'earn',
        'points' => 50,
    ]);

    $response = $this->getJson('/api/loyalty');

    $response->assertOk();
    expect($response->json('history.data'))->toHaveCount(20);
    expect($response->json('history.total'))->toBe(20);
});
