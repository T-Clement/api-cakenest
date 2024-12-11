<?php

use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

test('request return a number of items per page', function () {

    $cupcakesNumber = 100;
    $limitPerPage = 5;

    // create cupcakes
    Cupcake::factory()->count($cupcakesNumber)->create();

    /** @var User */
    $user = User::factory()->create();
    

    // send as logged in user request to cupcake.index with per_page as params
    $response = actingAs($user)->getJson(route('cupcake.index', [
        "per_page" => $limitPerPage
    ]));

    // check the correct number of responses per page
    $response->assertJsonCount($limitPerPage, 'data');

    // check json structure
    $response->assertJsonStructure([
        'data', 
        'meta' => [
            'current_page',
            'last_page',
            'per_page',
            'total',
        ],
    ]);

    // check same cupcakes count in database as we wanted
    expect($response->json('meta.total'))->toEqual($cupcakesNumber);

});



test('unauthenticated user cannot access cupcakes index with pagination', function () {

    $cupcakesNumber = 100;
    // $limitPerPage = 5;

    Cupcake::factory()->count($cupcakesNumber)->create();

    $response = getJson(route('cupcake.index'));

    $response->assertUnauthorized();
});
