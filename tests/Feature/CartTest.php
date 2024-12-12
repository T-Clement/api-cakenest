<?php

// créer un cart si pas déjà de cart existant
// récupérer les données d'un cart existant
// mettre à jour le contenu d'un cart
// valider un cart et le passer en commande / order

use App\Models\Cart;
use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;



// we get the current cart of the user or a new cart 
// after he logged-in the app
test('a logged-in user can create a cart', function () {

    // create User
    /** @var User */
    $user = User::factory()->create();

    // dd($user);

    // create Cart
    $cart = Cart::factory()->make(["user_id" => $user->id]);
    $response = actingAs($user)->postJson(route(
        "cart.store",
        $cart->toArray()
    ));

    $response->assertCreated();
});



test('an anonymous user cannot create a cart', function () {


    // create Cart
    $cart = Cart::factory()->make(["user_id" => rand(1, 10)]);
    $response = postJson(route(
        "cart.store",
        $cart->toArray()
    ));

    $response->assertUnauthorized();
});



test('a logged-in user can get his cart his previous / current cart', function () {

    // create User
    /** @var User */
    $user = User::factory()->create();


    // create cupcakes
    $cupcakes = Cupcake::factory()->count(10)->create(["quantity" => 10]);


    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);


    // add cupcakes to cart
    $cart->cupcakes()->attach([
        $cupcakes[0]->id => ['quantity' => 2],
        $cupcakes[1]->id => ['quantity' => 3],
    ]);



    $cartFromDatabase = actingAs($user)->getJson(route(
        "cart.show",
        ["id" => $user->id]
    ));

    $cartFromDatabase->assertStatus(200);


    $cartFromDatabase->assertJson([
        'id' => $cart->id,
        'user_id' => $user->id,
        'cupcakes' => [
            [
                'id' => $cupcakes[0]->id,
                'pivot' => [
                    'quantity' => 2,
                ],
            ],
            [
                'id' => $cupcakes[1]->id,
                'pivot' => [
                    'quantity' => 3,
                ],
            ],
        ],
    ]);
});



test("a logged-in user cannot get the cart of an another user", function () {


    $users = User::factory()->count(2)->create();

    $cart = Cart::factory()->create(["user_id" => $users[0]->id]);

    // acting as second user to access to first user cart
    $response = actingAs($users[1])->getJson(route("cart.show", ["id" => $users[0]->id]));

    $response->assertForbidden();
});






test('an anonymous user can not get a cart', function () {

    // create cupcakes
    $cupcakes = Cupcake::factory()->count(10)->create(["quantity" => 10]);


    $user = User::factory()->create();

    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);


    // add cupcakes to cart
    $cart->cupcakes()->attach([
        $cupcakes[0]->id => ['quantity' => 2],
        $cupcakes[1]->id => ['quantity' => 3],
    ]);



    $response = getJson(route(
        "cart.show",
        ["id" => $user->id]
    ));

    $response->assertUnauthorized();
});





// add cupcake (quantity > 0), delete (quantity = 0), 
// no presence of old cupcake in request remove him from the cart
// update the same cupcake by increasing or descreasing the quantity of one cupcake
// 
test("a user can add a cupcake to an non empty cart", function () {

    // create cupcakes
    $cupcakes = Cupcake::factory()->count(10)->create(["quantity" => 10]);

    // create User
    /** @var User */
    $user = User::factory()->create();

    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);
    $cart->cupcakes()->attach([
        $cupcakes[1]->id => ['quantity' => 5],
        $cupcakes[4]->id => ['quantity' => 7]
    ]);

    $selectedCupcake = $cupcakes[0]->toArray();

    // decrementation of quantity is handle in order.store
    $updatedCart = actingAs($user)->patchJson(
        route("cart.update", ["id" => $user->id]),
        [
            "cupcakes" => [
                [
                    "cupcake_id" => $selectedCupcake['id'],
                    "quantity" => 9
                ],

            ]
        ]
    );

    $updatedCart->assertStatus(200);

    // assert some other things to validate json


    // dd($response->json());
    // $response->dump();

    // $response->assert


});



test("a user can update an empty cart by adding one cupcake", function () {

    // create cupcakes
    $cupcake = Cupcake::factory()->create(["quantity" => 10]);

    // create User
    /** @var User */
    $user = User::factory()->create();


    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);

    $response = actingAs($user)->patchJson(
        route("cart.update", ["id" => $user->id]),
        [
            "cupcakes" => [
                [
                    "cupcake_id" => $cupcake->id,
                    "quantity" => 9
                ],

            ]
        ]
    );


    $response->assertStatus(200);

    $updatedCart = $response->json();

    // count that there is one cupcake in cupcakes and it's the cupcake id passed in request body and with the correct quantity
    expect($updatedCart["cupcakes"])->toHaveCount(1)
        ->and($updatedCart["cupcakes"][0]['id'])->toBe($cupcake->id)
        ->and($updatedCart['cupcakes'][0]['pivot']['quantity'])->toBe(9);



});



test("a user can update the quantity of a cupcake already in cart (increasing)", function () {

    // create cupcakes
    $cupcake = Cupcake::factory()->create(["quantity" => 10]);

    // create User
    /** @var User */
    $user = User::factory()->create();


    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);

    // add cupcake to cart
    $cart->cupcakes()->attach([
        $cupcake->id => ['quantity' => 5],
    ]);



    $response = actingAs($user)->patchJson(
        route("cart.update", ["id" => $user->id]),
        [
            "cupcakes" => [
                [
                    "cupcake_id" => $cupcake->id,
                    "quantity" => 9
                ],

            ]
        ]
    );

    $response->assertStatus(200);


    $updatedCart = $response->json();
    expect($updatedCart["cupcakes"][0]['id'])->toBe($cupcake->id)
        ->and($updatedCart['cupcakes'][0]['pivot']['quantity'])->toBe(9);





});


test("a user can update the quantity of a cupcake already in cart (decreasing)", function () {

    // create cupcakes
    $cupcake = Cupcake::factory()->create(["quantity" => 10]);

    // create User
    /** @var User */
    $user = User::factory()->create();


    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);


    // add cupcake to cart
    $cart->cupcakes()->attach([
        $cupcake->id => ['quantity' => 10],
    ]);



    $response = actingAs($user)->patchJson(
        route("cart.update", ["id" => $user->id]),
        [
            "cupcakes" => [
                [
                    "cupcake_id" => $cupcake->id,
                    "quantity" => 9
                ],

            ]
        ]
    );

    $response->assertStatus(200);


    $updatedCart = $response->json();
    expect($updatedCart["cupcakes"][0]['id'])->toBe($cupcake->id)
        ->and($updatedCart['cupcakes'][0]['pivot']['quantity'])->toBe(9);


});


test("a user can delete a cupcake in a cart", function () {
    // create cupcakes
    $cupcake = Cupcake::factory()->create(["quantity" => 10]);

    // create User
    /** @var User */
    $user = User::factory()->create();


    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);


    // add cupcake to cart
    $cart->cupcakes()->attach([
        $cupcake->id => ['quantity' => 10],
    ]);



    $response = actingAs($user)->patchJson(
        route("cart.update", ["id" => $user->id]),
        [
            "cupcakes" => [
                [
                    "cupcake_id" => $cupcake->id,
                    "quantity" => 0
                ],

            ]
        ]
    );

    $response->assertStatus(200);


    $updatedCart = $response->json();
    // no more cupcakes in cart after the cart is empty
    expect(count($updatedCart["cupcakes"]))->toEqual(0);

});

test("a user can not add / update a cupcake who has enough stock in cart", function () {
    /** @var User */
    $user = User::factory()->create();


    $cupcake = Cupcake::factory()->create(["quantity" => 5]);


    $cart = Cart::factory()->create(["user_id" => $user->id]);

    $response = actingAs($user)->patchJson(
        route('cart.update', ["id" => $user->id]),
        // data
        [
            "cupcakes" => [
                [
                    "cupcake_id" => $cupcake->id,
                    "quantity" => 10
                ]
            ]
        ]
    );


    $response->assertStatus(400);

    // $response->dump();


});




test("a user cannot add to the cart a non existing cupcake", function () {

    /** @var User */
    $user = User::factory()->create();
    
    $cart = Cart::factory()->create(["user_id" => $user->id]);


    $response = actingAs($user)->patchJson(
        route('cart.update', ["id" => $user->id]),
        // data
        [
            "cupcakes" => [
                [
                    "cupcake_id" => 5,
                    "quantity" => 10
                ]
            ]
        ]
    );


    $response->assertStatus(422);

    // dd($response);


});



test("a user cannot update the cart related to someone else", function (

) {

    // create User
    $user = User::factory()->create();

    // created



});



test("admin user can update the cart of a user", function () {

});
