<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_name' => fake()->word(),
            'description' => fake()->text(),
            'price' => fake()->randomElement([10000, 20000, 30000]),
            'stock' => fake()->randomElement([15, 20, 80, 100]),
            'img_url' => fake()->imageUrl(),
            'category_id' => fake()->randomElement([1,2,3,4,5,6])
        ];
    }
}
