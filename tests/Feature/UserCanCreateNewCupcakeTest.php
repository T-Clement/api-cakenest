<?php

use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('An admin user can store a new cupcake', function () {

    // create an admin user
    /** @var User */
    $adminUser = User::factory()->create(["is_admin" => true]);

    // create cupcake
    $cupcake = Cupcake::factory()->make();
    // dd($cupcake);

    // make cupcake store request as admin user
    actingAs($adminUser)
        ->postJson(route('cupcake.store', $cupcake->toArray()))
        ->assertCreated()
        ->assertJsonFragment([
            "name" => $cupcake["name"],
            "price_in_cents" => (string)$cupcake["price_in_cents"],
            "quantity" => (string)$cupcake["quantity"]
        ]);
});



test('An logged in user can not store a new cupcake', function () {

    // create a non admin user
    /** @var User */
    $loggedInUser = User::factory()->create(["is_admin" => false]);

    // create a cupcake with factory and store it in variable
    $cupcake = Cupcake::factory()->make();

    // make cupcake store request as non admin user
    actingAs($loggedInUser)
        ->postJson(route(
            'cupcake.store',
            $cupcake->toArray() // need an array so transform model instance to an array
        ))
        ->assertForbidden();
});



test('An anonymous user can not store a new cupcake', function () {
    // make cupcake store request as anonymous user

    // create cupcake
    $cupcake = Cupcake::factory()->make();

    // return unauthorized to cupcake store attempt
    $response = postJson(route('cupcake.store', $cupcake->toArray()));
    $response->assertUnauthorized();
});




// test for not corrected values 