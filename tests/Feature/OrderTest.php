<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('can attach cart to user new order', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $response = $this->postJson('/api/neworder', [
        "product_id" => $product->id,
        "quantity" => 5
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('cart_product', [
        "quantity" => 5,
        "product_id" => $product->id
    ]);
    $this->assertDatabaseHas('orders', [
        "total_price" => $product->price * 5
    ]);
    $this->assertDatabaseHas('products', [
        "id" => $product->id,
        "stock" => $product->stock - 5
    ]);
});

test('failed to attach when quantity greater than product stock with new order', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        "stock" => 2
    ]);

    $response = $this->postJson('/api/neworder', [
        "product_id" => $product->id,
        "quantity" => 5
    ]);

    $response->assertStatus(422);
});

test('can attach cart to user existing order', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $order = Order::factory()->create([
        "user_id" => $user->id
    ]);

    $response = $this->postJson('/api/order', [
        "product_id" => $product->id,
        "quantity" => 3,
        "order_id" => $order->id
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('cart_product', [
        "quantity" => 3,
        "product_id" => $product->id
    ]);
    $this->assertDatabaseHas('carts', [
        "order_id" => $order->id
    ]);
    $this->assertDatabaseHas('orders', [
        "total_price" => $product->price * 3 + $order->total_price
    ]);
    $this->assertDatabaseHas('products', [
        "id" => $product->id,
        "stock" => $product->stock - 3
    ]);
});

test('failed to attach when quantity greater than product stock with existing order', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        "stock" => 0
    ]);

    $order = Order::factory()->create([
        "user_id" => $user->id
    ]);

    $response = $this->postJson('/api/order', [
        "product_id" => $product->id,
        "quantity" => 3,
        "order_id" => $order->id
    ]);

    $response->assertStatus(422);
});

test('can update quantity', function () {
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

    $response = $this->putJson('/api/order', [
        "product_id" => $product->id,
        "cart_id" => $cart->id,
        "quantity" => 5
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('cart_product', [
        "cart_id" => $cart->id,
        "quantity" => 5
    ]);
    $this->assertDatabaseHas('orders', [
        "total_price" => $product->price * 5 + $order->total_price
    ]);
});

test('can delete order', function () {
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

    $response = $this->deleteJson('/api/order', [
        "order_id" => $order->id
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('orders', [
        "id" => $order->id
    ]);
    $this->assertDatabaseMissing('carts', [
        "order_id" => $order->id
    ]);
    $this->assertDatabaseMissing('cart_product', [
        "cart_id" => $cart->id,
        "product_id" => $product->id
    ]); 
});

test('can delete product from cart', function () {
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

    $order->update([
        "total_price" => $product->price * 3 + $order->total_price
    ]);

    $response = $this->postJson('api/order/delete', [
        "cart_id" => $cart->id,
        "product_id" => $product->id
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('cart_product', [
        "product_id" => $product->id
    ]);
    $this->assertDatabaseHas('orders', [
        "total_price" =>  $order->total_price - $product->price * 3
    ]);
});

test('can get all orders', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Order::factory()->create([
        "user_id" => $user->id
    ]);

    $response = $this->get('api/order');

    $response->assertStatus(200);

    $response->assertJsonCount(1);
});

test('can get all carts', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $products = Product::factory(5)->create();

    $order = Order::factory()->create([
        "user_id" => $user->id
    ]);

    foreach ($products as $product) {
        $cart = Cart::factory()->create([
            "order_id" => $order->id
        ]);

        $cart->products()->attach($product, [
            "quantity" => rand(1,5),
            "price" => $product->price
        ]);
    }

    $response = $this->postJson('/api/cart', [
        "order_id" => $order->id
    ]);

    $response->assertStatus(200);
    $response->assertJsonCount(5);
});