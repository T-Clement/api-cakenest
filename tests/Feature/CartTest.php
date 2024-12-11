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





// add, delete
test("a user can update his cart by adding a cupcake", function () {

    // create cupcakes
    $cupcakes = Cupcake::factory()->count(10)->create(["quantity" => 10]);

    // create User
    /** @var User */
    $user = User::factory()->create();

    // create Cart
    $cart = Cart::factory()->create(["user_id" => $user->id]);
    // dd($cart);

    $selectedCupcake = $cupcakes[0]->toArray();

    // dd($selectedCupcake);



    // decrementation of quantity is handle in order.store


    $response = actingAs($user)->patchJson(
        route("cart.update", ["id" => $user->id]),
        [
            "cupcakes" => [
                [
                    "cupcake_id" => $selectedCupcake['id'],
                    "quantity" => 2
                ]
            ]
        ]
    );

    $response->dump();

    // dd($response);
    // $response->assert


});
