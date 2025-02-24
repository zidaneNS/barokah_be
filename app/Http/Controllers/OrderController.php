<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OrderController extends Controller implements HasMiddleware
{
    public static function middleware() {
        return [
            new Middleware("auth:sanctum")
        ];
    }

    public function index(Request $request) {
        $orders = $request->user()->orders;

        return response($orders);
    }

    public function cart(Request $request)
    {
        $validatedFields = $request->validate([
            "order_id" => "required"
        ]);

        $order = Order::find($validatedFields["order_id"]);

        $carts = $order->carts;

        return response($carts);
    }

    public function newOrder(Request $request)
    {
        $validatedFields = $request->validate([
            "product_id" => "required",
            "quantity" => "required"
        ]);

        $product = Product::find($validatedFields["product_id"]);

        if ($product->stock < $validatedFields["quantity"]) {
            return response(["message" => "stock not enough"], 422);
        }

        $order = Order::factory()->create([
            "user_id" => $request->user()->id
        ]);

        $cart = Cart::factory()->create([
            "order_id" => $order->id
        ]);

        $cart->products()->attach($product, [
            "quantity" => $validatedFields["quantity"],
            "price" => $product->price
        ]);

        $totalPrice = 0;

        foreach ($order->carts as $carts) {
            foreach ($carts->products as $products) {
                $totalPrice += $products->price * $products->pivot->quantity;
            }
        }

        $product->update([
            "stock" => $product->stock - $validatedFields["quantity"]
        ]);

        $order->update([
            "total_price" => $totalPrice
        ]);

        return response(["message" => "success"], 201);
    }

    public function order(Request $request)
    {
        $validatedFields = $request->validate([
            "product_id" => "required",
            "quantity" => "required",
            "order_id" => "required"
        ]);

        $order = Order::find($validatedFields["order_id"]);

        $product = Product::find($validatedFields["product_id"]);

        if ($product->stock < $validatedFields["quantity"]) {
            return response(["message" => "stock not enough"], 422);
        }

        $cart = Cart::factory()->create([
            "order_id" => $order->id
        ]);

        $cart->products()->attach($product, [
            "quantity" => $validatedFields["quantity"],
            "price" => $product->price
        ]);

        $totalPrice = 0;

        foreach ($order->carts as $carts) {
            foreach ($carts->products as $products) {
                $totalPrice += $products->price * $products->pivot->quantity;
            }
        }

        $product->update([
            "stock" => $product->stock - $validatedFields["quantity"]
        ]);

        $order->update([
            "total_price" => $totalPrice
        ]);

        return response(["message" => "success"], 201);
    }

    public function update(Request $request)
    {
        $validatedFields = $request->validate([
            "product_id" => "required",
            "cart_id" => "required",
            "quantity" => "required"
        ]);

        $cart = Cart::find($validatedFields["cart_id"]);
        
        $cart->products()->updateExistingPivot($validatedFields["product_id"], [
            "quantity" => $validatedFields["quantity"]
        ]);

        $totalPrice = 0;

        $order = Order::find($cart->order->id);

        foreach ($order->carts as $carts) {
            foreach ($carts->products as $products) {
                // dd($products->pivot->quantity);
                $totalPrice += $products->price * $products->pivot->quantity;
            }
        }

        $order->update([
            "total_price" => $totalPrice
        ]);

        return response(["message" => "success"], 200);
    }

    public function destroy(Request $request)
    {
        $validatedFields = $request->validate([
            "order_id" => "required"
        ]);

        $order = Order::find($validatedFields["order_id"]);

        $order->delete();

        return response(["message" => "success"], 200);
    }

    public function deleteProduct(Request $request)
    {
        $validatedFields = $request->validate([
            "product_id" => "required",
            "cart_id" => "required"
        ]);

        $cart = Cart::find($validatedFields["cart_id"]);

        $order = Order::find($cart->order->id);

        $product = $cart->products()->find($validatedFields["product_id"]);
        
        $order->update([
            "total_price" => $order->total_price - $product->price * $product->pivot->quantity
        ]);
        $cart->products()->detach($validatedFields["product_id"]);

        return response(["message" => "success"], 200);
    }
}
