<?php

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
        Schema::create('cupcake_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cupcake_id')->constrained();
            $table->foreignId('order_id')->constrained();
            $table->integer("quantity");
            $table->integer("current_cupcake_price_when_order");
            $table->integer("total_price_in_cents");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupcake_order');
    }
};
