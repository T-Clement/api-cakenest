<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cupcake>
 */
class CupcakeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->word(),
            "price_in_cents" => fake()->numberBetween(100, 10000),
            'photo_url' => fake()->imageUrl(640, 480, 'cupcake', true),
            'description' => fake()->paragraph(),
            'quantity' => fake()->numberBetween(10, 50),
            'is_available' => true,
            'is_advertised' => false 
        ];  
    }
}
