<?php

// STORE


// order with discount code as percentage

// order with discount code as fix amount

// order with expired discount code

// order with inactive discount code

// order with an invalid / not existing discount code




// SHOW 

// show order with discount code applied

// show order without discount code

// show order of another user (admin)

// show order of another user (non-admin)

// 


// discount code

// discount cannot make a negative or equals to 0 total

// admin only can create a discount

// 



// order without discount code

use App\Models\Cart;
use App\Models\Cupcake;
use App\Models\Order;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('example', function () {
    
    
    
});


// an anonymous user can not make an order
test('an anonymous user can not make an order', function () {
    
    // create a user
    $customer = User::factory()->create();


    // cupcake
    $cupcakes = Cupcake::factory()->count(2)->create([
        "quantity" => 10
    ]);

    // dd($cupcake);

    $firstCupcake = $cupcakes[0];
    $secondCupcake = $cupcakes[1];


    $cart = Cart::factory()->create(["user_id" => $customer->id]);

    $cupcakeQuantityOrdered = 3;


    // add cupcake to cart
    $cart->cupcakes()->attach([
        $firstCupcake->id => ["quantity" => $cupcakeQuantityOrdered],
        $secondCupcake->id => ["quantity" => $cupcakeQuantityOrdered]
    ]);

    // dd($cart->with("cupcakes")->get()->toArray());


    $cartId = $cart->id;

    $response = postJson(
        route('order.store', ['id' => $customer->id]),
        [
            "user_id" => 2,
            "cart_id" => $cartId,
            "cupcakes" => [
                [
                    "cupcake_id" => $firstCupcake->id,
                    "quantity" => $cupcakeQuantityOrdered
                ], 
                [
                    "cupcake_id" => $secondCupcake->id,
                    "quantity" => $cupcakeQuantityOrdered
                ]
            ]
        ]
    );
    // handled in middleware
    $response->assertStatus(401);
     
    // dd($cartId);


    // add pivot data to pivot table
        // add cupcake to order with a specific quantity of one cupcake

        // attach can take an array of IDs
    // $cart->cupcakes()->attach($cupcake->id, [
    //     'quantity' => $cupcakeQuantityOrdered,
    //     'total_price_in_cents' => $cupcake->price_in_cents * $cupcakeQuantityOrdered, // calculate total_price with price from database and
    //     'current_cupcake_price_when_order' => $cupcake->price_in_cents 
    // ]);

    // // dd($order->cupcakes()->user()->get());

    // dd(Order::with(["cupcakes", "user"])->get()->toArray());






});



test("a customer can create an order without discount code", function() {
    // create a user
    /** @var User */
    $customer = User::factory()->create();


    // cupcake
    $cupcakes = Cupcake::factory()->count(2)->create([
        "quantity" => 10
    ]);


    $firstCupcake = $cupcakes[0];
    $secondCupcake = $cupcakes[1];


    $cart = Cart::factory()->create(["user_id" => $customer->id]);

    $cupcakeQuantityOrdered = 3;


    // add cupcake to cart
    $cart->cupcakes()->attach([
        $firstCupcake->id => ["quantity" => $cupcakeQuantityOrdered],
        $secondCupcake->id => ["quantity" => $cupcakeQuantityOrdered]
    ]);

    // dd($cart->with("cupcakes")->get()->toArray());


    $cartId = $cart->id;

    $response = actingAs($customer)->postJson(
        route('order.store', ['id' => $customer->id]),
        [
            "user_id" => $customer->id,
            "cart_id" => $cartId,
            "cupcakes" => [
                [
                    "cupcake_id" => $firstCupcake->id,
                    "quantity" => $cupcakeQuantityOrdered
                ], 
                [
                    "cupcake_id" => $secondCupcake->id,
                    "quantity" => $cupcakeQuantityOrdered
                ]
            ]
        ]
    );

    // dd($response->json());
    $response->assertStatus(201);


});




test("", function() {

    


});
