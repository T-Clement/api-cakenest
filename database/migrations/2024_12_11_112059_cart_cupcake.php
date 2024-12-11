<?php

use App\Models\Cart;
use App\Models\Cupcake;
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
        Schema::create('cart_cupcake', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class)->constrained();
            $table->foreignIdFor(Cupcake::class)->constrained();
            $table->integer("quantity");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_cupcake');
    }
};
