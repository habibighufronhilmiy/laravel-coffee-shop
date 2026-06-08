<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'no_telp' => '08111111111',
        'password' => Hash::make('oldpassword'),
    ]);
    $this->actingAs($this->user);
});

test('profile show returns user data', function () {
    $response = $this->getJson('/api/profile');

    $response->assertOk();
    expect($response->json('name'))->toBe('Original Name');
    expect($response->json('email'))->toBe('original@example.com');
    expect($response->json('no_telp'))->toBe('08111111111');
});

test('profile update changes name and email', function () {
    $response = $this->putJson('/api/profile', [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'no_telp' => '08222222222',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'no_telp' => '08222222222',
    ]);
});

test('profile update rejects duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->putJson('/api/profile', [
        'name' => 'Test',
        'email' => 'taken@example.com',
    ])->assertStatus(422);
});

test('profile update password with correct current password', function () {
    $response = $this->putJson('/api/profile/password', [
        'current_password' => 'oldpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertOk();
    expect(Hash::check('newpassword123', $this->user->fresh()->password))->toBeTrue();
});

test('profile update password fails with wrong current password', function () {
    $this->putJson('/api/profile/password', [
        'current_password' => 'wrongpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertStatus(400);
});

test('profile update password fails without confirmation', function () {
    $this->putJson('/api/profile/password', [
        'current_password' => 'oldpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'different',
    ])->assertStatus(422);
});

test('profile requires auth', function () {
    $this->app->get('auth')->forgetGuards();

    $this->getJson('/api/profile')->assertStatus(401);
    $this->putJson('/api/profile', ['name' => 'X'])->assertStatus(401);
    $this->putJson('/api/profile/password', [])->assertStatus(401);
});
