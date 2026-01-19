<?php

use App\Models\CategoryMetadata;
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
        Schema::create('metadata_values', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('label');
            $table->string('category_metadata_id');
            $table->timestamps();

            $table->foreign('category_metadata_id')
            ->references('id')
            ->on('category_metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadata_values');
    }
};
