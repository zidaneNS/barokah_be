<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('can login', function () {
    User::factory()->create([
        'name' => 'test',
        'email' => 'test@example.com',
        'password' => 'password'
    ]);

    $response = $this->postJson('/api/login', ['email' => 'test@example.com', 'password' => 'password']);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});

test('can register as customer', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'test',
        'email' => 'test@example.com',
        'password' => 'password',
        'role' => 'CUSTOMER'
    ]);

    $response->assertStatus(201);
});

test('can register as admin', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'test',
        'email' => 'test@example.com',
        'password' => 'password',
        'role' => 'ADMIN'
    ]);

    $response->assertStatus(201);
});

test('can logout', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->get('/api/logout');

    // $response->dump();
    $response->assertStatus(200);
});
