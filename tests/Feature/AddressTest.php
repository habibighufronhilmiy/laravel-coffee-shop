<?php

use App\Models\Address;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('address index returns empty array for new user', function () {
    $this->getJson('/api/addresses')
        ->assertOk()
        ->assertJson([]);
});

test('address store creates new address', function () {
    $response = $this->postJson('/api/addresses', [
        'label' => 'Rumah',
        'alamat' => 'Jl. Kopi Susu No. 10, Jakarta',
        'penerima' => 'Budi',
        'no_telp_penerima' => '08123456789',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('addresses', [
        'user_id' => $this->user->id,
        'label' => 'Rumah',
        'alamat' => 'Jl. Kopi Susu No. 10, Jakarta',
    ]);
});

test('address store sets first address as default', function () {
    $this->postJson('/api/addresses', [
        'label' => 'Rumah',
        'alamat' => 'Jl. A',
    ]);

    $this->assertDatabaseHas('addresses', [
        'user_id' => $this->user->id,
        'is_default' => true,
    ]);
});

test('address index returns user addresses', function () {
    Address::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/addresses');

    $response->assertOk()
        ->assertJsonCount(3);
});

test('address index only shows own addresses', function () {
    Address::factory()->create(['user_id' => $this->user->id]);
    Address::factory()->create(); // another user

    $response = $this->getJson('/api/addresses');

    $response->assertOk()
        ->assertJsonCount(1);
});

test('address update changes fields', function () {
    $address = Address::factory()->create([
        'user_id' => $this->user->id,
        'label' => 'Old Label',
    ]);

    $response = $this->putJson('/api/addresses/' . $address->id, [
        'label' => 'New Label',
        'alamat' => 'Jl. Baru No. 5',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('addresses', [
        'id' => $address->id,
        'label' => 'New Label',
    ]);
});

test('address update cannot edit other user address', function () {
    $otherAddress = Address::factory()->create();

    $this->putJson('/api/addresses/' . $otherAddress->id, [
        'label' => 'Hacked',
        'alamat' => 'Hacked',
    ])->assertStatus(403);
});

test('address destroy removes address', function () {
    $address = Address::factory()->create(['user_id' => $this->user->id]);

    $this->deleteJson('/api/addresses/' . $address->id)
        ->assertOk();

    $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
});

test('address destroy cannot delete other user address', function () {
    $otherAddress = Address::factory()->create();

    $this->deleteJson('/api/addresses/' . $otherAddress->id)
        ->assertStatus(403);
});

test('address store requires alamat field', function () {
    $this->postJson('/api/addresses', ['label' => 'Rumah'])
        ->assertStatus(422);
});
