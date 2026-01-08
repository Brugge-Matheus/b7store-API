<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::take(2)->get();
        $first = $products->first();
        $second = $products->get(1);

        $first->images()->create([
            'url' => 'products/product_1_1.jpg'
        ]);
        $second->images()->create([
            'url' => 'products/product_1_2.jpg'
        ]);

    }
}
