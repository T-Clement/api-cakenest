<?php

namespace Database\Seeders;

use App\Models\Cupcake;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Cupcake::factory()->count(10)->create();


        // category sucré / salé / gluten-free

        //ingredients

        //commandes

    }
}
