<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'no_telp' => '08123456789',
    ];
});

test('register creates user and returns token', function () {
    $response = $this->postJson('/api/auth/register', $this->userData);

    $response->assertCreated()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'no_telp', 'role']]);
    $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'customer']);
});

test('register requires valid email', function () {
    $this->userData['email'] = 'invalid';
    $this->postJson('/api/auth/register', $this->userData)
        ->assertStatus(422);
});

test('register requires password confirmation', function () {
    unset($this->userData['password_confirmation']);
    $this->postJson('/api/auth/register', $this->userData)
        ->assertStatus(422);
});

test('login returns token for valid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
});

test('login fails for invalid credentials', function () {
    $this->postJson('/api/auth/login', [
        'email' => 'wrong@example.com',
        'password' => 'wrongpassword',
    ])->assertStatus(422);
});

test('login requires email and password', function () {
    $this->postJson('/api/auth/login', [])
        ->assertStatus(422);
});

test('authenticated user can access profile', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson('/api/auth/user');

    $response->assertOk()
        ->assertJson(['id' => $user->id, 'email' => $user->email]);
});

test('guest cannot access profile', function () {
    $this->getJson('/api/auth/user')->assertStatus(401);
});

test('logout revokes tokens', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/auth/logout');

    $response->assertOk();
    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test('register cannot create admin role', function () {
    $this->userData['role'] = 'admin';
    $this->postJson('/api/auth/register', $this->userData);

    $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'customer']);
});

test('register duplicate email fails', function () {
    User::factory()->create(['email' => 'test@example.com']);
    $this->postJson('/api/auth/register', $this->userData)
        ->assertStatus(422);
});
