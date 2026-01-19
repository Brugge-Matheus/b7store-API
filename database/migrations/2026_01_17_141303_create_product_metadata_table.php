<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('category_metadata_id');    
            $table->string('metadata_value_id');    
            $table->foreignIdFor(Product::class)->constrained();    

            $table->foreign('category_metadata_id')->references('id')->on('category_metadata');
            $table->foreign('metadata_value_id')->references('id')->on('metadata_values');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_metadata');
    }
};
