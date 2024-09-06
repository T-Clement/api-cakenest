<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Cupcake;
use App\Models\Order;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $adminUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => true
        ]);
        
        $users = User::factory(10)->create();
       

        $categories = [
            ["name" => "Sucré", "created_at" => now()], 
            ["name" => "Salé", "created_at" => now()->startOfMonth()], 
            ["name" => "Gluten-Free", "created_at" => now()]
        ];
        
        Category::insert($categories);

        $categories = Category::all();
        
        $cupcakes = Cupcake::factory()->count(10)->create();


        // pour chaque cupcake, associer de manière random les catégories 1, 2 et 3 
        // un cupcake peut avoir plusieurs catégories comme 1 seule, mais pas de doublon


        foreach($cupcakes as $key => $cupcake) {
            $cupcake->categories()->attach($categories->random(mt_rand(1, $categories->count()))->pluck('id'));

        }



        // seed orders
        /** @var Order[] */
        $orders = Order::factory(5)->recycle($users)->create();  // create but not saved immediately

        foreach ($orders as $order) {
            // assign each order to a random user
            //$randomUser = $users->random();

            // save the order and associate the user
            //$order->user_id = $randomUser->id;
            //$order->save();

            // ensure each order has its own set of cupcakes
            $randomCupcakes = $cupcakes->random(mt_rand(1, 5)); // random selection of cupcakes for this order

            foreach ($randomCupcakes as $cupcake) {
                // random quantity between 1 and 5
                $quantity = mt_rand(1, 5);
                $totalPrice = $cupcake->price_in_cents * $quantity; // calculate the total price for this cupcake

                // insert into the pivot table `order_cupcake`
                $order->cupcakes()->attach($cupcake->id, ['quantity' => $quantity, 'total_price_in_cents' => $totalPrice, 'current_cupcake_price_when_order' => $cupcake->price_in_cents]);

                // DB::table('cupcake_order')->insert([
                //     'order_id' => $order->id, // ensure the correct order is being referenced
                //     'cupcake_id' => $cupcake->id,
                //     'quantity' => $quantity,
                //     'total_price_in_cents' => $totalPrice, // store the total price
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ]);
            }
        }


    }
}
