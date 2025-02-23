<?php

use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('can get all products', function () {
    Product::factory(20)->create();

    $response = $this->get('/api/product');

    $response->assertStatus(200);
    $response->assertJsonCount(20);
});

test('can create product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/product', [
        "product_name" => "test product",
        "description" => "testing items",
        "price" => 10000,
        "stock" => 5,
        "category_id" => 1,
        "img_url" => "test.png"
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        "product_name",
        "description",
        "price",
        "stock",
        "category_id",
        "img_url",
        "id"
    ]);
});

test('can delete product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create();

    $response = $this->delete('/api/product/' . $product->id);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        "message"
    ]);
});

test('can update product', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $product = Product::factory()->create([
        "product_name" => "test product",
        "description" => "description"
    ]);

    $response = $this->putJson('/api/product/' . $product->id, [
        "product_name" => "new product",
        "description" => "new description",
        "price" => $product->price,
        "stock" => $product->stock,
        "category_id" => $product->category_id,
        "img_url" => $product->img_url,
    ]);

    $expectedContent = [
        "product_name" => "new product",
        "description" => "new description",
        "price" => $product->price,
        "stock" => $product->stock,
        "category_id" => $product->category_id,
        "img_url" => $product->img_url,
        "id" => $product->id
    ];

    // $response->dump();

    $response->assertStatus(200);
    $response->assertJson($expectedContent);
});

test('can get product by id', function () {
    $product = Product::factory()->create([
        "product_name" => "test product"
    ]);

    $expectedContent = [
        "product_name" => "test product",
        "description" => $product->description,
        "price" => $product->price,
        "stock" => $product->stock,
        "category_id" => $product->category_id,
        "img_url" => $product->img_url,
        "id" => $product->id
    ];

    $response = $this->get('/api/product/' . $product->id);

    $response->assertStatus(200);
    $response->assertJson($expectedContent);
});