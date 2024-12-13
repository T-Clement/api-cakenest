<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscountCode>
 */
class DiscountCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'begin_at' => now()
        ];
    }



    public function percentage($value)
    {
        return $this->state([
            'discount_type' => 'percentage',
            'discount_value' => $value,
            'is_active' => true,
        ]);
    }


    public function expired() {



        return $this->state([
            "begin_at" => Carbon::yesterday(),
            "expires_at" => Carbon::now()->subHour()
        ]);
    }
}
