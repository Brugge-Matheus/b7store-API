<?php

use App\Enums\OrderStatusEnum;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(OrderStatusEnum::PENDING->value);
            $table->integer('total');
            $table->string('shippingCoast');
            $table->string('shippingDays');
            $table->string('shippingZipcode');
            $table->string('shippingStreet');
            $table->string('shippingNumber');
            $table->string('shippingCity');
            $table->string('shippingState');
            $table->string('shippingCountry');
            $table->string('shippingComplement');
            $table->foreignIdFor(User::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');  
    }
};
