<?php

use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

test('not authenticated user cannot view cupcakes', function () {

    // create cupcakes
    // really needed
    $cupcakes = Cupcake::factory()->count(10)->create();

    // anonymous user can not get access to cupcakes
    getJson(route('cupcake.index'))->assertStatus(401);
});



test('authenticated user can view cupcakes', function () {

    // create cupcakes
    $cupcakes = Cupcake::factory()->count(100)->create();
    // dd($cupcakes);


    /** @var User */
    $loggedInUser = User::factory()->create(['is_admin' => true]);


    $response = actingAs($loggedInUser)
        ->getJson(route('cupcake.index', [
            // data if needed 
            // ...
        ]));

    // dd($response);

    $response->assertStatus(200);

    // get cupcakes coming from $response
    // $responseCupcakes = $response->json('data');

    // dd($responseCupcakes);

    expect($response["meta"]["total"])->toEqual(count($cupcakes));


});
