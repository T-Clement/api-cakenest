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
        Schema::create('cupcakes', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("price_in_cents"); // register prices with cents / centimes
            $table->string("photo_url");
            $table->string("description");
            $table->integer("quantity");
            $table->boolean("is_available");
            $table->boolean("is_advertised");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupcakes');
    }
};
