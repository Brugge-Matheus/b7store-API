<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'label' => 'Produto de exemplo',
            'description' => 'Descrição de exemplo',
            'price' => 10000,
            'category_id' => Category::inRandomOrder()->value('id')
        ]);

        Product::create([
            'label' => 'Produto de exemplo 2',
            'description' => 'Descrição de exemplo 2',
            'price' => 100000,
            'category_id' => Category::inRandomOrder()->value('id')
        ]);
    }
}
