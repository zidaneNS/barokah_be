<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('payment exist when order created', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $response = $this->postJson('/api/neworder', [
        "product_id" => $product->id,
        "quantity" => 5
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('payments', [
        "status" => "pending"
    ]);
});

test('payment deleted when order deleted', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $order = Order::factory()->create([
        "user_id" => $user->id
    ]);

    $cart = Cart::factory()->create([
        "order_id" => $order->id
    ]);

    $cart->products()->attach($product, [
        "quantity" => 3,
        "price" => $product->price
    ]);

    Payment::factory()->create([
        "order_id" => $order->id
    ]);

    $response = $this->deleteJson('/api/order', [
        "order_id" => $order->id
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('payments', [
        "order_id" => $order->id
    ]);
});

test('can update payment status', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $order = Order::factory()->create([
        "user_id" => $user->id
    ]);

    $cart = Cart::factory()->create([
        "order_id" => $order->id
    ]);

    $cart->products()->attach($product, [
        "quantity" => 3,
        "price" => $product->price
    ]);

    Payment::factory()->create([
        "order_id" => $order->id
    ]);

    $response = $this->putJson('/api/payment', [
        "order_id" => $order->id,
        "status" => "success"
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('payments', [
        "id" => $order->payment->id,
        "status" => "success"
    ]);
});