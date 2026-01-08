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
        Category::create([
            'name' => 'Categoria de exemplo',
            'slug' => 'categoria-de-exemplo'
        ]);

        Category::create([
            'name' => 'Categoria de exemplo 2',
            'slug' => 'categoria-de-exemplo-2'
        ]);
    }
}
