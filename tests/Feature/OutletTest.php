<?php

use App\Models\Outlet;

test('outlet index returns all active outlets', function () {
    Outlet::factory()->count(3)->create();
    Outlet::factory()->nonaktif()->create();

    $response = $this->getJson('/api/outlets');

    $response->assertOk()
        ->assertJsonCount(3);
});

test('outlet index returns with formatted fields', function () {
    Outlet::factory()->create([
        'nama' => 'Tens Coffee Pusat',
        'jam_buka' => '08:00',
        'jam_tutup' => '22:00',
    ]);

    $response = $this->getJson('/api/outlets');

    expect($response->json('0.nama'))->toBe('Tens Coffee Pusat');
    expect($response->json('0.jam_buka'))->toBe('08:00');
    expect($response->json('0.jam_tutup'))->toBe('22:00');
});

test('outlet show returns single outlet', function () {
    $outlet = Outlet::factory()->create();

    $response = $this->getJson('/api/outlets/' . $outlet->id);

    $response->assertOk();
    expect($response->json('id'))->toBe($outlet->id);
    expect($response->json('nama'))->toBe($outlet->nama);
});

test('outlet show 404 for non-existent', function () {
    $this->getJson('/api/outlets/99999')->assertStatus(404);
});

test('outlet nearby returns outlets with distance', function () {
    Outlet::factory()->create([
        'latitude' => -6.2,
        'longitude' => 106.8,
    ]);

    $response = $this->getJson('/api/outlets/nearby?lat=-6.21&lng=106.81');

    $response->assertOk();
    expect($response->json('0.jarak_km'))->toBeGreaterThan(0);
});

test('outlet nearby requires coordinates', function () {
    $this->getJson('/api/outlets/nearby')
        ->assertStatus(422);
});

test('outlet nearby sorts by closest', function () {
    Outlet::factory()->create([
        'nama' => 'Jauh',
        'latitude' => -7.0,
        'longitude' => 110.0,
    ]);
    Outlet::factory()->create([
        'nama' => 'Dekat',
        'latitude' => -6.2,
        'longitude' => 106.8,
    ]);

    $response = $this->getJson('/api/outlets/nearby?lat=-6.21&lng=106.81');

    expect($response->json('0.nama'))->toBe('Dekat');
    expect($response->json('1.nama'))->toBe('Jauh');
});
