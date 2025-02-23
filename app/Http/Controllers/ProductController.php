<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware() {
        return [
            new Middleware("auth:sanctum", except: ["index", "show"])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->get();

        // dd($products);

        return response($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedFields = $request->validate([
            "product_name" => "required",
            "description" => "required",
            "price" => "required",
            "stock" => "required",
            "category_id" => "required",
            "img_url" => "required"
        ]);

        $product = Product::factory()->create($validatedFields);

        return response($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->validate([
            "product_name" => "required",
            "description" => "required",
            "price" => "required",
            "stock" => "required",
            "category_id" => "required",
            "img_url" => "required"
        ]));

        // dd($product);
        return response($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response(["message" => "success deleting product"]);
    }
}
