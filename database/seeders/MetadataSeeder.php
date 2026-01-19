<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetadataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Category Metadatas
         */
        $techCategoryMetadata = Category::first()->categoryMetadata()->create([
            'id' => 'tecnologia',
            'name' => 'Tecnologia'
        ]);
        $colorCategoryMetadata = Category::first()->categoryMetadata()->create([
            'id' => 'cor',
            'name' => 'Cor'
        ]);

        /**
         * Value Metadatas
         */
        $techMetadata = $techCategoryMetadata->values()->create([
            'id' => 'react-native',
            'label' => 'React Native'
        ]);
        $techMetadata = $techCategoryMetadata->values()->create([
            'id' => 'react',
            'label' => 'React'
        ]);
        $colorMetadata = $colorCategoryMetadata->values()->create([
            'id' => 'preto',
            'label' => 'Preto'
        ]);
        $colorMetadata = $colorCategoryMetadata->values()->create([
            'id' => 'cinza',
            'label' => 'Cinza'
        ]);

        /**
         * Product Metadatas
         */

        Product::first()->metadata()->create([
            'category_metadata_id' => $colorCategoryMetadata->id,
            'metadata_value_id' =>  $colorMetadata->id
        ]);
    }
}
