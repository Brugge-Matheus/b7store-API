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
            'label' => 'Camiseta React native',
            'description' => 'Descrição de exemplo',
            'price' => 129,
            'category_id' => Category::inRandomOrder()->value('id')
        ]);

        Product::create([
            'label' => 'Camiseta React',
            'description' => 'Descrição de exemplo 2',
            'price' => 89,
            'category_id' => Category::inRandomOrder()->value('id')
        ]);

        Product::create([
            'label' => 'Camiseta Laravel',
            'description' => 'Descrição de exemplo 3',
            'price' => 200,
            'category_id' => Category::inRandomOrder()->value('id')
        ]);
    }
}
