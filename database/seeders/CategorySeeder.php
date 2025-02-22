<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->create([
            'category' => 'STAPLE_FOODS'
        ]);
        Category::factory()->create([
            'category' => 'INSTANT_PROCESSED'
        ]);
        Category::factory()->create([
            'category' => 'BEVERAGES_COFFEE'
        ]);
        Category::factory()->create([
            'category' => 'SPICES_HERBS'
        ]);
        Category::factory()->create([
            'category' => 'FRESH_FOODS'
        ]);
        Category::factory()->create([
            'category' => 'HOUSEHOLD_CLEANING'
        ]);

        // ['STAPLE_FOODS', 'INSTANT_PROCESSED', 'BEVERAGES_COFFE', 'SPICES_HERBS', 'FRESH_FOODS', 'HOUSEHOLD_CLEANING']);
    }
}
