<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('can connect order with products', function () {

    $user = User::factory()->create();
    $order = $user->orders()->create([
        'total_price' => 0,
        'status' => 'pending'
    ]);
 
    $products = Product::factory(2)->create();
    
    $cart = Cart::factory()->create([
        'order_id' => $order->id
    ]);

    $cart->products()->attach($products[0], [
        'quantity' => 2,
        'price' => $products[0]->price
    ]);

    $cart->products()->attach($products[1], [
        'quantity' => 3,
        'price' => $products[1]->price
    ]);

    expect($cart->products()->count())->toBe(2);

    $foundOrder = Order::find($order->id);
    $foundCart = Cart::find($foundOrder->carts[0]->id);

    expect($foundCart->id)->toEqual($cart->id);
});